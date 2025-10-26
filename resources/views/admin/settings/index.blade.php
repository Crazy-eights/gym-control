@extends('layouts.admin')

@section('title', 'Configuraciones')

@section('content')
    <div class="content-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="h3 mb-0">Configuraciones del Sistema</h1>
                <p class="text-muted mb-0">Personaliza las configuraciones generales</p>
            </div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i class="fa fa-dashboard"></i> Inicio</a></li>
                    <li class="breadcrumb-item active">Configuraciones</li>
                </ol>
            </nav>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.settings.update') }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        
        <div class="row">
            <!-- Información General -->
            <div class="col-md-8">
                <div class="box">
                    <div class="box-header">
                        <h3 class="box-title">Información General</h3>
                    </div>
                    
                    <div class="box-body">
                        @if(session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <i class="fas fa-check-circle"></i> {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="site_name" class="form-label">Nombre del Sitio *</label>
                                    <input type="text" 
                                           class="form-control @error('site_name') is-invalid @enderror" 
                                           id="site_name" 
                                           name="site_name" 
                                           value="{{ old('site_name', $settings['site_name'] ?? 'Gym Control') }}" 
                                           required>
                                    @error('site_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="site_email" class="form-label">Email del Sitio *</label>
                                    <input type="email" 
                                           class="form-control @error('site_email') is-invalid @enderror" 
                                           id="site_email" 
                                           name="site_email" 
                                           value="{{ old('site_email', $settings['site_email'] ?? 'admin@gymcontrol.com') }}" 
                                           required>
                                    @error('site_email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="site_description" class="form-label">Descripción</label>
                            <textarea class="form-control @error('site_description') is-invalid @enderror" 
                                      id="site_description" 
                                      name="site_description" 
                                      rows="3">{{ old('site_description', $settings['site_description'] ?? '') }}</textarea>
                            @error('site_description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="site_phone" class="form-label">Teléfono</label>
                                    <input type="text" 
                                           class="form-control @error('site_phone') is-invalid @enderror" 
                                           id="site_phone" 
                                           name="site_phone" 
                                           value="{{ old('site_phone', $settings['site_phone'] ?? '') }}">
                                    @error('site_phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="currency" class="form-label">Moneda *</label>
                                    <select class="form-control @error('currency') is-invalid @enderror" 
                                            id="currency" 
                                            name="currency" 
                                            required>
                                        <option value="USD" {{ old('currency', $settings['currency'] ?? 'USD') == 'USD' ? 'selected' : '' }}>USD ($)</option>
                                        <option value="EUR" {{ old('currency', $settings['currency'] ?? 'USD') == 'EUR' ? 'selected' : '' }}>EUR (€)</option>
                                        <option value="MXN" {{ old('currency', $settings['currency'] ?? 'USD') == 'MXN' ? 'selected' : '' }}>MXN ($)</option>
                                        <option value="COP" {{ old('currency', $settings['currency'] ?? 'USD') == 'COP' ? 'selected' : '' }}>COP ($)</option>
                                    </select>
                                    @error('currency')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="site_address" class="form-label">Dirección</label>
                            <textarea class="form-control @error('site_address') is-invalid @enderror" 
                                      id="site_address" 
                                      name="site_address" 
                                      rows="2">{{ old('site_address', $settings['site_address'] ?? '') }}</textarea>
                            @error('site_address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="timezone" class="form-label">Zona Horaria *</label>
                            <select class="form-control @error('timezone') is-invalid @enderror" 
                                    id="timezone" 
                                    name="timezone" 
                                    required>
                                <option value="America/Mexico_City" {{ old('timezone', $settings['timezone'] ?? 'America/Mexico_City') == 'America/Mexico_City' ? 'selected' : '' }}>México City (GMT-6)</option>
                                <option value="America/New_York" {{ old('timezone', $settings['timezone'] ?? 'America/Mexico_City') == 'America/New_York' ? 'selected' : '' }}>New York (GMT-5)</option>
                                <option value="America/Los_Angeles" {{ old('timezone', $settings['timezone'] ?? 'America/Mexico_City') == 'America/Los_Angeles' ? 'selected' : '' }}>Los Angeles (GMT-8)</option>
                                <option value="Europe/Madrid" {{ old('timezone', $settings['timezone'] ?? 'America/Mexico_City') == 'Europe/Madrid' ? 'selected' : '' }}>Madrid (GMT+1)</option>
                                <option value="America/Bogota" {{ old('timezone', $settings['timezone'] ?? 'America/Mexico_City') == 'America/Bogota' ? 'selected' : '' }}>Bogotá (GMT-5)</option>
                            </select>
                            @error('timezone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Archivos y Multimedia -->
            <div class="col-md-4">
                <div class="box">
                    <div class="box-header">
                        <h3 class="box-title">Logo y Favicon</h3>
                    </div>
                    
                    <div class="box-body">
                        <!-- Logo -->
                        <div class="mb-4">
                            <label for="logo" class="form-label">Logo del Sitio</label>
                            
                            @if(isset($settings['site_logo']) && Storage::disk('public')->exists($settings['site_logo']))
                                <div class="current-logo mb-3">
                                    <p class="form-text">Logo actual:</p>
                                    <img src="{{ Storage::url($settings['site_logo']) }}" 
                                         alt="Logo actual" 
                                         class="img-thumbnail" 
                                         style="max-width: 200px; max-height: 100px;">
                                </div>
                            @endif
                            
                            <input type="file" 
                                   class="form-control @error('logo') is-invalid @enderror" 
                                   id="logo" 
                                   name="logo" 
                                   accept="image/*"
                                   onchange="previewImage(event, 'logoPreview')">
                            @error('logo')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Formatos: JPEG, PNG, JPG, GIF, SVG. Max: 2MB</div>
                            
                            <div class="mt-3" id="logoPreview" style="display: none;">
                                <p class="form-text">Nuevo logo:</p>
                                <img id="logoImg" src="" alt="Preview" class="img-thumbnail" style="max-width: 200px; max-height: 100px;">
                            </div>
                        </div>

                        <!-- Favicon -->
                        <div class="mb-3">
                            <label for="favicon" class="form-label">Favicon</label>
                            
                            @if(isset($settings['site_favicon']) && Storage::disk('public')->exists($settings['site_favicon']))
                                <div class="current-favicon mb-3">
                                    <p class="form-text">Favicon actual:</p>
                                    <img src="{{ Storage::url($settings['site_favicon']) }}" 
                                         alt="Favicon actual" 
                                         class="img-thumbnail" 
                                         style="max-width: 32px; max-height: 32px;">
                                </div>
                            @endif
                            
                            <input type="file" 
                                   class="form-control @error('favicon') is-invalid @enderror" 
                                   id="favicon" 
                                   name="favicon" 
                                   accept=".ico,.png"
                                   onchange="previewImage(event, 'faviconPreview')">
                            @error('favicon')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Formatos: ICO, PNG. Max: 1MB</div>
                            
                            <div class="mt-3" id="faviconPreview" style="display: none;">
                                <p class="form-text">Nuevo favicon:</p>
                                <img id="faviconImg" src="" alt="Preview" class="img-thumbnail" style="max-width: 32px; max-height: 32px;">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Acciones -->
                <div class="box">
                    <div class="box-header">
                        <h3 class="box-title">Acciones</h3>
                    </div>
                    
                    <div class="box-body">
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Guardar Configuraciones
                            </button>
                            
                            <button type="button" 
                                    class="btn btn-outline-warning"
                                    onclick="confirmReset()">
                                <i class="fas fa-undo"></i> Restaurar por Defecto
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <!-- Modal de confirmación de reset -->
    <div class="modal fade" id="resetModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirmar Restauración</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>¿Estás seguro de que deseas restaurar todas las configuraciones a sus valores por defecto?</p>
                    <p class="text-warning"><i class="fas fa-exclamation-triangle"></i> Esta acción sobrescribirá todas las configuraciones actuales.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <form action="{{ route('admin.settings.reset') }}" method="POST" style="display: inline;">
                        @csrf
                        <button type="submit" class="btn btn-warning">Restaurar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
function previewImage(event, previewId) {
    const file = event.target.files[0];
    const preview = document.getElementById(previewId);
    const img = preview.querySelector('img');
    
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            img.src = e.target.result;
            preview.style.display = 'block';
        }
        reader.readAsDataURL(file);
    } else {
        preview.style.display = 'none';
    }
}

function confirmReset() {
    new bootstrap.Modal(document.getElementById('resetModal')).show();
}
</script>
@endpush