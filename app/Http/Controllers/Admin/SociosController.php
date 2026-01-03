<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Member;
use App\Models\MembershipPlan;
use App\Models\Admin;
use App\Notifications\NewMemberRegistered;
use App\Notifications\MembershipExpiring;
use App\Notifications\MemberSuspended;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;

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
                    $query->activeSubscription();
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
                case 'suspended':
                    $query->suspended();
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
            'activos' => Member::activeSubscription()->count(),
            'suspendidos' => Member::suspended()->count(),
            'vencidos' => Member::expired()->count(),
            'proximos_vencimiento' => Member::whereBetween('subscription_end_date', [
                now(),
                now()->addDays(7)
            ])->count(),
        ];

        // Si es una petición AJAX, devolver solo la tabla
        if ($request->ajax() || $request->get('ajax')) {
            return view('admin.socios.partials.table', compact('socios', 'planes'))->render();
        }

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
            'email' => 'nullable|email|unique:members,email',
            'password' => 'required|string|min:6|confirmed',
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
            'email.email' => 'El email debe ser una dirección válida.',
            'email.unique' => 'Este email ya está registrado.',
            'password.required' => 'La contraseña es obligatoria.',
            'password.min' => 'La contraseña debe tener al menos 6 caracteres.',
            'password.confirmed' => 'La confirmación de contraseña no coincide.',
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

            // Hash de la contraseña
            $validated['password'] = Hash::make($validated['password']);

            // Calcular fecha de fin automáticamente si se selecciona un plan
            if (isset($validated['plan_id']) && $validated['plan_id'] && 
                isset($validated['subscription_start_date']) && $validated['subscription_start_date']) {
                $plan = MembershipPlan::find($validated['plan_id']);
                if ($plan) {
                    $startDate = \Carbon\Carbon::parse($validated['subscription_start_date']);
                    $validated['subscription_end_date'] = $startDate->copy()->addDays($plan->duration_days)->format('Y-m-d');
                }
            }

            $socio = Member::create($validated);

            // Notificar a todos los admins sobre el nuevo socio
            $admins = Admin::all();
            foreach ($admins as $admin) {
                $admin->notify(new NewMemberRegistered($socio));
            }

            // Verificar si la membresía está vencida o próxima a vencer
            if (isset($validated['subscription_end_date'])) {
                $endDate = \Carbon\Carbon::parse($validated['subscription_end_date']);
                $daysLeft = now()->diffInDays($endDate, false);
                
                // Notificar si está vencida (hasta 30 días atrás) o por vencer (próximos 7 días)
                if ($daysLeft >= -30 && $daysLeft <= 7) {
                    foreach ($admins as $admin) {
                        $admin->notify(new MembershipExpiring($socio, $daysLeft));
                    }
                }
            }

            // Si es una petición AJAX, devolver JSON
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Socio creado exitosamente',
                    'socio' => $socio
                ]);
            }

            return redirect()->route('admin.socios.index')
                ->with('success', 'Socio registrado exitosamente.');

        } catch (\Illuminate\Validation\ValidationException $e) {
            // Si es AJAX y hay errores de validación
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error de validación',
                    'errors' => $e->errors()
                ], 422);
            }
            
            // Para requests normales, manejar como antes
            throw $e;
            
        } catch (\Exception $e) {
            Log::error('Error al crear socio: ' . $e->getMessage());
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al crear el socio: ' . $e->getMessage()
                ], 500);
            }
            
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
    public function edit(Request $request, Member $socio)
    {
        // Si es una petición AJAX, devolver datos JSON
        if ($request->ajax() || $request->expectsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
            try {
                return response()->json([
                    'success' => true,
                    'socio' => [
                        'id' => $socio->id,
                        'member_id' => $socio->member_id,
                        'firstname' => $socio->firstname,
                        'lastname' => $socio->lastname,
                        'email' => $socio->email,
                        'contact_info' => $socio->contact_info,
                        'gender' => $socio->gender,
                        'birthdate' => $socio->birthdate,
                        'address' => $socio->address,
                        'plan_id' => $socio->plan_id,
                        'subscription_start_date' => $socio->subscription_start_date,
                        'subscription_end_date' => $socio->subscription_end_date,
                        'status' => $socio->status, // Usa el atributo calculado del modelo
                        'photo' => $socio->photo,
                    ]
                ]);
            } catch (\Exception $e) {
                Log::error('Error al obtener datos del socio para edición: ' . $e->getMessage());
                return response()->json([
                    'success' => false,
                    'error' => 'Error al cargar los datos del socio'
                ], 500);
            }
        }

        // Si es una petición normal, devolver vista
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
            'email' => 'nullable|email|unique:members,email,' . $socio->id,
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
            'email.email' => 'El email debe ser una dirección válida.',
            'email.unique' => 'Este email ya está registrado.',
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

            // Calcular fecha de fin automáticamente si se selecciona un plan
            if (isset($validated['plan_id']) && $validated['plan_id'] && 
                isset($validated['subscription_start_date']) && $validated['subscription_start_date']) {
                $plan = MembershipPlan::find($validated['plan_id']);
                if ($plan) {
                    $startDate = \Carbon\Carbon::parse($validated['subscription_start_date']);
                    // Solo recalcular si la fecha de fin no se proporcionó o es diferente al cálculo
                    $calculatedEndDate = $startDate->copy()->addDays($plan->duration_days);
                    if (!isset($validated['subscription_end_date']) || !$validated['subscription_end_date'] ||
                        \Carbon\Carbon::parse($validated['subscription_end_date'])->ne($calculatedEndDate)) {
                        $validated['subscription_end_date'] = $calculatedEndDate->format('Y-m-d');
                    }
                }
            } elseif (!isset($validated['plan_id']) || !$validated['plan_id']) {
                // Si no hay plan, limpiar las fechas
                $validated['subscription_start_date'] = null;
                $validated['subscription_end_date'] = null;
            }

            $socio->update($validated);

            // Verificar si la membresía está vencida o próxima a vencer
            if (isset($validated['subscription_end_date'])) {
                $endDate = \Carbon\Carbon::parse($validated['subscription_end_date']);
                $daysLeft = now()->diffInDays($endDate, false);
                
                // Notificar si está vencida (hasta 30 días atrás) o por vencer (próximos 7 días)
                if ($daysLeft >= -30 && $daysLeft <= 7) {
                    $admins = Admin::all();
                    foreach ($admins as $admin) {
                        $admin->notify(new MembershipExpiring($socio, $daysLeft));
                    }
                }
            }

            // Si es una petición AJAX, devolver JSON
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Socio actualizado exitosamente',
                    'socio' => $socio->load('membershipPlan')
                ]);
            }

            return redirect()->route('admin.socios.index')
                ->with('success', 'Datos del socio actualizados exitosamente.');

        } catch (\Illuminate\Validation\ValidationException $e) {
            // Si es AJAX y hay errores de validación
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error de validación',
                    'errors' => $e->errors()
                ], 422);
            }
            
            // Para requests normales, manejar como antes
            throw $e;

        } catch (\Exception $e) {
            Log::error('Error al actualizar socio: ' . $e->getMessage());
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al actualizar el socio: ' . $e->getMessage()
                ], 500);
            }
            
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

    /**
     * Suspender un socio.
     */
    public function suspend(Request $request, Member $socio)
    {
        $validated = $request->validate([
            'suspension_reason' => 'required|string|max:500',
        ], [
            'suspension_reason.required' => 'Debes proporcionar un motivo para la suspensión.',
            'suspension_reason.max' => 'El motivo no puede tener más de 500 caracteres.',
        ]);

        try {
            $socio->update([
                'status' => 'suspended',
                'suspension_reason' => $validated['suspension_reason'],
                'suspended_at' => now(),
            ]);

            // Notificar a todos los admins
            $admins = Admin::all();
            foreach ($admins as $admin) {
                $admin->notify(new MemberSuspended($socio, $validated['suspension_reason']));
            }

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Socio suspendido exitosamente.',
                    'socio' => $socio
                ]);
            }

            return redirect()->back()
                ->with('success', 'Socio suspendido exitosamente.');

        } catch (\Exception $e) {
            Log::error('Error al suspender socio: ' . $e->getMessage());
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al suspender el socio.'
                ], 500);
            }

            return redirect()->back()
                ->withErrors(['error' => 'Error al suspender el socio.']);
        }
    }

    /**
     * Reactivar un socio suspendido.
     */
    public function activate(Member $socio)
    {
        try {
            $socio->update([
                'status' => 'active',
                'suspension_reason' => null,
                'suspended_at' => null,
            ]);

            return redirect()->back()
                ->with('success', 'Socio reactivado exitosamente.');

        } catch (\Exception $e) {
            Log::error('Error al reactivar socio: ' . $e->getMessage());
            return redirect()->back()
                ->withErrors(['error' => 'Error al reactivar el socio.']);
        }
    }
}
