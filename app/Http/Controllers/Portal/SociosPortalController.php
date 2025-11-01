<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\Member;
use App\Models\MembershipPlan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;

class SociosPortalController extends Controller
{
    /**
     * Dashboard del portal de socios
     */
    public function dashboard()
    {
        $socio = auth()->user();
        
        // Estadísticas del socio
        $diasRestantes = null;
        $estadoMembresia = $socio->status;
        
        if ($socio->subscription_end_date) {
            $diasRestantes = now()->diffInDays($socio->subscription_end_date, false);
        }
        
        // Asistencias reales recientes del socio
        $asistenciasRecientes = \App\Models\MemberAttendance::where('member_id', $socio->id)
            ->whereNotNull('checkin_time')
            ->orderBy('checkin_time', 'desc')
            ->take(5)
            ->get()
            ->map(function($asistencia) {
                // Usar checkin_time para fecha y hora ya que attendance_date está en NULL
                if ($asistencia->checkin_time) {
                    return (object)[
                        'fecha' => $asistencia->checkin_time,
                        'hora' => $asistencia->checkin_time->format('H:i'),
                        'tipo' => 'entrada' // Por ahora solo manejamos entradas
                    ];
                }
                return null;
            })
            ->filter(); // Remover elementos null
        
        // Próximas clases - por ahora empty hasta crear el módulo de clases
        $proximasClases = collect([]);
        
        return view('portal.dashboard', compact(
            'socio',
            'diasRestantes',
            'estadoMembresia',
            'asistenciasRecientes',
            'proximasClases'
        ));
    }
    
    /**
     * Mostrar perfil del socio
     */
    public function perfil()
    {
        $socio = auth()->user();
        return view('portal.perfil', compact('socio'));
    }
    
    /**
     * Actualizar perfil del socio
     */
    public function actualizarPerfil(Request $request)
    {
        $socio = auth()->user();
        
        $request->validate([
            'firstname' => 'required|string|max:255',
            'lastname' => 'required|string|max:255',
            'email' => 'required|email|unique:members,email,' . $socio->id,
            'contact_info' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:500',
            'birthdate' => 'nullable|date',
            'gender' => 'nullable|in:masculino,femenino,otro',
            'photo' => 'nullable|image|max:2048'
        ], [
            'firstname.required' => 'El nombre es obligatorio.',
            'lastname.required' => 'El apellido es obligatorio.',
            'email.required' => 'El email es obligatorio.',
            'email.email' => 'El email debe tener un formato válido.',
            'email.unique' => 'Este email ya está en uso.',
            'photo.image' => 'El archivo debe ser una imagen.',
            'photo.max' => 'La imagen no puede superar los 2MB.'
        ]);
        
        $data = $request->except(['photo']);
        
        // Manejar la foto
        if ($request->hasFile('photo')) {
            // Eliminar foto anterior si existe
            if ($socio->photo) {
                Storage::delete('public/' . $socio->photo);
            }
            
            $photoPath = $request->file('photo')->store('socios', 'public');
            $data['photo'] = $photoPath;
        }
        
        $socio->update($data);
        
        return redirect()->route('portal.perfil')
            ->with('success', 'Perfil actualizado exitosamente.');
    }
    
    /**
     * Cambiar contraseña
     */
    public function cambiarPassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => ['required', 'confirmed', Password::min(8)],
        ], [
            'current_password.required' => 'La contraseña actual es obligatoria.',
            'password.required' => 'La nueva contraseña es obligatoria.',
            'password.confirmed' => 'La confirmación de contraseña no coincide.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.'
        ]);
        
        $socio = auth()->user();
        
        // Verificar contraseña actual
        if (!Hash::check($request->current_password, $socio->password)) {
            return back()->withErrors(['current_password' => 'La contraseña actual es incorrecta.']);
        }
        
        $socio->update([
            'password' => Hash::make($request->password)
        ]);
        
        return redirect()->route('portal.perfil')
            ->with('success', 'Contraseña actualizada exitosamente.');
    }
    
    /**
     * Información de membresía
     */
    public function membresia()
    {
        $socio = auth()->user();
        $plan = $socio->membershipPlan;
        
        // Historial de pagos simulado
        $historialPagos = collect([
            (object)[
                'fecha' => now()->subMonth(),
                'monto' => $plan ? $plan->price : 0,
                'concepto' => 'Mensualidad ' . now()->subMonth()->format('F Y'),
                'estado' => 'pagado'
            ],
            (object)[
                'fecha' => now()->subMonths(2),
                'monto' => $plan ? $plan->price : 0,
                'concepto' => 'Mensualidad ' . now()->subMonths(2)->format('F Y'),
                'estado' => 'pagado'
            ],
            (object)[
                'fecha' => now()->subMonths(3),
                'monto' => $plan ? $plan->price : 0,
                'concepto' => 'Mensualidad ' . now()->subMonths(3)->format('F Y'),
                'estado' => 'pagado'
            ]
        ]);
        
        return view('portal.membresia', compact('socio', 'plan', 'historialPagos'));
    }
    
    /**
     * Ver clases disponibles
     */
    public function clases()
    {
        // Clases disponibles simuladas
        $clases = collect([
            (object)[
                'nombre' => 'Yoga Matutino',
                'instructor' => 'María González',
                'descripcion' => 'Clase de yoga relajante para comenzar el día con energía.',
                'horarios' => ['Lunes 08:00', 'Miércoles 08:00', 'Viernes 08:00'],
                'duracion' => '60 min',
                'nivel' => 'Principiante',
                'cupo_maximo' => 15,
                'inscritos' => 12
            ],
            (object)[
                'nombre' => 'CrossFit',
                'instructor' => 'Carlos Ruiz',
                'descripcion' => 'Entrenamiento funcional de alta intensidad.',
                'horarios' => ['Martes 19:00', 'Jueves 19:00', 'Sábado 10:00'],
                'duracion' => '45 min',
                'nivel' => 'Intermedio',
                'cupo_maximo' => 12,
                'inscritos' => 8
            ],
            (object)[
                'nombre' => 'Spinning',
                'instructor' => 'Ana López',
                'descripcion' => 'Cardio intenso sobre bicicleta estática.',
                'horarios' => ['Lunes 07:30', 'Miércoles 18:00', 'Viernes 07:30'],
                'duracion' => '50 min',
                'nivel' => 'Todos los niveles',
                'cupo_maximo' => 20,
                'inscritos' => 16
            ],
            (object)[
                'nombre' => 'Pilates',
                'instructor' => 'Laura Fernández',
                'descripcion' => 'Fortalecimiento del core y mejora de la flexibilidad.',
                'horarios' => ['Martes 10:00', 'Jueves 17:00'],
                'duracion' => '55 min',
                'nivel' => 'Principiante',
                'cupo_maximo' => 10,
                'inscritos' => 7
            ]
        ]);
        
        return view('portal.clases', compact('clases'));
    }
    
    /**
     * Ver rutinas disponibles
     */
    public function rutinas()
    {
        // Rutinas disponibles simuladas
        $rutinas = collect([
            (object)[
                'nombre' => 'Rutina para Principiantes',
                'descripcion' => 'Rutina básica para quienes comienzan en el gimnasio.',
                'duracion' => '45 min',
                'nivel' => 'Principiante',
                'tipo' => 'Cuerpo completo',
                'ejercicios' => [
                    'Calentamiento: 10 min caminata',
                    'Sentadillas: 3 series de 12 repeticiones',
                    'Flexiones: 3 series de 8 repeticiones',
                    'Plancha: 3 series de 30 segundos',
                    'Estiramiento: 10 minutos'
                ]
            ],
            (object)[
                'nombre' => 'Rutina de Fuerza',
                'descripcion' => 'Enfocada en desarrollo de fuerza y masa muscular.',
                'duracion' => '60 min',
                'nivel' => 'Intermedio',
                'tipo' => 'Fuerza',
                'ejercicios' => [
                    'Calentamiento: 5 min bicicleta',
                    'Press de banca: 4 series de 8-10 reps',
                    'Sentadilla con peso: 4 series de 8-10 reps',
                    'Peso muerto: 4 series de 6-8 reps',
                    'Remo con barra: 4 series de 8-10 reps'
                ]
            ],
            (object)[
                'nombre' => 'Cardio Intenso',
                'descripcion' => 'Rutina cardiovascular para quemar calorías.',
                'duracion' => '30 min',
                'nivel' => 'Intermedio',
                'tipo' => 'Cardio',
                'ejercicios' => [
                    'Calentamiento: 5 min caminata',
                    'Burpees: 4 series de 10 reps',
                    'Mountain climbers: 4 series de 30 segundos',
                    'Jumping jacks: 4 series de 30 segundos',
                    'Sprint en cinta: 5 intervalos de 1 min'
                ]
            ],
            (object)[
                'nombre' => 'Yoga Flow',
                'descripcion' => 'Secuencia de yoga para flexibilidad y relajación.',
                'duracion' => '50 min',
                'nivel' => 'Todos los niveles',
                'tipo' => 'Flexibilidad',
                'ejercicios' => [
                    'Respiración consciente: 5 min',
                    'Saludo al sol: 5 repeticiones',
                    'Posturas de pie: 15 min',
                    'Posturas sentado: 15 min',
                    'Relajación final: 10 min'
                ]
            ]
        ]);
        
        return view('portal.rutinas', compact('rutinas'));
    }
    
    /**
     * Configuración del perfil
     */
    public function configuracion()
    {
        $socio = auth()->user();
        return view('portal.configuracion', compact('socio'));
    }
}