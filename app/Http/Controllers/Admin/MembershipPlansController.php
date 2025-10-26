<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MembershipPlan;
use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MembershipPlansController extends Controller
{
    /**
     * Mostrar listado de planes de membresía.
     */
    public function index(Request $request)
    {
        $query = MembershipPlan::query();

        // Búsqueda por nombre
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('plan_name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Filtro por rango de precio
        if ($request->filled('price_min')) {
            $query->where('price', '>=', $request->price_min);
        }
        if ($request->filled('price_max')) {
            $query->where('price', '<=', $request->price_max);
        }

        // Filtro por duración
        if ($request->filled('duration_filter')) {
            switch ($request->duration_filter) {
                case 'semanal':
                    $query->where('duration_days', '<=', 7);
                    break;
                case 'mensual':
                    $query->whereBetween('duration_days', [8, 31]);
                    break;
                case 'trimestral':
                    $query->whereBetween('duration_days', [32, 93]);
                    break;
                case 'anual':
                    $query->where('duration_days', '>=', 365);
                    break;
            }
        }

        $planes = $query->with('members')->orderBy('plan_name')->paginate(10)->withQueryString();

        // Estadísticas
        $stats = [
            'total_planes' => MembershipPlan::count(),
            'precio_promedio' => MembershipPlan::avg('price'),
            'plan_mas_popular' => $this->getPlanMasPopular(),
            'total_miembros_activos' => Member::whereNotNull('plan_id')->count()
        ];

        return view('admin.membership-plans.index', compact('planes', 'stats'));
    }

    /**
     * Mostrar formulario para crear nuevo plan.
     */
    public function create()
    {
        return view('admin.membership-plans.create');
    }

    /**
     * Almacenar nuevo plan de membresía.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'plan_name' => 'required|string|max:100|unique:membership_plans,plan_name',
            'description' => 'required|string|max:1000',
            'price' => 'required|numeric|min:0|max:999999.99',
            'duration_days' => 'required|integer|min:1|max:3650', // máximo 10 años
        ]);

        try {
            $plan = MembershipPlan::create($validated);



            return redirect()->route('admin.membership-plans.index')
                ->with('success', 'Plan de membresía creado exitosamente.');

        } catch (\Exception $e) {
            Log::error('Error al crear plan de membresía: ' . $e->getMessage());
            return redirect()->back()
                ->withErrors(['error' => 'Error al crear el plan de membresía.'])
                ->withInput();
        }
    }

    /**
     * Mostrar detalles de un plan específico.
     */
    public function show(MembershipPlan $membershipPlan)
    {
        // Cargar miembros asociados con paginación
        $miembros = $membershipPlan->members()
            ->orderBy('subscription_end_date', 'desc')
            ->paginate(15);

        // Estadísticas del plan
        $estadisticas = [
            'total_miembros' => $membershipPlan->members()->count(),
            'miembros_activos' => $membershipPlan->members()
                ->where('subscription_end_date', '>=', now())
                ->count(),
            'ingresos_totales' => $membershipPlan->members()->count() * $membershipPlan->price,
            'duracion_promedio' => $membershipPlan->duration_days
        ];

        return view('admin.membership-plans.show', compact('membershipPlan', 'miembros', 'estadisticas'));
    }

    /**
     * Mostrar formulario para editar plan.
     */
    public function edit(MembershipPlan $membershipPlan)
    {
        return view('admin.membership-plans.edit', compact('membershipPlan'));
    }

    /**
     * Actualizar plan de membresía.
     */
    public function update(Request $request, MembershipPlan $membershipPlan)
    {
        $validated = $request->validate([
            'plan_name' => 'required|string|max:100|unique:membership_plans,plan_name,' . $membershipPlan->id,
            'description' => 'required|string|max:1000',
            'price' => 'required|numeric|min:0|max:999999.99',
            'duration_days' => 'required|integer|min:1|max:3650',
        ]);

        try {
            $membershipPlan->update($validated);

            Log::info('Plan de membresía actualizado exitosamente', [
                'plan_id' => $membershipPlan->id,
                'nombre' => $membershipPlan->plan_name
            ]);

            return redirect()->route('admin.membership-plans.index')
                ->with('success', 'Plan de membresía actualizado exitosamente.');

        } catch (\Exception $e) {
            Log::error('Error al actualizar plan de membresía: ' . $e->getMessage());
            return redirect()->back()
                ->withErrors(['error' => 'Error al actualizar el plan de membresía.'])
                ->withInput();
        }
    }

    /**
     * Eliminar plan de membresía.
     */
    public function destroy(MembershipPlan $membershipPlan)
    {
        try {
            // Verificar si hay miembros asociados
            $cantidadMiembros = $membershipPlan->members()->count();
            
            if ($cantidadMiembros > 0) {
                return redirect()->back()
                    ->withErrors(['error' => "No se puede eliminar el plan porque tiene {$cantidadMiembros} miembro(s) asociado(s)."]);
            }

            $nombrePlan = $membershipPlan->plan_name;
            $membershipPlan->delete();

            Log::info('Plan de membresía eliminado exitosamente', [
                'plan_eliminado' => $nombrePlan
            ]);

            return redirect()->route('admin.membership-plans.index')
                ->with('success', 'Plan de membresía eliminado exitosamente.');

        } catch (\Exception $e) {
            Log::error('Error al eliminar plan de membresía: ' . $e->getMessage());
            return redirect()->back()
                ->withErrors(['error' => 'Error al eliminar el plan de membresía.']);
        }
    }

    /**
     * Obtener el plan más popular (con más miembros).
     */
    private function getPlanMasPopular()
    {
        return MembershipPlan::withCount('members')
            ->orderBy('members_count', 'desc')
            ->first();
    }

    /**
     * Duplicar un plan existente.
     */
    public function duplicate(MembershipPlan $membershipPlan)
    {
        try {
            $nuevoPlan = $membershipPlan->replicate();
            $nuevoPlan->plan_name = $membershipPlan->plan_name . ' (Copia)';
            $nuevoPlan->save();

            Log::info('Plan de membresía duplicado exitosamente', [
                'plan_original' => $membershipPlan->plan_name,
                'plan_nuevo' => $nuevoPlan->plan_name
            ]);

            return redirect()->route('admin.membership-plans.edit', $nuevoPlan)
                ->with('success', 'Plan duplicado exitosamente. Puedes editarlo ahora.');

        } catch (\Exception $e) {
            Log::error('Error al duplicar plan de membresía: ' . $e->getMessage());
            return redirect()->back()
                ->withErrors(['error' => 'Error al duplicar el plan de membresía.']);
        }
    }
}