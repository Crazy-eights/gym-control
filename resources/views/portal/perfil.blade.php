@extends('layouts.portal')

@section('title', 'Mi Perfil')

@section('content')
<div class="row">
    <div class="col-lg-4 mb-4">
        <!-- Profile Card -->
        <div class="card">
            <div class="card-header text-center">
                <h5 class="mb-0">
                    <i class="fas fa-user me-2"></i>Mi Perfil
                </h5>
            </div>
            <div class="card-body text-center">
                <div class="mb-3">
                    @if($socio->photo)
                        <img src="{{ asset('storage/' . $socio->photo) }}" 
                             alt="Foto de {{ $socio->full_name }}" 
                             class="profile-avatar">
                    @else
                        <div class="profile-avatar mx-auto d-flex align-items-center justify-content-center bg-light text-success"
                             style="font-size: 2rem;">
                            <i class="fas fa-user"></i>
                        </div>
                    @endif
                </div>
                
                <h4 class="mb-1">{{ $socio->full_name }}</h4>
                <p class="text-muted mb-2">Socio #{{ $socio->member_id }}</p>
                
                <div class="row text-center mt-4">
                    <div class="col-4">
                        <div class="border-end">
                            <h5 class="mb-0 text-primary">
                                {{ \Carbon\Carbon::parse($socio->created_at)->diffInMonths(now()) }}
                            </h5>
                            <small class="text-muted">Meses</small>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="border-end">
                            <h5 class="mb-0 text-success">
                                @switch($socio->status)
                                    @case('activo')
                                        <i class="fas fa-check-circle"></i>
                                        @break
                                    @case('vencido')
                                        <i class="fas fa-times-circle text-danger"></i>
                                        @break
                                    @default
                                        <i class="fas fa-question-circle text-warning"></i>
                                @endswitch
                            </h5>
                            <small class="text-muted">Estado</small>
                        </div>
                    </div>
                    <div class="col-4">
                        <h5 class="mb-0 text-info">
                            {{ $socio->membershipPlan ? '✓' : '✗' }}
                        </h5>
                        <small class="text-muted">Plan</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Info -->
        <div class="card mt-4">
            <div class="card-header">
                <h6 class="mb-0">
                    <i class="fas fa-info-circle me-2"></i>Información Rápida
                </h6>
            </div>
            <div class="card-body">
                <ul class="list-unstyled mb-0">
                    <li class="mb-2">
                        <i class="fas fa-envelope text-primary me-2"></i>
                        <small>{{ $socio->email ?? 'No registrado' }}</small>
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-phone text-success me-2"></i>
                        <small>{{ $socio->contact_info ?? 'No registrado' }}</small>
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-birthday-cake text-warning me-2"></i>
                        <small>{{ $socio->birthdate ? $socio->birthdate->format('d/m/Y') : 'No registrado' }}</small>
                    </li>
                    <li class="mb-0">
                        <i class="fas fa-user-tag text-info me-2"></i>
                        <small>{{ ucfirst($socio->gender ?? 'No especificado') }}</small>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <!-- Navigation Tabs -->
        <ul class="nav nav-tabs" id="profileTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="info-tab" data-bs-toggle="tab" data-bs-target="#info" 
                        type="button" role="tab">
                    <i class="fas fa-user me-2"></i>Información Personal
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="security-tab" data-bs-toggle="tab" data-bs-target="#security" 
                        type="button" role="tab">
                    <i class="fas fa-lock me-2"></i>Seguridad
                </button>
            </li>
        </ul>

        <!-- Tab Content -->
        <div class="tab-content" id="profileTabContent">
            <!-- Personal Information Tab -->
            <div class="tab-pane fade show active" id="info" role="tabpanel">
                <div class="card border-top-0" style="border-top-left-radius: 0; border-top-right-radius: 0;">
                    <div class="card-body">
                        <form action="{{ route('portal.perfil.actualizar') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            
                            <div class="row">
                                <!-- Photo Upload -->
                                <div class="col-12 mb-4">
                                    <label class="form-label">Foto de Perfil</label>
                                    <div class="d-flex align-items-center">
                                        <div class="me-3">
                                            @if($socio->photo)
                                                <img src="{{ asset('storage/' . $socio->photo) }}" 
                                                     alt="Foto actual" 
                                                     class="rounded-circle"
                                                     style="width: 60px; height: 60px; object-fit: cover;">
                                            @else
                                                <div class="rounded-circle bg-light d-flex align-items-center justify-content-center text-muted"
                                                     style="width: 60px; height: 60px;">
                                                    <i class="fas fa-user"></i>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="flex-grow-1">
                                            <input type="file" class="form-control" name="photo" accept="image/*">
                                            <small class="text-muted">Formatos: JPG, PNG. Máximo 2MB.</small>
                                        </div>
                                    </div>
                                </div>

                                <!-- Name Fields -->
                                <div class="col-md-6 mb-3">
                                    <label for="firstname" class="form-label">Nombre *</label>
                                    <input type="text" class="form-control @error('firstname') is-invalid @enderror" 
                                           id="firstname" name="firstname" value="{{ old('firstname', $socio->firstname) }}" required>
                                    @error('firstname')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="lastname" class="form-label">Apellido *</label>
                                    <input type="text" class="form-control @error('lastname') is-invalid @enderror" 
                                           id="lastname" name="lastname" value="{{ old('lastname', $socio->lastname) }}" required>
                                    @error('lastname')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Contact Fields -->
                                <div class="col-md-6 mb-3">
                                    <label for="email" class="form-label">Email *</label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                           id="email" name="email" value="{{ old('email', $socio->email) }}" required>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="contact_info" class="form-label">Teléfono</label>
                                    <input type="text" class="form-control @error('contact_info') is-invalid @enderror" 
                                           id="contact_info" name="contact_info" value="{{ old('contact_info', $socio->contact_info) }}">
                                    @error('contact_info')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Address -->
                                <div class="col-12 mb-3">
                                    <label for="address" class="form-label">Dirección</label>
                                    <textarea class="form-control @error('address') is-invalid @enderror" 
                                              id="address" name="address" rows="2">{{ old('address', $socio->address) }}</textarea>
                                    @error('address')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Personal Info -->
                                <div class="col-md-6 mb-3">
                                    <label for="birthdate" class="form-label">Fecha de Nacimiento</label>
                                    <input type="date" class="form-control @error('birthdate') is-invalid @enderror" 
                                           id="birthdate" name="birthdate" value="{{ old('birthdate', $socio->birthdate ? $socio->birthdate->format('Y-m-d') : '') }}">
                                    @error('birthdate')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="gender" class="form-label">Género</label>
                                    <select class="form-select @error('gender') is-invalid @enderror" id="gender" name="gender">
                                        <option value="">Seleccionar...</option>
                                        <option value="masculino" {{ old('gender', $socio->gender) == 'masculino' ? 'selected' : '' }}>Masculino</option>
                                        <option value="femenino" {{ old('gender', $socio->gender) == 'femenino' ? 'selected' : '' }}>Femenino</option>
                                        <option value="otro" {{ old('gender', $socio->gender) == 'otro' ? 'selected' : '' }}>Otro</option>
                                    </select>
                                    @error('gender')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="d-flex justify-content-end">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i>Guardar Cambios
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Security Tab -->
            <div class="tab-pane fade" id="security" role="tabpanel">
                <div class="card border-top-0" style="border-top-left-radius: 0; border-top-right-radius: 0;">
                    <div class="card-body">
                        <h5 class="mb-4">Cambiar Contraseña</h5>
                        
                        <form action="{{ route('portal.password.cambiar') }}" method="POST">
                            @csrf
                            @method('PUT')
                            
                            <div class="row">
                                <div class="col-12 mb-3">
                                    <label for="current_password" class="form-label">Contraseña Actual *</label>
                                    <input type="password" class="form-control @error('current_password') is-invalid @enderror" 
                                           id="current_password" name="current_password" required>
                                    @error('current_password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="password" class="form-label">Nueva Contraseña *</label>
                                    <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                           id="password" name="password" required>
                                    @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">Mínimo 8 caracteres</small>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="password_confirmation" class="form-label">Confirmar Nueva Contraseña *</label>
                                    <input type="password" class="form-control" 
                                           id="password_confirmation" name="password_confirmation" required>
                                </div>
                            </div>

                            <div class="d-flex justify-content-end">
                                <button type="submit" class="btn btn-warning">
                                    <i class="fas fa-key me-2"></i>Cambiar Contraseña
                                </button>
                            </div>
                        </form>

                        <!-- Security Tips -->
                        <div class="alert alert-info mt-4">
                            <h6><i class="fas fa-shield-alt me-2"></i>Consejos de Seguridad</h6>
                            <ul class="mb-0">
                                <li>Usa una contraseña de al menos 8 caracteres</li>
                                <li>Incluye mayúsculas, minúsculas y números</li>
                                <li>No compartas tu contraseña con nadie</li>
                                <li>Cambia tu contraseña periódicamente</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Photo preview
    const photoInput = document.querySelector('input[name="photo"]');
    if (photoInput) {
        photoInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const img = document.querySelector('.profile-avatar');
                    if (img.tagName === 'IMG') {
                        img.src = e.target.result;
                    } else {
                        // Replace div with img
                        const newImg = document.createElement('img');
                        newImg.src = e.target.result;
                        newImg.className = 'profile-avatar';
                        newImg.style.width = '60px';
                        newImg.style.height = '60px';
                        newImg.style.objectFit = 'cover';
                        img.parentNode.replaceChild(newImg, img);
                    }
                };
                reader.readAsDataURL(file);
            }
        });
    }

    // Password strength indicator
    const passwordInput = document.getElementById('password');
    if (passwordInput) {
        passwordInput.addEventListener('input', function() {
            const password = this.value;
            let strength = 0;
            
            if (password.length >= 8) strength++;
            if (/[a-z]/.test(password)) strength++;
            if (/[A-Z]/.test(password)) strength++;
            if (/[0-9]/.test(password)) strength++;
            if (/[^A-Za-z0-9]/.test(password)) strength++;
            
            // Update visual indicator (you can add a progress bar here)

        });
    }
});
</script>
@endpush