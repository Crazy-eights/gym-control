@extends('layouts.admin-modern')@extends('layouts.admin')



@section('title', 'Crear Administrador')@section('title', 'Crear Administrador')

@section('page-title', 'Administradores')

@section('content')

@section('content')    <div class="content-header">

<div class="animate-fade-in-up">        <div class="d-flex justify-content-between align-items-center">

    <div class="d-flex justify-content-between align-items-center mb-4">            <div>

        <div>                <h1 class="h3 mb-0">Crear Administrador</h1>

            <h2 class="text-success mb-1">                <p class="text-muted mb-0">Agregar nuevo usuario administrador</p>

                <i class="fas fa-user-plus me-2"></i>Nuevo Administrador            </div>

            </h2>            <nav aria-label="breadcrumb">

            <p class="text-muted mb-0">Completa el formulario para registrar una nueva cuenta administrativa</p>                <ol class="breadcrumb mb-0">

        </div>                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i class="fa fa-dashboard"></i> Inicio</a></li>

        <a href="{{ route('admin.admins.index') }}" class="btn btn-secondary btn-modern">                    <li class="breadcrumb-item"><a href="{{ route('admin.admins.index') }}">Administradores</a></li>

            <i class="fas fa-arrow-left me-2"></i>Volver al listado                    <li class="breadcrumb-item active">Crear</li>

        </a>                </ol>

    </div>            </nav>

        </div>

    <div class="row g-4">    </div>

        <div class="col-xl-8">

            <div class="modern-card">    <div class="row">

                <div class="modern-card-header">        <div class="col-md-8 offset-md-2">

                    <h6 class="mb-0 fw-bold text-success">            <div class="box">

                        <i class="fas fa-id-card me-2"></i>Información del Administrador                <div class="box-header">

                    </h6>                    <h3 class="box-title">Información del Administrador</h3>

                </div>                </div>

                <div class="modern-card-body">

                    <form action="{{ route('admin.admins.store') }}" method="POST" enctype="multipart/form-data" novalidate>                <div class="box-body">

                        @csrf                    <form method="POST" action="{{ route('admin.admins.store') }}" enctype="multipart/form-data">

                        @csrf

                        <div class="row g-3">

                            <div class="col-md-6">                        <div class="row">

                                <label for="firstname" class="form-label fw-semibold text-success">Nombre <span class="text-danger">*</span></label>                            <!-- Información Personal -->

                                <input type="text" id="firstname" name="firstname"                            <div class="col-md-6">

                                       class="form-control form-control-sm @error('firstname') is-invalid @enderror"                                <div class="mb-3">

                                       value="{{ old('firstname') }}" placeholder="Ej: Ana" required>                                    <label for="firstname" class="form-label">Nombre *</label>

                                @error('firstname')                                    <input type="text"

                                    <div class="invalid-feedback">{{ $message }}</div>                                           class="form-control @error('firstname') is-invalid @enderror"

                                @enderror                                           id="firstname"

                            </div>                                           name="firstname"

                            <div class="col-md-6">                                           value="{{ old('firstname') }}"

                                <label for="lastname" class="form-label fw-semibold text-success">Apellido <span class="text-danger">*</span></label>                                           required>

                                <input type="text" id="lastname" name="lastname"                                    @error('firstname')

                                       class="form-control form-control-sm @error('lastname') is-invalid @enderror"                                        <div class="invalid-feedback">{{ $message }}</div>

                                       value="{{ old('lastname') }}" placeholder="Ej: Ramírez" required>                                    @enderror

                                @error('lastname')                                </div>

                                    <div class="invalid-feedback">{{ $message }}</div>                            </div>

                                @enderror

                            </div>                            <div class="col-md-6">

                        </div>                                <div class="mb-3">

                                    <label for="lastname" class="form-label">Apellido *</label>

                        <div class="row g-3 mt-1">                                    <input type="text"

                            <div class="col-md-6">                                           class="form-control @error('lastname') is-invalid @enderror"

                                <label for="username" class="form-label fw-semibold text-success">Nombre de Usuario <span class="text-danger">*</span></label>                                           id="lastname"

                                <input type="text" id="username" name="username"                                           name="lastname"

                                       class="form-control form-control-sm @error('username') is-invalid @enderror"                                           value="{{ old('lastname') }}"

                                       value="{{ old('username') }}" placeholder="admin.gym" required>                                           required>

                                @error('username')                                    @error('lastname')

                                    <div class="invalid-feedback">{{ $message }}</div>                                        <div class="invalid-feedback">{{ $message }}</div>

                                @enderror                                    @enderror

                            </div>                                </div>

                            <div class="col-md-6">                            </div>

                                <label for="email" class="form-label fw-semibold text-success">Correo Electrónico <span class="text-danger">*</span></label>                        </div>

                                <input type="email" id="email" name="email"

                                       class="form-control form-control-sm @error('email') is-invalid @enderror"                        <div class="row">

                                       value="{{ old('email') }}" placeholder="admin@gym.com" required>                            <!-- Información de Cuenta -->

                                @error('email')                            <div class="col-md-6">

                                    <div class="invalid-feedback">{{ $message }}</div>                                <div class="mb-3">

                                @enderror                                    <label for="username" class="form-label">Nombre de Usuario *</label>

                            </div>                                    <input type="text"

                        </div>                                           class="form-control @error('username') is-invalid @enderror"

                                           id="username"

                        <div class="row g-3 mt-1">                                           name="username"

                            <div class="col-md-6">                                           value="{{ old('username') }}"

                                <label for="password" class="form-label fw-semibold text-success">Contraseña <span class="text-danger">*</span></label>                                           required>

                                <input type="password" id="password" name="password"                                    @error('username')

                                       class="form-control form-control-sm @error('password') is-invalid @enderror"                                        <div class="invalid-feedback">{{ $message }}</div>

                                       minlength="6" required>                                    @enderror

                                @error('password')                                </div>

                                    <div class="invalid-feedback">{{ $message }}</div>                            </div>

                                @enderror

                                <small class="text-muted">Mínimo 6 caracteres.</small>                            <div class="col-md-6">

                            </div>                                <div class="mb-3">

                            <div class="col-md-6">                                    <label for="email" class="form-label">Email *</label>

                                <label for="password_confirmation" class="form-label fw-semibold text-success">Confirmar Contraseña <span class="text-danger">*</span></label>                                    <input type="email"

                                <input type="password" id="password_confirmation" name="password_confirmation"                                           class="form-control @error('email') is-invalid @enderror"

                                       class="form-control form-control-sm" required>                                           id="email"

                            </div>                                           name="email"

                        </div>                                           value="{{ old('email') }}"

                                           required>

                        <div class="mt-3">                                    @error('email')

                            <label for="photo" class="form-label fw-semibold text-success">Foto de Perfil</label>                                        <div class="invalid-feedback">{{ $message }}</div>

                            <input type="file" id="photo" name="photo" accept="image/*"                                    @enderror

                                   class="form-control form-control-sm @error('photo') is-invalid @enderror">                                </div>

                            @error('photo')                            </div>

                                <div class="invalid-feedback">{{ $message }}</div>                        </div>

                            @enderror

                            <small class="text-muted">Formatos permitidos: JPG, PNG, GIF (máx. 2MB).</small>                        <div class="row">

                            <!-- Contraseña -->

                            <div class="d-flex align-items-center gap-3 mt-3" id="photoPreview" style="display:none;">                            <div class="col-md-6">

                                <img id="photoPreviewImage" class="rounded-circle shadow" style="width:70px; height:70px; object-fit:cover;" alt="Vista previa">                                <div class="mb-3">

                                <div class="text-muted small">Vista previa de la imagen seleccionada</div>                                    <label for="password" class="form-label">Contraseña *</label>

                            </div>                                    <input type="password"

                        </div>                                           class="form-control @error('password') is-invalid @enderror"

                                           id="password"

                        <div class="d-flex justify-content-end gap-2 mt-4">                                           name="password"

                            <a href="{{ route('admin.admins.index') }}" class="btn btn-outline-secondary btn-modern">                                           required>

                                <i class="fas fa-times me-2"></i>Cancelar                                    @error('password')

                            </a>                                        <div class="invalid-feedback">{{ $message }}</div>

                            <button type="submit" class="btn btn-success btn-modern">                                    @enderror

                                <i class="fas fa-save me-2"></i>Crear Administrador                                </div>

                            </button>                            </div>

                        </div>

                    </form>                            <div class="col-md-6">

                </div>                                <div class="mb-3">

            </div>                                    <label for="password_confirmation" class="form-label">Confirmar Contraseña *</label>

        </div>                                    <input type="password"

                                           class="form-control"

        <div class="col-xl-4">                                           id="password_confirmation"

            <div class="modern-card mb-4">                                           name="password_confirmation"

                <div class="modern-card-header">                                           required>

                    <h6 class="mb-0 fw-bold text-info"><i class="fas fa-shield-alt me-2"></i>Buenas prácticas</h6>                                </div>

                </div>                            </div>

                <div class="modern-card-body">                        </div>

                    <ul class="list-unstyled text-muted small mb-0">

                        <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Utiliza correos corporativos.</li>                        <!-- Foto de Perfil -->

                        <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Asigna contraseñas únicas.</li>                        <div class="mb-3">

                        <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Sube fotos cuadradas para mejor calidad.</li>                            <label for="photo" class="form-label">Foto de Perfil</label>

                        <li><i class="fas fa-check text-success me-2"></i>Documenta los accesos entregados al equipo.</li>                            <input type="file"

                    </ul>                                   class="form-control @error('photo') is-invalid @enderror"

                </div>                                   id="photo"

            </div>                                   name="photo"

                                   accept="image/*"

            <div class="modern-card">                                   onchange="previewImage(event)">

                <div class="modern-card-header">                            @error('photo')

                    <h6 class="mb-0 fw-bold text-secondary"><i class="fas fa-info-circle me-2"></i>Tips rápidos</h6>                                <div class="invalid-feedback">{{ $message }}</div>

                </div>                            @enderror

                <div class="modern-card-body">                            <div class="form-text">Formatos soportados: JPEG, PNG, JPG, GIF. Tamaño máximo: 2MB</div>

                    <p class="small text-muted mb-2"><strong>Usuario:</strong> evita espacios o caracteres extraños.</p>

                    <p class="small text-muted mb-2"><strong>Email:</strong> se usará para alertas internas.</p>                            <!-- Preview de la imagen -->

                    <p class="small text-muted mb-0"><strong>Foto:</strong> ayuda a identificar al staff en la cabecera.</p>                            <div class="mt-3" id="imagePreview" style="display: none;">

                </div>                                <img id="preview" src="" alt="Preview" class="img-thumbnail" style="max-width: 150px; max-height: 150px;">

            </div>                            </div>

        </div>                        </div>

    </div>

</div>                        <div class="d-flex justify-content-between align-items-center">

@endsection                            <a href="{{ route('admin.admins.index') }}" class="btn btn-secondary">

                                <i class="fas fa-arrow-left"></i> Volver

@push('scripts')                            </a>

<script>                            <button type="submit" class="btn btn-primary">

document.addEventListener('DOMContentLoaded', function() {                                <i class="fas fa-save"></i> Crear Administrador

    const photoInput = document.getElementById('photo');                            </button>

    const previewContainer = document.getElementById('photoPreview');                        </div>

    const previewImage = document.getElementById('photoPreviewImage');                    </form>

                </div>

    if (photoInput) {            </div>

        photoInput.addEventListener('change', function(event) {        </div>

            const file = event.target.files[0];    </div>

            if (!file) {@endsection

                previewContainer.style.display = 'none';

                return;@push('scripts')

            }<script>

function previewImage(event) {

            const reader = new FileReader();    const file = event.target.files[0];

            reader.onload = function(e) {    const preview = document.getElementById('preview');

                previewImage.src = e.target.result;    const previewContainer = document.getElementById('imagePreview');

                previewContainer.style.display = 'flex';

            };    if (file) {

            reader.readAsDataURL(file);        const reader = new FileReader();

        });        reader.onload = function(e) {

    }            preview.src = e.target.result;

});            previewContainer.style.display = 'block';

</script>        }

@endpush        reader.readAsDataURL(file);

    } else {
        previewContainer.style.display = 'none';
    }
}
</script>
@endpush
