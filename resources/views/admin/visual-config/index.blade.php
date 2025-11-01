@extends('layouts.admin-modern')

@section('title', 'Configuración Visual')

@section('header-color', 'bg-success')

@section('content')
<div class="content-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="h3 mb-0 text-success">Configuración Visual</h1>
            <p class="text-muted mb-0">Personaliza la apariencia del sistema</p>
        </div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-success"><i class="fa fa-dashboard"></i> Inicio</a></li>
                <li class="breadcrumb-item active">Configuración Visual</li>
            </ol>
        </nav>
    </div>
</div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <!-- Logo Principal -->
        <div class="col-lg-6 mb-4">
            <div class="card-modern">
                <div class="card-modern-header">
                    <h3 class="card-modern-title">
                        <i class="fas fa-image text-success me-2"></i>
                        Logo Principal
                    </h3>
                </div>
                <div class="card-modern-body">
                    <form action="{{ route('admin.visual.config.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="mb-3">
                            <label for="logo" class="form-label">Subir Logo</label>
                            <input type="file" class="form-control" id="logo" name="logo" accept="image/*">
                            <div class="form-text">Formatos permitidos: PNG, JPG, JPEG, SVG. Tamaño máximo: 2MB</div>
                        </div>
                        
                        @if($config && $config->logo)
                            <div class="current-logo mb-3">
                                <label class="form-label">Logo Actual:</label>
                                <div class="mt-2">
                                    <img src="{{ asset('storage/' . $config->logo) }}" alt="Logo actual" class="img-thumbnail" style="max-height: 80px;">
                                </div>
                            </div>
                        @endif
                        
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-upload me-1"></i>
                            Actualizar Logo
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Logo Secundario -->
        <div class="col-lg-6 mb-4">
            <div class="card-modern">
                <div class="card-modern-header">
                    <h3 class="card-modern-title">
                        <i class="fas fa-images text-info me-2"></i>
                        Logo Secundario
                    </h3>
                </div>
                <div class="card-modern-body">
                    <form action="{{ route('admin.visual.config.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="mb-3">
                            <label for="secondary_logo" class="form-label">Subir Logo Secundario</label>
                            <input type="file" class="form-control" id="secondary_logo" name="secondary_logo" accept="image/*">
                            <div class="form-text">Para uso en facturas, emails, etc.</div>
                        </div>
                        
                        @if($config && $config->secondary_logo)
                            <div class="current-logo mb-3">
                                <label class="form-label">Logo Secundario Actual:</label>
                                <div class="mt-2">
                                    <img src="{{ asset('storage/' . $config->secondary_logo) }}" alt="Logo secundario actual" class="img-thumbnail" style="max-height: 80px;">
                                </div>
                            </div>
                        @endif
                        
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-upload me-1"></i>
                            Actualizar Logo Secundario
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Colores y Tipografía -->
    <div class="row">
        <div class="col-lg-12 mb-4">
            <div class="card-modern">
                <div class="card-modern-header">
                    <h3 class="card-modern-title">
                        <i class="fas fa-palette text-warning me-2"></i>
                        Esquema de Colores y Tipografía
                    </h3>
                </div>
                <div class="card-modern-body">
                    <form action="{{ route('admin.visual.config.update') }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="primary_color" class="form-label">Color Primario</label>
                                    <input type="color" class="form-control form-control-color" id="primary_color" name="primary_color" 
                                           value="{{ $config->primary_color ?? '#007bff' }}">
                                </div>
                                
                                <div class="mb-3">
                                    <label for="secondary_color" class="form-label">Color Secundario</label>
                                    <input type="color" class="form-control form-control-color" id="secondary_color" name="secondary_color" 
                                           value="{{ $config->secondary_color ?? '#6c757d' }}">
                                </div>
                                
                                <div class="mb-3">
                                    <label for="accent_color" class="form-label">Color de Acento</label>
                                    <input type="color" class="form-control form-control-color" id="accent_color" name="accent_color" 
                                           value="{{ $config->accent_color ?? '#28a745' }}">
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="font_family" class="form-label">Fuente Principal</label>
                                    <select class="form-select" id="font_family" name="font_family">
                                        <option value="Nunito, sans-serif" {{ ($config->font_family ?? '') == 'Nunito, sans-serif' ? 'selected' : '' }}>Nunito (Predeterminada)</option>
                                        <option value="Roboto, sans-serif" {{ ($config->font_family ?? '') == 'Roboto, sans-serif' ? 'selected' : '' }}>Roboto</option>
                                        <option value="Open Sans, sans-serif" {{ ($config->font_family ?? '') == 'Open Sans, sans-serif' ? 'selected' : '' }}>Open Sans</option>
                                        <option value="Poppins, sans-serif" {{ ($config->font_family ?? '') == 'Poppins, sans-serif' ? 'selected' : '' }}>Poppins</option>
                                        <option value="Montserrat, sans-serif" {{ ($config->font_family ?? '') == 'Montserrat, sans-serif' ? 'selected' : '' }}>Montserrat</option>
                                    </select>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="navbar_color" class="form-label">Color de Navegación</label>
                                    <input type="color" class="form-control form-control-color" id="navbar_color" name="navbar_color" 
                                           value="{{ $config->navbar_color ?? '#ffffff' }}">
                                </div>
                                
                                <div class="mb-3">
                                    <label for="sidebar_color" class="form-label">Color de Barra Lateral</label>
                                    <input type="color" class="form-control form-control-color" id="sidebar_color" name="sidebar_color" 
                                           value="{{ $config->sidebar_color ?? '#5a5c69' }}">
                                </div>
                            </div>
                        </div>
                        
                        <hr>
                        
                        <div class="row">
                            <div class="col-md-12">
                                <h6 class="fw-bold mb-3">
                                    <i class="fas fa-eye me-2"></i>
                                    Vista Previa de Colores
                                </h6>
                                <div class="color-preview mb-3">
                                    <div class="row g-3">
                                        <div class="col-md-2">
                                            <div class="text-center">
                                                <div class="color-sample" style="background-color: {{ $config->primary_color ?? '#007bff' }}; height: 50px; border-radius: 8px; margin-bottom: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);"></div>
                                                <small class="text-muted">Primario</small>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="text-center">
                                                <div class="color-sample" style="background-color: {{ $config->secondary_color ?? '#6c757d' }}; height: 50px; border-radius: 8px; margin-bottom: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);"></div>
                                                <small class="text-muted">Secundario</small>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="text-center">
                                                <div class="color-sample" style="background-color: {{ $config->accent_color ?? '#28a745' }}; height: 50px; border-radius: 8px; margin-bottom: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);"></div>
                                                <small class="text-muted">Acento</small>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="text-center">
                                                <div class="color-sample" style="background-color: {{ $config->navbar_color ?? '#ffffff' }}; height: 50px; border-radius: 8px; margin-bottom: 8px; border: 1px solid #ddd; box-shadow: 0 2px 4px rgba(0,0,0,0.1);"></div>
                                                <small class="text-muted">Navegación</small>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="text-center">
                                                <div class="color-sample" style="background-color: {{ $config->sidebar_color ?? '#5a5c69' }}; height: 50px; border-radius: 8px; margin-bottom: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);"></div>
                                                <small class="text-muted">Barra Lateral</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-save me-1"></i>
                            Actualizar Colores y Tipografía
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Configuraciones Adicionales -->
    <div class="row">
        <div class="col-lg-12 mb-4">
            <div class="card-modern">
                <div class="card-modern-header">
                    <h3 class="card-modern-title">
                        <i class="fas fa-cogs text-primary me-2"></i>
                        Configuraciones Adicionales
                    </h3>
                </div>
                <div class="card-modern-body">
                    <form action="{{ route('admin.visual.config.update') }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="favicon" class="form-label">Favicon URL</label>
                                    <input type="url" class="form-control" id="favicon" name="favicon" 
                                           value="{{ $config->favicon ?? '' }}" placeholder="https://ejemplo.com/favicon.ico">
                                    <div class="form-text">URL del favicon (icono del navegador)</div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="meta_description" class="form-label">Meta Descripción</label>
                                    <textarea class="form-control" id="meta_description" name="meta_description" rows="3" 
                                              placeholder="Descripción para motores de búsqueda">{{ $config->meta_description ?? '' }}</textarea>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="custom_css" class="form-label">CSS Personalizado</label>
                                    <textarea class="form-control" id="custom_css" name="custom_css" rows="6" 
                                              placeholder="/* CSS personalizado aquí */">{{ $config->custom_css ?? '' }}</textarea>
                                    <div class="form-text">CSS adicional para personalizaciones avanzadas</div>
                                </div>
                            </div>
                        </div>
                        
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-save me-1"></i>
                            Actualizar Configuraciones
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Restablecer Configuración -->
    <div class="row">
        <div class="col-lg-12 mb-4">
            <div class="card-modern border-danger">
                <div class="card-modern-header bg-danger text-white">
                    <h3 class="card-modern-title mb-0">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Zona de Peligro
                    </h3>
                </div>
                <div class="card-modern-body">
                    <div class="alert alert-warning d-flex align-items-center" role="alert">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <div>
                            <strong>¡Atención!</strong> Restablecer toda la configuración visual eliminará todas las personalizaciones actuales y volverá a los valores predeterminados.
                        </div>
                    </div>
                    
                    <form action="{{ route('admin.visual.config.reset') }}" method="POST" style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger" 
                                onclick="return confirm('¿Estás seguro de que quieres restablecer toda la configuración visual? Esta acción no se puede deshacer.')">
                            <i class="fas fa-undo me-1"></i>
                            Restablecer Configuración
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
// Actualizar vista previa de colores en tiempo real
document.addEventListener('DOMContentLoaded', function() {
    const colorInputs = document.querySelectorAll('input[type="color"]');
    
    colorInputs.forEach(input => {
        input.addEventListener('input', function() {
            updateColorPreview(this);
        });
        
        input.addEventListener('change', function() {
            updateColorPreview(this);
        });
    });
    
    function updateColorPreview(input) {
        const colorName = input.name.replace('_color', '');
        const previewElements = document.querySelectorAll('.color-sample');
        
        previewElements.forEach(element => {
            const elementStyle = element.getAttribute('style');
            if (elementStyle && elementStyle.includes(input.defaultValue)) {
                element.style.backgroundColor = input.value;
            }
        });
        
        // Agregar efecto visual de cambio
        input.parentElement.style.transition = 'all 0.3s ease';
        input.parentElement.style.transform = 'scale(1.02)';
        
        setTimeout(() => {
            input.parentElement.style.transform = 'scale(1)';
        }, 200);
    }
    
    // Mejorar la experiencia del formulario
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', function() {
            const submitBtn = this.querySelector('button[type="submit"]');
            if (submitBtn) {
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Procesando...';
                submitBtn.disabled = true;
            }
        });
    });
});
</script>
@endpush