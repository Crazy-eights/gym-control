<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingsController extends Controller
{
    /**
     * Mostrar configuraciones del sistema.
     */
    public function index()
    {
        // Obtener todas las configuraciones como array asociativo
        $settings = Setting::all_settings();
        
        return view('admin.settings.index', compact('settings'));
    }

    /**
     * Actualizar configuraciones del sistema.
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'site_name' => 'required|string|max:255',
            'site_description' => 'nullable|string|max:500',
            'site_email' => 'required|email|max:255',
            'site_phone' => 'nullable|string|max:20',
            'site_address' => 'nullable|string|max:500',
            'currency' => 'required|string|max:10',
            'timezone' => 'required|string|max:50',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'favicon' => 'nullable|image|mimes:ico,png|max:1024',
        ]);

        // Manejar upload del logo
        if ($request->hasFile('logo')) {
            // Eliminar logo anterior si existe
            $oldLogo = Setting::where('setting_key', 'site_logo')->first();
            if ($oldLogo && $oldLogo->setting_value && Storage::disk('public')->exists($oldLogo->setting_value)) {
                Storage::disk('public')->delete($oldLogo->setting_value);
            }
            
            $logoPath = $request->file('logo')->store('settings', 'public');
            Setting::set('site_logo', $logoPath);
        }

        // Manejar upload del favicon
        if ($request->hasFile('favicon')) {
            // Eliminar favicon anterior si existe
            $oldFavicon = Setting::where('setting_key', 'site_favicon')->first();
            if ($oldFavicon && $oldFavicon->setting_value && Storage::disk('public')->exists($oldFavicon->setting_value)) {
                Storage::disk('public')->delete($oldFavicon->setting_value);
            }
            
            $faviconPath = $request->file('favicon')->store('settings', 'public');
            Setting::set('site_favicon', $faviconPath);
        }

        // Actualizar el resto de configuraciones
        foreach ($validated as $key => $value) {
            if (!in_array($key, ['logo', 'favicon'])) {
                Setting::set($key, $value);
            }
        }

        return redirect()->route('admin.settings.index')
            ->with('success', 'Configuraciones actualizadas exitosamente.');
    }

    /**
     * Actualizar o crear una configuración específica.
     */
    private function updateSetting($key, $value)
    {
        Setting::set($key, $value);
    }

    /**
     * Restaurar configuraciones por defecto.
     */
    public function reset()
    {
        // Configuraciones por defecto
        $defaultSettings = [
            'site_name' => 'Gym Control',
            'site_description' => 'Sistema de gestión integral para gimnasios',
            'site_email' => 'admin@gymcontrol.com',
            'site_phone' => '',
            'site_address' => '',
            'currency' => 'USD',
            'timezone' => 'America/Mexico_City',
        ];

        foreach ($defaultSettings as $key => $value) {
            Setting::set($key, $value);
        }

        return redirect()->route('admin.settings.index')
            ->with('success', 'Configuraciones restauradas a valores por defecto.');
    }
}