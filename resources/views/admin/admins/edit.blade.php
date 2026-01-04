@extends('layouts.admin-modern')@extends('layouts.admin')



@section('title', 'Editar Administrador')@section('title', 'Editar Administrador')

@section('page-title', 'Administradores')

@section('content')

@section('content')    <div class="content-header">

<div class="animate-fade-in-up">        <div class="d-flex justify-content-between align-items-center">

    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">            <div>

        <div>                <h1 class="h3 mb-0">Editar Administrador</h1>

            <h2 class="text-success mb-1">                <p class="text-muted mb-0">Modificar información del administrador</p>

                <i class="fas fa-user-edit me-2"></i>Editar Administrador            </div>

            </h2>            <nav aria-label="breadcrumb">

            <p class="text-muted mb-0">Actualiza los datos de {{ $admin->firstname }} {{ $admin->lastname }}</p>                <ol class="breadcrumb mb-0">

        </div>                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i class="fa fa-dashboard"></i> Inicio</a></li>

        <div class="d-flex flex-wrap gap-2">                    <li class="breadcrumb-item"><a href="{{ route('admin.admins.index') }}">Administradores</a></li>

            <a href="{{ route('admin.admins.show', $admin) }}" class="btn btn-outline-success btn-modern">                    <li class="breadcrumb-item active">Editar</li>

                <i class="fas fa-eye me-2"></i>Ver Detalles                </ol>

            </a>            </nav>

            <a href="{{ route('admin.admins.index') }}" class="btn btn-secondary btn-modern">        </div>

                <i class="fas fa-arrow-left me-2"></i>Volver al listado    </div>

            </a>

        </div>    <div class="row">

    </div>        <div class="col-md-8 offset-md-2">

            <div class="box">

    <div class="row g-4">                <div class="box-header">

        <div class="col-xl-4">                    <h3 class="box-title">Información del Administrador</h3>

            <div class="modern-card">                </div>

                <div class="modern-card-header">

                    <h6 class="mb-0 fw-bold text-info">                <div class="box-body">

                        <i class="fas fa-user-circle me-2"></i>Perfil Actual                    <form method="POST" action="{{ route('admin.admins.update', $admin) }}" enctype="multipart/form-data">

                    </h6>                        @csrf

                </div>                        @method('PUT')

                <div class="modern-card-body text-center">

                    @if($admin->photo && Storage::disk('public')->exists($admin->photo))                        <div class="row">

                        <img src="{{ Storage::url($admin->photo) }}" alt="{{ $admin->firstname }}"                            <!-- Información Personal -->

                             class="rounded-circle shadow mb-3" style="width: 120px; height: 120px; object-fit: cover;">                            <div class="col-md-6">

                    @else                                <div class="mb-3">

                        <div class="rounded-circle bg-gradient-success d-flex align-items-center justify-content-center mx-auto mb-3"                                    <label for="firstname" class="form-label">Nombre *</label>

                             style="width: 120px; height: 120px; font-size: 48px;">                                    <input type="text"

                            {{ strtoupper(substr($admin->firstname, 0, 1)) }}                                           class="form-control @error('firstname') is-invalid @enderror"

                        </div>                                           id="firstname"

                    @endif                                           name="firstname"

                    <p class="mb-0 fw-semibold">{{ $admin->firstname }} {{ $admin->lastname }}</p>                                           value="{{ old('firstname', $admin->firstname) }}"

                    <p class="text-muted small mb-0">{{ $admin->email }}</p>                                           required>

                </div>                                    @error('firstname')

            </div>                                        <div class="invalid-feedback">{{ $message }}</div>

                                    @enderror

            <div class="modern-card">                                </div>

                <div class="modern-card-header">                            </div>

                    <h6 class="mb-0 fw-bold text-secondary"><i class="fas fa-info-circle me-2"></i>Consejos rápidos</h6>

                </div>                            <div class="col-md-6">

                <div class="modern-card-body">                                <div class="mb-3">

                    <ul class="list-unstyled text-muted small mb-0">                                    <label for="lastname" class="form-label">Apellido *</label>

                        <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Solo cambia la contraseña si el usuario lo solicita.</li>                                    <input type="text"

                        <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Mantén correos corporativos para notificaciones.</li>                                           class="form-control @error('lastname') is-invalid @enderror"

                        <li><i class="fas fa-check text-success me-2"></i>Utiliza fotos recientes para identificar al equipo.</li>                                           id="lastname"

                    </ul>                                           name="lastname"

                </div>                                           value="{{ old('lastname', $admin->lastname) }}"

            </div>                                           required>

        </div>                                    @error('lastname')

                                        <div class="invalid-feedback">{{ $message }}</div>

        <div class="col-xl-8">                                    @enderror

            <div class="modern-card">                                </div>

                <div class="modern-card-header">                            </div>

                    <h6 class="mb-0 fw-bold text-success">                        </div>

                        <i class="fas fa-pen me-2"></i>Actualizar Información

                    </h6>                        <div class="row">

                </div>                            <!-- Información de Cuenta -->

                <div class="modern-card-body">                            <div class="col-md-6">

                    <form action="{{ route('admin.admins.update', $admin) }}" method="POST" enctype="multipart/form-data" novalidate>                                <div class="mb-3">

                        @csrf                                    <label for="username" class="form-label">Nombre de Usuario *</label>

                        @method('PUT')                                    <input type="text"

                                           class="form-control @error('username') is-invalid @enderror"

                        <div class="row g-3">                                           id="username"

                            <div class="col-md-6">                                           name="username"

                                <label for="firstname" class="form-label fw-semibold text-success">Nombre <span class="text-danger">*</span></label>                                           value="{{ old('username', $admin->username) }}"

                                <input type="text" id="firstname" name="firstname"                                           required>

                                       class="form-control form-control-sm @error('firstname') is-invalid @enderror"                                    @error('username')

                                       value="{{ old('firstname', $admin->firstname) }}" required>                                        <div class="invalid-feedback">{{ $message }}</div>

                                @error('firstname')<div class="invalid-feedback">{{ $message }}</div>@enderror                                    @enderror

                            </div>                                </div>

                            <div class="col-md-6">                            </div>

                                <label for="lastname" class="form-label fw-semibold text-success">Apellido <span class="text-danger">*</span></label>

                                <input type="text" id="lastname" name="lastname"                            <div class="col-md-6">

                                       class="form-control form-control-sm @error('lastname') is-invalid @enderror"                                <div class="mb-3">

                                       value="{{ old('lastname', $admin->lastname) }}" required>                                    <label for="email" class="form-label">Email *</label>

                                @error('lastname')<div class="invalid-feedback">{{ $message }}</div>@enderror                                    <input type="email"

                            </div>                                           class="form-control @error('email') is-invalid @enderror"

                        </div>                                           id="email"

                                           name="email"

                        <div class="row g-3 mt-1">                                           value="{{ old('email', $admin->email) }}"

                            <div class="col-md-6">                                           required>

                                <label for="username" class="form-label fw-semibold text-success">Usuario <span class="text-danger">*</span></label>                                    @error('email')

                                <input type="text" id="username" name="username"                                        <div class="invalid-feedback">{{ $message }}</div>

                                       class="form-control form-control-sm @error('username') is-invalid @enderror"                                    @enderror

                                       value="{{ old('username', $admin->username) }}" required>                                </div>

                                @error('username')<div class="invalid-feedback">{{ $message }}</div>@enderror                            </div>

                            </div>                        </div>

                            <div class="col-md-6">

                                <label for="email" class="form-label fw-semibold text-success">Correo <span class="text-danger">*</span></label>                        <div class="row">

                                <input type="email" id="email" name="email"                            <!-- Contraseña -->

                                       class="form-control form-control-sm @error('email') is-invalid @enderror"                            <div class="col-md-6">

                                       value="{{ old('email', $admin->email) }}" required>                                <div class="mb-3">

                                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror                                    <label for="password" class="form-label">Nueva Contraseña</label>

                            </div>                                    <input type="password"

                        </div>                                           class="form-control @error('password') is-invalid @enderror"

                                           id="password"

                        <div class="row g-3 mt-1">                                           name="password">

                            <div class="col-md-6">                                    <div class="form-text">Dejar en blanco para mantener la contraseña actual</div>

                                <label for="password" class="form-label fw-semibold text-success">Nueva Contraseña</label>                                    @error('password')

                                <input type="password" id="password" name="password"                                        <div class="invalid-feedback">{{ $message }}</div>

                                       class="form-control form-control-sm @error('password') is-invalid @enderror">                                    @enderror

                                @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror                                </div>

                                <small class="text-muted">Déjalo en blanco para mantener la contraseña actual.</small>                            </div>

                            </div>

                            <div class="col-md-6">                            <div class="col-md-6">

                                <label for="password_confirmation" class="form-label fw-semibold text-success">Confirmar Contraseña</label>                                <div class="mb-3">

                                <input type="password" id="password_confirmation" name="password_confirmation"                                    <label for="password_confirmation" class="form-label">Confirmar Nueva Contraseña</label>

                                       class="form-control form-control-sm">                                    <input type="password"

                            </div>                                           class="form-control"

                        </div>                                           id="password_confirmation"

                                           name="password_confirmation">

                        <div class="mt-3">                                </div>

                            <label for="photo" class="form-label fw-semibold text-success">Actualizar Foto</label>                            </div>

                            <input type="file" id="photo" name="photo" accept="image/*"                        </div>

                                   class="form-control form-control-sm @error('photo') is-invalid @enderror">

                            @error('photo')<div class="invalid-feedback">{{ $message }}</div>@enderror                        <!-- Foto de Perfil -->

                            <small class="text-muted">Deja el campo vacío para conservar la foto actual.</small>                        <div class="mb-3">

                            <label for="photo" class="form-label">Foto de Perfil</label>

                            <div class="d-flex align-items-center gap-3 mt-3" id="photoPreview" style="display:none;">

                                <img id="photoPreviewImage" class="rounded-circle shadow" style="width:70px; height:70px; object-fit:cover;" alt="Vista previa">                            <!-- Foto actual -->

                                <div class="text-muted small">Nueva vista previa</div>                            @if($admin->photo && Storage::disk('public')->exists($admin->photo))

                            </div>                                <div class="current-photo mb-3">

                        </div>                                    <p class="form-text">Foto actual:</p>

                                    <img src="{{ Storage::url($admin->photo) }}"

                        <div class="d-flex justify-content-end gap-2 mt-4">                                         alt="{{ $admin->firstname }}"

                            <a href="{{ route('admin.admins.index') }}" class="btn btn-outline-secondary btn-modern">                                         class="img-thumbnail"

                                <i class="fas fa-times me-2"></i>Cancelar                                         style="max-width: 150px; max-height: 150px;">

                            </a>                                </div>

                            <button type="submit" class="btn btn-warning btn-modern text-white">                            @endif

                                <i class="fas fa-save me-2"></i>Actualizar Administrador

                            </button>                            <input type="file"

                        </div>                                   class="form-control @error('photo') is-invalid @enderror"

                    </form>                                   id="photo"

                </div>                                   name="photo"

            </div>                                   accept="image/*"

        </div>                                   onchange="previewImage(event)">

    </div>                            @error('photo')

</div>                                <div class="invalid-feedback">{{ $message }}</div>

@endsection                            @enderror

                            <div class="form-text">Formatos soportados: JPEG, PNG, JPG, GIF. Tamaño máximo: 2MB</div>

@push('scripts')

<script>                            <!-- Preview de la nueva imagen -->

document.addEventListener('DOMContentLoaded', function() {                            <div class="mt-3" id="imagePreview" style="display: none;">

    const photoInput = document.getElementById('photo');                                <p class="form-text">Nueva foto:</p>

    const previewContainer = document.getElementById('photoPreview');                                <img id="preview" src="" alt="Preview" class="img-thumbnail" style="max-width: 150px; max-height: 150px;">

    const previewImage = document.getElementById('photoPreviewImage');                            </div>

                        </div>

    if (photoInput) {

        photoInput.addEventListener('change', function(event) {                        <div class="d-flex justify-content-between align-items-center">

            const file = event.target.files[0];                            <a href="{{ route('admin.admins.index') }}" class="btn btn-secondary">

            if (!file) {                                <i class="fas fa-arrow-left"></i> Volver

                previewContainer.style.display = 'none';                            </a>

                return;                            <button type="submit" class="btn btn-primary">

            }                                <i class="fas fa-save"></i> Actualizar Administrador

                            </button>

            const reader = new FileReader();                        </div>

            reader.onload = function(e) {                    </form>

                previewImage.src = e.target.result;                </div>

                previewContainer.style.display = 'flex';            </div>

            };        </div>

            reader.readAsDataURL(file);    </div>

        });@endsection

    }

});@push('scripts')

</script><script>

@endpushfunction previewImage(event) {

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
