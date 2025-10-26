@extends('layouts.admin')

@section('title', 'Editar Administrador')

@section('content')
    <div class="content-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="h3 mb-0">Editar Administrador</h1>
                <p class="text-muted mb-0">Modificar información del administrador</p>
            </div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i class="fa fa-dashboard"></i> Inicio</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.admins.index') }}">Administradores</a></li>
                    <li class="breadcrumb-item active">Editar</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="box">
                <div class="box-header">
                    <h3 class="box-title">Información del Administrador</h3>
                </div>
                
                <div class="box-body">
                    <form method="POST" action="{{ route('admin.admins.update', $admin) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <!-- Información Personal -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="firstname" class="form-label">Nombre *</label>
                                    <input type="text" 
                                           class="form-control @error('firstname') is-invalid @enderror" 
                                           id="firstname" 
                                           name="firstname" 
                                           value="{{ old('firstname', $admin->firstname) }}" 
                                           required>
                                    @error('firstname')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="lastname" class="form-label">Apellido *</label>
                                    <input type="text" 
                                           class="form-control @error('lastname') is-invalid @enderror" 
                                           id="lastname" 
                                           name="lastname" 
                                           value="{{ old('lastname', $admin->lastname) }}" 
                                           required>
                                    @error('lastname')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <!-- Información de Cuenta -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="username" class="form-label">Nombre de Usuario *</label>
                                    <input type="text" 
                                           class="form-control @error('username') is-invalid @enderror" 
                                           id="username" 
                                           name="username" 
                                           value="{{ old('username', $admin->username) }}" 
                                           required>
                                    @error('username')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email *</label>
                                    <input type="email" 
                                           class="form-control @error('email') is-invalid @enderror" 
                                           id="email" 
                                           name="email" 
                                           value="{{ old('email', $admin->email) }}" 
                                           required>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <!-- Contraseña -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="password" class="form-label">Nueva Contraseña</label>
                                    <input type="password" 
                                           class="form-control @error('password') is-invalid @enderror" 
                                           id="password" 
                                           name="password">
                                    <div class="form-text">Dejar en blanco para mantener la contraseña actual</div>
                                    @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="password_confirmation" class="form-label">Confirmar Nueva Contraseña</label>
                                    <input type="password" 
                                           class="form-control" 
                                           id="password_confirmation" 
                                           name="password_confirmation">
                                </div>
                            </div>
                        </div>

                        <!-- Foto de Perfil -->
                        <div class="mb-3">
                            <label for="photo" class="form-label">Foto de Perfil</label>
                            
                            <!-- Foto actual -->
                            @if($admin->photo && Storage::disk('public')->exists($admin->photo))
                                <div class="current-photo mb-3">
                                    <p class="form-text">Foto actual:</p>
                                    <img src="{{ Storage::url($admin->photo) }}" 
                                         alt="{{ $admin->firstname }}" 
                                         class="img-thumbnail" 
                                         style="max-width: 150px; max-height: 150px;">
                                </div>
                            @endif
                            
                            <input type="file" 
                                   class="form-control @error('photo') is-invalid @enderror" 
                                   id="photo" 
                                   name="photo" 
                                   accept="image/*"
                                   onchange="previewImage(event)">
                            @error('photo')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Formatos soportados: JPEG, PNG, JPG, GIF. Tamaño máximo: 2MB</div>
                            
                            <!-- Preview de la nueva imagen -->
                            <div class="mt-3" id="imagePreview" style="display: none;">
                                <p class="form-text">Nueva foto:</p>
                                <img id="preview" src="" alt="Preview" class="img-thumbnail" style="max-width: 150px; max-height: 150px;">
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center">
                            <a href="{{ route('admin.admins.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Volver
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Actualizar Administrador
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
function previewImage(event) {
    const file = event.target.files[0];
    const preview = document.getElementById('preview');
    const previewContainer = document.getElementById('imagePreview');
    
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.src = e.target.result;
            previewContainer.style.display = 'block';
        }
        reader.readAsDataURL(file);
    } else {
        previewContainer.style.display = 'none';
    }
}
</script>
@endpush