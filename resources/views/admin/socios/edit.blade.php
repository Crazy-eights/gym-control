@extends('layouts.admin')

@section('title', 'Editar Socio')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3">
                    <i class="fas fa-user-edit me-2"></i>
                    Editar Socio: {{ $socio->full_name }}
                </h1>
                <div class="btn-group">
                    <a href="{{ route('admin.socios.show', $socio) }}" class="btn btn-info">
                        <i class="fas fa-eye me-1"></i>Ver Detalles
                    </a>
                    <a href="{{ route('admin.socios.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-1"></i>Volver a la Lista
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Editar Datos del Socio</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.socios.update', $socio) }}" method="POST" enctype="multipart/form-data" id="socioEditForm">
                        @csrf
                        @method('PUT')
                        
                        <!-- Foto actual -->
                        <div class="row mb-4">
                            <div class="col-12 text-center">
                                <h6 class="text-primary">Foto Actual</h6>
                                @if($socio->photo)
                                    <img src="{{ asset('storage/' . $socio->photo) }}" 
                                         alt="Foto actual de {{ $socio->full_name }}" 
                                         class="rounded-circle img-thumbnail mb-2"
                                         style="width: 120px; height: 120px; object-fit: cover;">
                                @else
                                    <div class="rounded-circle bg-secondary d-flex align-items-center justify-content-center mx-auto mb-2" 
                                         style="width: 120px; height: 120px;">
                                        <i class="fas fa-user fa-3x text-white"></i>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Datos Básicos -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="text-primary border-bottom pb-2">
                                    <i class="fas fa-id-card me-2"></i>Datos Básicos
                                </h5>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="member_id" class="form-label">ID del Socio <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('member_id') is-invalid @enderror" 
                                       id="member_id" name="member_id" value="{{ old('member_id', $socio->member_id) }}" 
                                       placeholder="Ej: SOC001" required>
                                @error('member_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="photo" class="form-label">Cambiar Foto</label>
                                <input type="file" class="form-control @error('photo') is-invalid @enderror" 
                                       id="photo" name="photo" accept="image/*">
                                @error('photo')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Dejar vacío para mantener la foto actual</div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="firstname" class="form-label">Nombre <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('firstname') is-invalid @enderror" 
                                       id="firstname" name="firstname" value="{{ old('firstname', $socio->firstname) }}" 
                                       placeholder="Nombre del socio" required>
                                @error('firstname')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="lastname" class="form-label">Apellidos <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('lastname') is-invalid @enderror" 
                                       id="lastname" name="lastname" value="{{ old('lastname', $socio->lastname) }}" 
                                       placeholder="Apellidos del socio" required>
                                @error('lastname')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="contact_info" class="form-label">Información de Contacto <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('contact_info') is-invalid @enderror" 
                                       id="contact_info" name="contact_info" value="{{ old('contact_info', $socio->contact_info) }}" 
                                       placeholder="Teléfono o email" required>
                                @error('contact_info')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="gender" class="form-label">Género <span class="text-danger">*</span></label>
                                <select class="form-select @error('gender') is-invalid @enderror" id="gender" name="gender" required>
                                    <option value="">Seleccionar género</option>
                                    <option value="M" {{ old('gender', $socio->gender) == 'M' ? 'selected' : '' }}>Masculino</option>
                                    <option value="F" {{ old('gender', $socio->gender) == 'F' ? 'selected' : '' }}>Femenino</option>
                                    <option value="Otro" {{ old('gender', $socio->gender) == 'Otro' ? 'selected' : '' }}>Otro</option>
                                </select>
                                @error('gender')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="birthdate" class="form-label">Fecha de Nacimiento</label>
                                <input type="date" class="form-control @error('birthdate') is-invalid @enderror" 
                                       id="birthdate" name="birthdate" value="{{ old('birthdate', $socio->birthdate ? $socio->birthdate->format('Y-m-d') : '') }}">
                                @error('birthdate')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="address" class="form-label">Dirección</label>
                                <input type="text" class="form-control @error('address') is-invalid @enderror" 
                                       id="address" name="address" value="{{ old('address', $socio->address) }}" 
                                       placeholder="Dirección completa">
                                @error('address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Membresía -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="text-primary border-bottom pb-2">
                                    <i class="fas fa-calendar-alt me-2"></i>Membresía
                                </h5>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="plan_id" class="form-label">Plan de Membresía</label>
                                <select class="form-select @error('plan_id') is-invalid @enderror" id="plan_id" name="plan_id">
                                    <option value="">Sin plan asignado</option>
                                    @foreach($planes as $plan)
                                        <option value="{{ $plan->id }}" {{ old('plan_id', $socio->plan_id) == $plan->id ? 'selected' : '' }}
                                                data-duration="{{ $plan->duration_days }}" data-price="${{ number_format($plan->price, 0) }}">
                                            {{ $plan->plan_name }} - ${{ number_format($plan->price, 0) }} ({{ $plan->duration_days }} días)
                                        </option>
                                    @endforeach
                                </select>
                                @error('plan_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3" id="membership-dates" style="{{ $socio->plan_id ? 'display: block;' : 'display: none;' }}">
                            <div class="col-md-6">
                                <label for="subscription_start_date" class="form-label">Fecha de Inicio</label>
                                <input type="date" class="form-control @error('subscription_start_date') is-invalid @enderror" 
                                       id="subscription_start_date" name="subscription_start_date" 
                                       value="{{ old('subscription_start_date', $socio->subscription_start_date ? $socio->subscription_start_date->format('Y-m-d') : '') }}">
                                @error('subscription_start_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="subscription_end_date" class="form-label">Fecha de Vencimiento</label>
                                <input type="date" class="form-control @error('subscription_end_date') is-invalid @enderror" 
                                       id="subscription_end_date" name="subscription_end_date" 
                                       value="{{ old('subscription_end_date', $socio->subscription_end_date ? $socio->subscription_end_date->format('Y-m-d') : '') }}" readonly>
                                @error('subscription_end_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Se calcula automáticamente según el plan seleccionado</div>
                            </div>
                        </div>

                        <!-- Vista previa de la nueva foto -->
                        <div class="row mb-3" id="photo-preview" style="display: none;">
                            <div class="col-12 text-center">
                                <h6>Vista previa de la nueva foto:</h6>
                                <img id="preview-img" src="" alt="Vista previa" class="img-thumbnail rounded-circle" style="max-width: 150px; max-height: 150px;">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <div class="d-flex justify-content-between">
                                    <a href="{{ route('admin.socios.show', $socio) }}" class="btn btn-secondary">
                                        <i class="fas fa-times me-1"></i>Cancelar
                                    </a>
                                    <button type="submit" class="btn btn-warning">
                                        <i class="fas fa-save me-1"></i>Actualizar Datos
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const planSelect = document.getElementById('plan_id');
    const membershipDates = document.getElementById('membership-dates');
    const startDateInput = document.getElementById('subscription_start_date');
    const endDateInput = document.getElementById('subscription_end_date');
    const photoInput = document.getElementById('photo');
    const photoPreview = document.getElementById('photo-preview');
    const previewImg = document.getElementById('preview-img');

    // Manejar cambio de plan
    planSelect.addEventListener('change', function() {
        if (this.value) {
            membershipDates.style.display = 'block';
            calculateEndDate();
        } else {
            membershipDates.style.display = 'none';
            endDateInput.value = '';
        }
    });

    // Calcular fecha de vencimiento
    startDateInput.addEventListener('change', calculateEndDate);

    function calculateEndDate() {
        const planOption = planSelect.options[planSelect.selectedIndex];
        const duration = planOption.getAttribute('data-duration');
        const startDate = startDateInput.value;

        if (duration && startDate) {
            const start = new Date(startDate);
            start.setDate(start.getDate() + parseInt(duration));
            endDateInput.value = start.toISOString().split('T')[0];
        }
    }

    // Vista previa de la foto
    photoInput.addEventListener('change', function() {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                previewImg.src = e.target.result;
                photoPreview.style.display = 'block';
            };
            reader.readAsDataURL(file);
        } else {
            photoPreview.style.display = 'none';
        }
    });
});
</script>
@endpush