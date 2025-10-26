<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Member;
use App\Models\MembershipPlan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class SociosController extends Controller
{
    /**
     * Mostrar lista de socios con paginación y búsqueda.
     */
    public function index(Request $request)
    {
        $query = Member::with('membershipPlan');

        // Búsqueda por nombre
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('firstname', 'like', "%{$search}%")
                  ->orWhere('lastname', 'like', "%{$search}%")
                  ->orWhere('member_id', 'like', "%{$search}%")
                  ->orWhere('contact_info', 'like', "%{$search}%");
            });
        }

        // Filtro por estado
        if ($request->filled('status')) {
            switch ($request->status) {
                case 'activo':
                    $query->active();
                    break;
                case 'vencido':
                    $query->expired();
                    break;
                case 'sin_plan':
                    $query->whereNull('plan_id');
                    break;
                case 'proximo_vencimiento':
                    $query->whereBetween('subscription_end_date', [
                        now(),
                        now()->addDays(7)
                    ]);
                    break;
            }
        }

        // Filtro por plan
        if ($request->filled('plan_id')) {
            $query->where('plan_id', $request->plan_id);
        }

        // Ordenamiento
        $sortBy = $request->get('sort_by', 'created_at');
        $sortDirection = $request->get('sort_direction', 'desc');
        $query->orderBy($sortBy, $sortDirection);

        $socios = $query->paginate(15);
        $planes = MembershipPlan::all();

        // Estadísticas rápidas
        $stats = [
            'total' => Member::count(),
            'activos' => Member::active()->count(),
            'vencidos' => Member::expired()->count(),
            'proximos_vencimiento' => Member::whereBetween('subscription_end_date', [
                now(),
                now()->addDays(7)
            ])->count(),
        ];

        return view('admin.socios.index', compact('socios', 'planes', 'stats'));
    }

    /**
     * Mostrar formulario para crear nuevo socio.
     */
    public function create()
    {
        $planes = MembershipPlan::all();
        return view('admin.socios.create', compact('planes'));
    }

    /**
     * Almacenar nuevo socio.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'member_id' => 'required|string|max:50|unique:members,member_id',
            'firstname' => 'required|string|max:100',
            'lastname' => 'required|string|max:100',
            'address' => 'nullable|string|max:255',
            'birthdate' => 'nullable|date|before:today',
            'contact_info' => 'required|string|max:100',
            'gender' => 'required|in:M,F,Otro',
            'plan_id' => 'nullable|exists:membership_plans,id',
            'subscription_start_date' => 'nullable|date',
            'subscription_end_date' => 'nullable|date|after_or_equal:subscription_start_date',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ], [
            'member_id.required' => 'El ID del socio es obligatorio.',
            'member_id.unique' => 'Este ID de socio ya está en uso. Por favor, elige otro.',
            'member_id.max' => 'El ID del socio no puede tener más de 50 caracteres.',
            'firstname.required' => 'El nombre es obligatorio.',
            'firstname.max' => 'El nombre no puede tener más de 100 caracteres.',
            'lastname.required' => 'El apellido es obligatorio.',
            'lastname.max' => 'El apellido no puede tener más de 100 caracteres.',
            'address.max' => 'La dirección no puede tener más de 255 caracteres.',
            'birthdate.date' => 'La fecha de nacimiento debe ser una fecha válida.',
            'birthdate.before' => 'La fecha de nacimiento debe ser anterior a hoy.',
            'contact_info.required' => 'La información de contacto es obligatoria.',
            'contact_info.max' => 'La información de contacto no puede tener más de 100 caracteres.',
            'gender.required' => 'El género es obligatorio.',
            'gender.in' => 'El género debe ser Masculino, Femenino u Otro.',
            'plan_id.exists' => 'El plan de membresía seleccionado no existe.',
            'subscription_start_date.date' => 'La fecha de inicio debe ser una fecha válida.',
            'subscription_end_date.date' => 'La fecha de fin debe ser una fecha válida.',
            'subscription_end_date.after_or_equal' => 'La fecha de fin debe ser igual o posterior a la fecha de inicio.',
            'photo.image' => 'El archivo debe ser una imagen.',
            'photo.mimes' => 'La imagen debe ser de tipo: jpeg, png, jpg o gif.',
            'photo.max' => 'La imagen no puede ser mayor a 2MB.',
        ]);

        try {
            // Manejo de la foto
            if ($request->hasFile('photo')) {
                $photoPath = $request->file('photo')->store('socios', 'public');
                $validated['photo'] = $photoPath;
            }

            $socio = Member::create($validated);



            return redirect()->route('admin.socios.index')
                ->with('success', 'Socio registrado exitosamente.');

        } catch (\Exception $e) {
            Log::error('Error al crear socio: ' . $e->getMessage());
            return redirect()->back()
                ->withErrors(['error' => 'Error al registrar el socio.'])
                ->withInput();
        }
    }

    /**
     * Mostrar detalles de un socio específico.
     */
    public function show(Member $socio)
    {
        $socio->load('membershipPlan');
        return view('admin.socios.show', compact('socio'));
    }

    /**
     * Mostrar formulario para editar socio.
     */
    public function edit(Member $socio)
    {
        $planes = MembershipPlan::all();
        return view('admin.socios.edit', compact('socio', 'planes'));
    }

    /**
     * Actualizar datos del socio.
     */
    public function update(Request $request, Member $socio)
    {
        $validated = $request->validate([
            'member_id' => 'required|string|max:50|unique:members,member_id,' . $socio->id,
            'firstname' => 'required|string|max:100',
            'lastname' => 'required|string|max:100',
            'address' => 'nullable|string|max:255',
            'birthdate' => 'nullable|date|before:today',
            'contact_info' => 'required|string|max:100',
            'gender' => 'required|in:M,F,Otro',
            'plan_id' => 'nullable|exists:membership_plans,id',
            'subscription_start_date' => 'nullable|date',
            'subscription_end_date' => 'nullable|date|after_or_equal:subscription_start_date',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ], [
            'member_id.required' => 'El ID del socio es obligatorio.',
            'member_id.unique' => 'Este ID de socio ya está en uso. Por favor, elige otro.',
            'member_id.max' => 'El ID del socio no puede tener más de 50 caracteres.',
            'firstname.required' => 'El nombre es obligatorio.',
            'firstname.max' => 'El nombre no puede tener más de 100 caracteres.',
            'lastname.required' => 'El apellido es obligatorio.',
            'lastname.max' => 'El apellido no puede tener más de 100 caracteres.',
            'address.max' => 'La dirección no puede tener más de 255 caracteres.',
            'birthdate.date' => 'La fecha de nacimiento debe ser una fecha válida.',
            'birthdate.before' => 'La fecha de nacimiento debe ser anterior a hoy.',
            'contact_info.required' => 'La información de contacto es obligatoria.',
            'contact_info.max' => 'La información de contacto no puede tener más de 100 caracteres.',
            'gender.required' => 'El género es obligatorio.',
            'gender.in' => 'El género debe ser Masculino, Femenino u Otro.',
            'plan_id.exists' => 'El plan de membresía seleccionado no existe.',
            'subscription_start_date.date' => 'La fecha de inicio debe ser una fecha válida.',
            'subscription_end_date.date' => 'La fecha de fin debe ser una fecha válida.',
            'subscription_end_date.after_or_equal' => 'La fecha de fin debe ser igual o posterior a la fecha de inicio.',
            'photo.image' => 'El archivo debe ser una imagen.',
            'photo.mimes' => 'La imagen debe ser de tipo: jpeg, png, jpg o gif.',
            'photo.max' => 'La imagen no puede ser mayor a 2MB.',
        ]);

        try {
            // Manejo de la foto
            if ($request->hasFile('photo')) {
                // Eliminar foto anterior si existe
                if ($socio->photo) {
                    Storage::disk('public')->delete($socio->photo);
                }
                $photoPath = $request->file('photo')->store('socios', 'public');
                $validated['photo'] = $photoPath;
            }

            $socio->update($validated);



            return redirect()->route('admin.socios.index')
                ->with('success', 'Datos del socio actualizados exitosamente.');

        } catch (\Exception $e) {
            Log::error('Error al actualizar socio: ' . $e->getMessage());
            return redirect()->back()
                ->withErrors(['error' => 'Error al actualizar los datos.'])
                ->withInput();
        }
    }

    /**
     * Eliminar socio.
     */
    public function destroy(Member $socio)
    {
        try {
            // Eliminar foto si existe
            if ($socio->photo) {
                Storage::disk('public')->delete($socio->photo);
            }

            $nombreSocio = $socio->full_name;
            $memberID = $socio->member_id;

            $socio->delete();



            return redirect()->route('admin.socios.index')
                ->with('success', 'Socio eliminado exitosamente.');

        } catch (\Exception $e) {
            Log::error('Error al eliminar socio: ' . $e->getMessage());
            return redirect()->back()
                ->withErrors(['error' => 'Error al eliminar el socio.']);
        }
    }

    /**
     * Renovar membresía de un socio.
     */
    public function renovarMembresia(Request $request, Member $socio)
    {
        $validated = $request->validate([
            'plan_id' => 'required|exists:membership_plans,id',
            'subscription_start_date' => 'required|date',
        ]);

        try {
            $plan = MembershipPlan::findOrFail($validated['plan_id']);
            $startDate = \Carbon\Carbon::parse($validated['subscription_start_date']);
            $endDate = $startDate->copy()->addDays($plan->duration_days);

            $socio->update([
                'plan_id' => $plan->id,
                'subscription_start_date' => $startDate,
                'subscription_end_date' => $endDate,
            ]);



            return redirect()->back()
                ->with('success', 'Membresía renovada exitosamente.');

        } catch (\Exception $e) {
            Log::error('Error al renovar membresía: ' . $e->getMessage());
            return redirect()->back()
                ->withErrors(['error' => 'Error al renovar la membresía.']);
        }
    }
}