<?php

namespace App\Http\Controllers;

use App\Models\VisualConfig;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;

class VisualConfigController extends Controller
{
    public function index()
    {
        $config = VisualConfig::first() ?? new VisualConfig();
        
        return view('admin.visual-config.index', compact('config'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'primary_color' => 'nullable|string|max:7',
            'secondary_color' => 'nullable|string|max:7',
            'accent_color' => 'nullable|string|max:7',
            'navbar_color' => 'nullable|string|max:7',
            'sidebar_color' => 'nullable|string|max:7',
            'font_family' => 'nullable|string|max:100',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'secondary_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'favicon' => 'nullable|image|mimes:ico,png|max:1024',
            'meta_description' => 'nullable|string|max:255',
            'custom_css' => 'nullable|string'
        ]);

        $config = VisualConfig::first() ?? new VisualConfig();
        
        // Procesar archivos
        if ($request->hasFile('logo')) {
            $logoPath = $request->file('logo')->store('logos', 'public');
            $config->logo = $logoPath;
        }
        
        if ($request->hasFile('secondary_logo')) {
            $secondaryLogoPath = $request->file('secondary_logo')->store('logos', 'public');
            $config->secondary_logo = $secondaryLogoPath;
        }
        
        if ($request->hasFile('favicon')) {
            $faviconPath = $request->file('favicon')->store('favicons', 'public');
            $config->favicon = $faviconPath;
        }

        // Actualizar colores y configuración
        $config->primary_color = $request->primary_color ?? '#007bff';
        $config->secondary_color = $request->secondary_color ?? '#6c757d';
        $config->accent_color = $request->accent_color ?? '#28a745';
        $config->navbar_color = $request->navbar_color ?? '#343a40';
        $config->sidebar_color = $request->sidebar_color ?? '#343a40';
        $config->font_family = $request->font_family ?? 'Nunito';
        $config->meta_description = $request->meta_description;
        $config->custom_css = $request->custom_css;

        $config->save();

        // Generar CSS dinámico
        $this->generateDynamicCSS($config);

        // Limpiar cache
        Cache::forget('visual_config');

        return redirect()->back()->with('success', 'Configuración visual actualizada correctamente.');
    }

    public function reset()
    {
        $config = VisualConfig::first();
        
        if ($config) {
            // Eliminar archivos antiguos
            if ($config->logo) {
                Storage::disk('public')->delete($config->logo);
            }
            if ($config->secondary_logo) {
                Storage::disk('public')->delete($config->secondary_logo);
            }
            if ($config->favicon) {
                Storage::disk('public')->delete($config->favicon);
            }
            
            $config->delete();
        }

        // Eliminar CSS dinámico
        Storage::disk('public')->delete('css/dynamic-theme.css');

        // Limpiar cache
        Cache::forget('visual_config');

        return redirect()->back()->with('success', 'Configuración visual restablecida a valores por defecto.');
    }

    private function generateDynamicCSS($config)
    {
        $css = ":root {
    --primary-color: {$config->primary_color};
    --secondary-color: {$config->secondary_color};
    --accent-color: {$config->accent_color};
    --navbar-color: {$config->navbar_color};
    --sidebar-color: {$config->sidebar_color};
    --font-family: '{$config->font_family}', sans-serif;
}

/* Configuración de fuente global */
body, .font-weight-bold, .text-gray-800, .text-gray-900 {
    font-family: var(--font-family) !important;
}

/* Colores principales */
.bg-primary, .btn-primary {
    background-color: var(--primary-color) !important;
    border-color: var(--primary-color) !important;
}

.text-primary {
    color: var(--primary-color) !important;
}

.btn-primary:hover, .btn-primary:focus {
    background-color: color-mix(in srgb, var(--primary-color) 90%, black) !important;
    border-color: color-mix(in srgb, var(--primary-color) 90%, black) !important;
}

/* Colores secundarios */
.bg-secondary, .btn-secondary {
    background-color: var(--secondary-color) !important;
    border-color: var(--secondary-color) !important;
}

.text-secondary {
    color: var(--secondary-color) !important;
}

/* Color de acento */
.bg-success, .btn-success {
    background-color: var(--accent-color) !important;
    border-color: var(--accent-color) !important;
}

.text-success {
    color: var(--accent-color) !important;
}

/* Navbar */
.navbar, .topbar {
    background-color: var(--navbar-color) !important;
}

/* Sidebar */
.bg-gradient-primary, .sidebar {
    background: linear-gradient(180deg, var(--sidebar-color) 10%, color-mix(in srgb, var(--sidebar-color) 85%, black) 100%) !important;
}

.sidebar .nav-item .nav-link {
    color: rgba(255, 255, 255, 0.8) !important;
}

.sidebar .nav-item .nav-link:hover {
    color: rgba(255, 255, 255, 1) !important;
}

.sidebar .nav-item .nav-link.active {
    color: var(--primary-color) !important;
    background-color: rgba(255, 255, 255, 0.1) !important;
}

/* Compatibilidad con sidebar colapsado */
.sidebar-collapsed .nav-item .nav-link {
    text-align: center !important;
    padding: 0.75rem 0 !important;
}

.sidebar-collapsed .nav-item .nav-link i {
    margin-right: 0 !important;
    font-size: 1.2rem !important;
}

.sidebar-collapsed .sidebar-brand {
    justify-content: center !important;
}

/* Cards y componentes */
.card-header {
    background-color: color-mix(in srgb, var(--primary-color) 10%, white) !important;
    border-bottom: 1px solid color-mix(in srgb, var(--primary-color) 20%, white) !important;
}

.border-primary {
    border-color: var(--primary-color) !important;
}

/* Enlaces */
a {
    color: var(--primary-color) !important;
}

a:hover {
    color: color-mix(in srgb, var(--primary-color) 80%, black) !important;
}

/* Tablas */
.table-primary {
    background-color: color-mix(in srgb, var(--primary-color) 10%, white) !important;
}

.table thead th {
    background-color: color-mix(in srgb, var(--primary-color) 15%, white) !important;
    border-color: color-mix(in srgb, var(--primary-color) 25%, white) !important;
}

/* Forms */
.form-control:focus {
    border-color: var(--primary-color) !important;
    box-shadow: 0 0 0 0.2rem color-mix(in srgb, var(--primary-color) 25%, transparent) !important;
}

.btn-outline-primary {
    color: var(--primary-color) !important;
    border-color: var(--primary-color) !important;
}

.btn-outline-primary:hover {
    background-color: var(--primary-color) !important;
    border-color: var(--primary-color) !important;
}

/* Badges */
.badge-primary {
    background-color: var(--primary-color) !important;
}

.badge-secondary {
    background-color: var(--secondary-color) !important;
}

.badge-success {
    background-color: var(--accent-color) !important;
}

/* Progress bars */
.progress-bar {
    background-color: var(--primary-color) !important;
}

/* Alertas */
.alert-primary {
    background-color: color-mix(in srgb, var(--primary-color) 15%, white) !important;
    border-color: color-mix(in srgb, var(--primary-color) 30%, white) !important;
    color: color-mix(in srgb, var(--primary-color) 80%, black) !important;
}

/* CSS personalizado adicional */
{$config->custom_css}";

        // Crear directorio si no existe
        Storage::disk('public')->makeDirectory('css');
        
        // Guardar CSS
        Storage::disk('public')->put('css/dynamic-theme.css', $css);
    }
}