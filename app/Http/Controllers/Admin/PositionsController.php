<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Position;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PositionsController extends Controller
{
    public function index(Request $request)
    {
        $query = Position::withCount('employees');
        
        // Búsqueda
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('description', 'LIKE', "%{$search}%");
        }

        $positions = $query->paginate(10);

        // Si es una petición AJAX, devolver solo la tabla
        if ($request->ajax()) {
            return response()->json([
                'html' => view('admin.positions.partials.table', compact('positions'))->render(),
                'pagination' => $positions->links()->render()
            ]);
        }

        return view('admin.positions.index', compact('positions'));
    }

    public function show($id)
    {
        $position = Position::withCount('employees')->findOrFail($id);
        
        return response()->json([
            'position' => $position
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'description' => 'required|string|max:100|unique:position,description',
            // Tarifa opcional, por defecto 0.00
            'rate' => 'nullable|numeric|min:0|max:99999.99'
        ], [
            'description.required' => 'La descripción es obligatoria.',
            'description.max' => 'La descripción no puede tener más de 100 caracteres.',
            'description.unique' => 'Ya existe un puesto con esta descripción.',
            'rate.numeric' => 'La tarifa debe ser un número válido.',
            'rate.min' => 'La tarifa no puede ser negativa.',
            'rate.max' => 'La tarifa no puede ser mayor a 99999.99.'
        ]);

        // Asignar valor por defecto si no se proporciona
        $validated['rate'] = $validated['rate'] ?? 0.00;

        try {
            $position = Position::create($validated);

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Puesto creada exitosamente',
                    'position' => $position
                ]);
            }

            return redirect()->route('admin.positions.index')
                           ->with('success', 'Puesto creada exitosamente');
                           
        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error de validación',
                    'errors' => $e->errors()
                ], 422);
            }
            throw $e;
            
        } catch (\Exception $e) {
            Log::error('Error al crear puesto: ' . $e->getMessage());
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al crear el puesto: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->back()
                           ->withErrors(['error' => 'Error al crear el puesto.'])
                           ->withInput();
        }
    }

    public function update(Request $request, $id)
    {
        $position = Position::findOrFail($id);

        $validated = $request->validate([
            'description' => 'required|string|max:100|unique:position,description,' . $position->id,
            // Tarifa opcional, mantener valor actual si no se proporciona
            'rate' => 'nullable|numeric|min:0|max:99999.99'
        ], [
            'description.required' => 'La descripción es obligatoria.',
            'description.max' => 'La descripción no puede tener más de 100 caracteres.',
            'description.unique' => 'Ya existe un puesto con esta descripción.',
            'rate.numeric' => 'La tarifa debe ser un número válido.',
            'rate.min' => 'La tarifa no puede ser negativa.',
            'rate.max' => 'La tarifa no puede ser mayor a 99999.99.'
        ]);

        // Mantener valor actual si no se proporciona nuevo
        if (!isset($validated['rate'])) {
            $validated['rate'] = $position->rate;
        }

        try {
            $position->update($validated);

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Puesto actualizado exitosamente',
                    'position' => $position
                ]);
            }

            return redirect()->route('admin.positions.index')
                           ->with('success', 'Puesto actualizado exitosamente');
                           
        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error de validación',
                    'errors' => $e->errors()
                ], 422);
            }
            throw $e;
            
        } catch (\Exception $e) {
            Log::error('Error al actualizar puesto: ' . $e->getMessage());
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al actualizar el puesto: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->back()
                           ->withErrors(['error' => 'Error al actualizar el puesto.'])
                           ->withInput();
        }
    }

    public function destroy($id)
    {
        try {
            $position = Position::findOrFail($id);
            
            // Verificar si hay empleados asignados a esta Puesto
            if ($position->employees()->count() > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se puede eliminar este puesto porque tiene empleados asignados.'
                ], 400);
            }
            
            $position->delete();

            return response()->json([
                'success' => true,
                'message' => 'Puesto eliminado exitosamente'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error al eliminar puesto: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar el puesto: ' . $e->getMessage()
            ], 500);
        }
    }
}