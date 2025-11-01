<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\VisualConfig;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class VisualConfigController extends Controller
{
    public function index()
    {
        // Obtener la configuración actual o crear una nueva instancia
        $config = VisualConfig::first();
        
        if (!$config) {
            // Crear configuración por defecto si no existe
            $config = new VisualConfig();
            $config->primary_color = '#007bff';
            $config->secondary_color = '#6c757d';
            $config->accent_color = '#28a745';
            $config->navbar_color = '#ffffff';
            $config->sidebar_color = '#5a5c69';
            $config->font_family = 'Nunito, sans-serif';
            $config->save();
        }

        return view('admin.visual-config.index', compact('config'));
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->only([
            'logo', 'secondary_logo', 'primary_color', 'secondary_color', 
            'accent_color', 'navbar_color', 'sidebar_color', 'font_family', 
            'favicon', 'meta_description', 'custom_css'
        ]), [
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'secondary_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'primary_color' => 'nullable|string|max:7',
            'secondary_color' => 'nullable|string|max:7',
            'accent_color' => 'nullable|string|max:7',
            'navbar_color' => 'nullable|string|max:7',
            'sidebar_color' => 'nullable|string|max:7',
            'font_family' => 'nullable|string|max:255',
            'favicon' => 'nullable|url|max:255',
            'meta_description' => 'nullable|string|max:500',
            'custom_css' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            // Obtener o crear configuración
            $config = VisualConfig::first();
            if (!$config) {
                $config = new VisualConfig();
            }

            // Manejo de archivos
            if ($request->hasFile('logo')) {
                // Eliminar logo anterior si existe
                if ($config->logo && Storage::disk('public')->exists($config->logo)) {
                    Storage::disk('public')->delete($config->logo);
                }
                $config->logo = $request->file('logo')->store('visual-config/logos', 'public');
            }

            if ($request->hasFile('secondary_logo')) {
                // Eliminar logo secundario anterior si existe
                if ($config->secondary_logo && Storage::disk('public')->exists($config->secondary_logo)) {
                    Storage::disk('public')->delete($config->secondary_logo);
                }
                $config->secondary_logo = $request->file('secondary_logo')->store('visual-config/logos', 'public');
            }

            // Actualizar campos de colores y configuración
            if ($request->filled('primary_color')) $config->primary_color = $request->primary_color;
            if ($request->filled('secondary_color')) $config->secondary_color = $request->secondary_color;
            if ($request->filled('accent_color')) $config->accent_color = $request->accent_color;
            if ($request->filled('navbar_color')) $config->navbar_color = $request->navbar_color;
            if ($request->filled('sidebar_color')) $config->sidebar_color = $request->sidebar_color;
            if ($request->filled('font_family')) $config->font_family = $request->font_family;
            if ($request->filled('favicon')) $config->favicon = $request->favicon;
            if ($request->filled('meta_description')) $config->meta_description = $request->meta_description;
            if ($request->filled('custom_css')) $config->custom_css = $request->custom_css;

            $config->save();

            // Generar archivo CSS dinámico
            $this->generateDynamicCSS($config);

            return back()->with('success', 'Configuración visual actualizada exitosamente.');
            
        } catch (\Exception $e) {
            return back()->with('error', 'Error al actualizar configuración: ' . $e->getMessage());
        }
    }

    public function reset()
    {
        try {
            $config = VisualConfig::first();
            
            if ($config) {
                // Eliminar archivos si existen
                if ($config->logo && Storage::disk('public')->exists($config->logo)) {
                    Storage::disk('public')->delete($config->logo);
                }
                if ($config->secondary_logo && Storage::disk('public')->exists($config->secondary_logo)) {
                    Storage::disk('public')->delete($config->secondary_logo);
                }

                // Restablecer valores predeterminados
                $config->logo = null;
                $config->secondary_logo = null;
                $config->primary_color = '#007bff';
                $config->secondary_color = '#6c757d';
                $config->accent_color = '#28a745';
                $config->navbar_color = '#ffffff';
                $config->sidebar_color = '#5a5c69';
                $config->font_family = 'Nunito, sans-serif';
                $config->favicon = null;
                $config->meta_description = null;
                $config->custom_css = null;
                $config->save();
            }
            
            // Regenerar CSS
            $this->generateDynamicCSS($config);

            return back()->with('success', 'Configuración visual restablecida a valores predeterminados.');
            
        } catch (\Exception $e) {
            return back()->with('error', 'Error al restablecer configuración: ' . $e->getMessage());
        }
    }

    private function generateDynamicCSS($config = null)
    {
        if (!$config) {
            $config = VisualConfig::first();
        }
        
        if (!$config) {
            return;
        }
        
        $css = "/* Configuración Visual Personalizada - Generada automáticamente */\n\n";
        
        $css .= ":root {\n";
        
        // Variables CSS dinámicas
        if ($config->primary_color) {
            $css .= "    --bs-primary: {$config->primary_color};\n";
            $css .= "    --gym-primary: {$config->primary_color};\n";
            $css .= "    --primary: {$config->primary_color};\n";
        }
        if ($config->secondary_color) {
            $css .= "    --bs-secondary: {$config->secondary_color};\n";
            $css .= "    --gym-secondary: {$config->secondary_color};\n";
            $css .= "    --secondary: {$config->secondary_color};\n";
        }
        if ($config->accent_color) {
            $css .= "    --bs-success: {$config->accent_color};\n";
            $css .= "    --gym-accent: {$config->accent_color};\n";
            $css .= "    --success: {$config->accent_color};\n";
        }
        if ($config->navbar_color) {
            $css .= "    --navbar-bg: {$config->navbar_color};\n";
        }
        if ($config->sidebar_color) {
            $css .= "    --sidebar-bg: {$config->sidebar_color};\n";
        }
        if ($config->font_family) {
            $css .= "    --bs-font-sans-serif: {$config->font_family};\n";
            $css .= "    --font-family-base: {$config->font_family};\n";
        }
        
        $css .= "}\n\n";
        
        // Estilos globales
        if ($config->font_family) {
            $css .= "body, .sidebar, .navbar, .card, .btn {\n";
            $css .= "    font-family: {$config->font_family} !important;\n";
            $css .= "}\n\n";
        }

        // Botones primarios
        if ($config->primary_color) {
            $css .= ".btn-primary, .bg-primary {\n";
            $css .= "    background-color: {$config->primary_color} !important;\n";
            $css .= "    border-color: {$config->primary_color} !important;\n";
            $css .= "}\n\n";
            
            $css .= ".btn-primary:hover, .btn-primary:focus, .btn-primary:active {\n";
            $css .= "    background-color: {$config->primary_color} !important;\n";
            $css .= "    border-color: {$config->primary_color} !important;\n";
            $css .= "    opacity: 0.9;\n";
            $css .= "}\n\n";
        }

        // Navbar
        if ($config->navbar_color) {
            $css .= ".navbar, .topbar {\n";
            $css .= "    background: {$config->navbar_color} !important;\n";
            $css .= "}\n\n";
        }

        // Sidebar
        if ($config->sidebar_color) {
            $css .= ".sidebar, .bg-gradient-primary, #accordionSidebar {\n";
            $css .= "    background: {$config->sidebar_color} !important;\n";
            $css .= "    background-image: none !important;\n";
            $css .= "}\n\n";
            
            $css .= ".sidebar .nav-item .nav-link {\n";
            $css .= "    color: rgba(255, 255, 255, 0.8) !important;\n";
            $css .= "}\n\n";
            
            $css .= ".sidebar .nav-item .nav-link:hover, .sidebar .nav-item .nav-link.active {\n";
            $css .= "    color: rgba(255, 255, 255, 1) !important;\n";
            $css .= "}\n\n";
        }

        // Enlaces y elementos primarios
        if ($config->primary_color) {
            $css .= "a, .text-primary {\n";
            $css .= "    color: {$config->primary_color} !important;\n";
            $css .= "}\n\n";
            
            $css .= ".border-left-primary {\n";
            $css .= "    border-left: 0.25rem solid {$config->primary_color} !important;\n";
            $css .= "}\n\n";
        }

        // Cards y elementos de acento
        if ($config->accent_color) {
            $css .= ".btn-success, .bg-success {\n";
            $css .= "    background-color: {$config->accent_color} !important;\n";
            $css .= "    border-color: {$config->accent_color} !important;\n";
            $css .= "}\n\n";
        }

        // CSS personalizado del usuario
        if ($config->custom_css) {
            $css .= "\n/* CSS Personalizado del Usuario */\n";
            $css .= $config->custom_css . "\n";
        }

        // Crear directorio si no existe
        if (!Storage::disk('public')->exists('css')) {
            Storage::disk('public')->makeDirectory('css');
        }

        // Guardar CSS dinámico
        Storage::disk('public')->put('css/dynamic-theme.css', $css);
        
        return $css;
    }

    public function seedDefaults()
    {
        try {
            // Crear o actualizar configuración predeterminada
            $config = VisualConfig::first();
            if (!$config) {
                $config = new VisualConfig();
            }
            
            $config->primary_color = '#007bff';
            $config->secondary_color = '#6c757d';
            $config->accent_color = '#28a745';
            $config->navbar_color = '#ffffff';
            $config->sidebar_color = '#5a5c69';
            $config->font_family = 'Nunito, sans-serif';
            $config->save();
            
            $this->generateDynamicCSS($config);
            
            return response()->json(['success' => true, 'message' => 'Configuraciones predeterminadas creadas exitosamente.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
    }

    public function preview(Request $request)
    {
        // Vista previa con configuraciones temporales (para implementar en el futuro)
        return response()->json(['message' => 'Función de vista previa en desarrollo.']);
    }
}