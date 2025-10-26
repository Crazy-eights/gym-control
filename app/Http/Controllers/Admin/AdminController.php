<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class AdminController extends Controller
{
    /**
     * Mostrar lista de administradores.
     */
    public function index()
    {
        $admins = Admin::paginate(10);
        return view('admin.admins.index', compact('admins'));
    }

    /**
     * Mostrar formulario para crear nuevo administrador.
     */
    public function create()
    {
        return view('admin.admins.create');
    }

    /**
     * Guardar nuevo administrador.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'username' => 'required|string|max:255|unique:admin,username',
            'email' => 'required|email|max:255|unique:admin,email',
            'firstname' => 'required|string|max:255',
            'lastname' => 'required|string|max:255',
            'password' => 'required|string|min:6|confirmed',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Manejar la foto si se subió
        $photoPath = '';
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('admin_photos', 'public');
        }

        Admin::create([
            'username' => $validated['username'],
            'email' => $validated['email'],
            'firstname' => $validated['firstname'],
            'lastname' => $validated['lastname'],
            'password' => Hash::make($validated['password']),
            'photo' => $photoPath,
            'created_on' => date('Y-m-d'),
        ]);

        return redirect()->route('admin.admins.index')
            ->with('success', 'Administrador creado exitosamente.');
    }

    /**
     * Mostrar detalles de un administrador.
     */
    public function show(Admin $admin)
    {
        return view('admin.admins.show', compact('admin'));
    }

    /**
     * Mostrar formulario para editar administrador.
     */
    public function edit(Admin $admin)
    {
        return view('admin.admins.edit', compact('admin'));
    }

    /**
     * Actualizar administrador.
     */
    public function update(Request $request, Admin $admin)
    {
        $validated = $request->validate([
            'username' => ['required', 'string', 'max:255', Rule::unique('admin')->ignore($admin->id)],
            'email' => ['required', 'email', 'max:255', Rule::unique('admin')->ignore($admin->id)],
            'firstname' => 'required|string|max:255',
            'lastname' => 'required|string|max:255',
            'password' => 'nullable|string|min:6|confirmed',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Manejar la foto si se subió
        if ($request->hasFile('photo')) {
            // Eliminar foto anterior si existe
            if ($admin->photo && Storage::disk('public')->exists($admin->photo)) {
                Storage::disk('public')->delete($admin->photo);
            }
            $validated['photo'] = $request->file('photo')->store('admin_photos', 'public');
        }

        // Solo actualizar password si se proporcionó uno nuevo
        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $admin->update($validated);

        return redirect()->route('admin.admins.index')
            ->with('success', 'Administrador actualizado exitosamente.');
    }

    /**
     * Eliminar administrador.
     */
    public function destroy(Admin $admin)
    {
        // No permitir eliminar al usuario logueado
        if ($admin->id === auth('admin')->id()) {
            return redirect()->route('admin.admins.index')
                ->with('error', 'No puedes eliminar tu propia cuenta.');
        }

        // Eliminar foto si existe
        if ($admin->photo && Storage::disk('public')->exists($admin->photo)) {
            Storage::disk('public')->delete($admin->photo);
        }

        $admin->delete();

        return redirect()->route('admin.admins.index')
            ->with('success', 'Administrador eliminado exitosamente.');
    }
}