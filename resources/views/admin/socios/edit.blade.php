@extends('layouts.admin-modern')

@section('title', 'Editar Socio')
@section('page-title', 'Socios')

@section('content')
<div class="animate-fade-in-up">
    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mb-4">
        <div>
            <h2 class="text-warning mb-1"><i class="fas fa-user-edit me-2"></i>Editar {{ $socio->full_name }}</h2>
            <p class="text-muted mb-0">Actualiza los datos personales y la membresía del socio</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.socios.show', $socio) }}" class="btn btn-outline-dark btn-modern">
                <i class="fas fa-eye me-2"></i>Ver perfil
            </a>
            <a href="{{ route('admin.socios.index') }}" class="btn btn-secondary btn-modern">
                <i class="fas fa-arrow-left me-2"></i>Volver al listado
            </a>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-xl-8">
            <div class="modern-card">
                <div class="modern-card-header">
                    <h6 class="mb-0 fw-bold text-warning"><i class="fas fa-id-card me-2"></i>Datos del Socio</h6>
                </div>
                <div class="modern-card-body">
                    <form action="{{ route('admin.socios.update', $socio) }}" method="POST" enctype="multipart/form-data" id="socioEditForm" novalidate>
                        @csrf
                        @method('PUT')

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="member_id" class="form-label fw-semibold text-warning">ID del Socio <span class="text-danger">*</span></label>
                                <input type="text" id="member_id" name="member_id"
                                       class="form-control form-control-sm @error('member_id') is-invalid @enderror"
                                       value="{{ old('member_id', $socio->member_id) }}" placeholder="Ej: SOC001" required>
                                @error('member_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                <small class="text-muted">No se permiten duplicados.</small>
                            </div>
                            <div class="col-md-6">
                                <label for="photo" class="form-label fw-semibold text-warning">Actualizar Foto</label>
                                <input type="file" id="photo" name="photo" accept="image/*"
                                       class="form-control form-control-sm @error('photo') is-invalid @enderror">
                                @error('photo')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                <small class="text-muted">Deja vacío para mantener la actual.</small>
                            </div>
                        </div>

                        <div class="row g-3 mt-1">
                            <div class="col-md-6">
                                <label for="firstname" class="form-label fw-semibold text-warning">Nombre <span class="text-danger">*</span></label>
                                <input type="text" id="firstname" name="firstname"
                                       class="form-control form-control-sm @error('firstname') is-invalid @enderror"
                                       value="{{ old('firstname', $socio->firstname) }}" placeholder="Nombre" required>
                                @error('firstname')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label for="lastname" class="form-label fw-semibold text-warning">Apellidos <span class="text-danger">*</span></label>
                                <input type="text" id="lastname" name="lastname"
                                       class="form-control form-control-sm @error('lastname') is-invalid @enderror"
                                       value="{{ old('lastname', $socio->lastname) }}" placeholder="Apellidos" required>
                                @error('lastname')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <div class="row g-3 mt-1">
                            <div class="col-md-6">
                                <label for="contact_info" class="form-label fw-semibold text-warning">Contacto <span class="text-danger">*</span></label>
                                <input type="text" id="contact_info" name="contact_info"
                                       class="form-control form-control-sm @error('contact_info') is-invalid @enderror"
                                       value="{{ old('contact_info', $socio->contact_info) }}" placeholder="Teléfono o email" required>
                                @error('contact_info')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label for="gender" class="form-label fw-semibold text-warning">Género <span class="text-danger">*</span></label>
                                <select id="gender" name="gender" class="form-select form-select-sm @error('gender') is-invalid @enderror" required>
                                    <option value="">Seleccionar género</option>
                                    <option value="M" {{ old('gender', $socio->gender) == 'M' ? 'selected' : '' }}>Masculino</option>
                                    <option value="F" {{ old('gender', $socio->gender) == 'F' ? 'selected' : '' }}>Femenino</option>
                                    <option value="Otro" {{ old('gender', $socio->gender) == 'Otro' ? 'selected' : '' }}>Otro</option>
                                </select>
                                @error('gender')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <div class="row g-3 mt-1">
                            <div class="col-md-6">
                                <label for="birthdate" class="form-label fw-semibold text-warning">Fecha de Nacimiento</label>
                                <input type="date" id="birthdate" name="birthdate"
                                       class="form-control form-control-sm @error('birthdate') is-invalid @enderror"
                                       value="{{ old('birthdate', optional($socio->birthdate)->format('Y-m-d')) }}">
                                @error('birthdate')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label for="address" class="form-label fw-semibold text-warning">Dirección</label>
                                <input type="text" id="address" name="address"
                                       class="form-control form-control-sm @error('address') is-invalid @enderror"
                                       value="{{ old('address', $socio->address) }}" placeholder="Dirección completa">
                                @error('address')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <div class="section-divider my-4">
                            <span class="text-muted text-uppercase small"><i class="fas fa-calendar-alt me-2 text-warning"></i>Membresía</span>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-8">
                                <label for="plan_id" class="form-label fw-semibold text-warning">Plan de Membresía</label>
                                <select id="plan_id" name="plan_id" class="form-select form-select-sm @error('plan_id') is-invalid @enderror">
                                    <option value="">Sin plan asignado</option>
                                    @foreach($planes as $plan)
                                        <option value="{{ $plan->id }}" {{ old('plan_id', $socio->plan_id) == $plan->id ? 'selected' : '' }}
                                                data-duration="{{ $plan->duration_days }}" data-price="{{ number_format($plan->price, 2) }}">
                                            {{ $plan->plan_name }} - ${{ number_format($plan->price, 2) }} ({{ $plan->duration_days }} días)
                                        </option>
                                    @endforeach
                                </select>
                                @error('plan_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold text-warning">Resumen</label>
                                <div class="bg-light rounded p-2 small" id="planSummary">Selecciona un plan para ver detalles.</div>
                            </div>
                        </div>

                        <div class="row g-3 mt-1" id="membership-dates" style="display: {{ $socio->plan_id ? 'flex' : 'none' }};">
                            <div class="col-md-6">
                                <label for="subscription_start_date" class="form-label fw-semibold text-warning">Fecha de Inicio</label>
                                <input type="date" id="subscription_start_date" name="subscription_start_date"
                                       class="form-control form-control-sm @error('subscription_start_date') is-invalid @enderror"
                                       value="{{ old('subscription_start_date', optional($socio->subscription_start_date)->format('Y-m-d')) }}">
                                @error('subscription_start_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label for="subscription_end_date" class="form-label fw-semibold text-warning">Fecha de Vencimiento</label>
                                <input type="date" id="subscription_end_date" name="subscription_end_date"
                                       class="form-control form-control-sm @error('subscription_end_date') is-invalid @enderror"
                                       value="{{ old('subscription_end_date', optional($socio->subscription_end_date)->format('Y-m-d')) }}" readonly>
                                @error('subscription_end_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                <small class="text-muted">Calculado según el plan asignado.</small>
                            </div>
                        </div>

                        <div class="row mt-4" id="photo-preview" style="display: none;">
                            <div class="col-12">
                                <div class="alert alert-light d-flex align-items-center gap-3 mb-0">
                                    <img id="preview-img" class="rounded-circle shadow" style="width: 80px; height: 80px; object-fit: cover;" alt="Vista previa">
                                    <div>
                                        <h6 class="mb-1">Vista previa de la nueva foto</h6>
                                        <p class="text-muted small mb-0">Se mostrará en el perfil del socio.</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2 mt-4">
                            <a href="{{ route('admin.socios.show', $socio) }}" class="btn btn-outline-secondary btn-modern">
                                <i class="fas fa-times me-2"></i>Cancelar
                            </a>
                            <button type="submit" class="btn btn-warning btn-modern">
                                <i class="fas fa-save me-2"></i>Guardar cambios
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-xl-4">
            <div class="modern-card mb-4">
                <div class="modern-card-header d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-bold text-secondary"><i class="fas fa-user-circle me-2"></i>Perfil actual</h6>
                    <span class="badge bg-{{ $socio->is_active ? 'success' : 'secondary' }}">{{ $socio->is_active ? 'Activo' : 'Inactivo' }}</span>
                </div>
                <div class="modern-card-body text-center">
                    <div class="position-relative d-inline-block mb-3">
                        <img src="{{ $socio->photo ? asset('storage/' . $socio->photo) : asset('images/default-avatar.png') }}"
                             alt="Foto actual" class="rounded-circle shadow"
                             style="width: 110px; height: 110px; object-fit: cover;">
                        <span class="position-absolute bottom-0 end-0 translate-middle p-2 bg-warning border border-white rounded-circle">
                            <i class="fas fa-pen text-white small"></i>
                        </span>
                    </div>
                    <h5 class="mb-1">{{ $socio->full_name }}</h5>
                    <p class="text-muted small mb-3">ID: {{ $socio->member_id }}</p>
                    <div class="d-flex justify-content-between text-muted small">
                        <span><i class="fas fa-phone me-2 text-warning"></i>{{ $socio->contact_info }}</span>
                    </div>
                    <div class="d-flex justify-content-between text-muted small mt-2">
                        <span><i class="fas fa-map-marker-alt me-2 text-warning"></i>{{ $socio->address ?? 'Sin dirección' }}</span>
                    </div>
                </div>
            </div>

            <div class="modern-card">
                <div class="modern-card-header">
                    <h6 class="mb-0 fw-bold text-info"><i class="fas fa-info-circle me-2"></i>Estado de la membresía</h6>
                </div>
                <div class="modern-card-body">
                    @if($socio->plan)
                        <p class="fw-semibold text-info mb-1">{{ $socio->plan->plan_name }}</p>
                        <p class="text-muted small mb-2">Vence: {{ optional($socio->subscription_end_date)->format('d/m/Y') ?? 'Sin fecha' }}</p>
                        <p class="text-muted small mb-0">Duración: {{ $socio->plan->duration_days }} días · Precio: ${{ number_format($socio->plan->price, 2) }}</p>
                    @else
                        <p class="text-muted mb-0">Sin plan asignado actualmente.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>

    const planSelect = document.getElementById('plan_id');
    const planSummary = document.getElementById('planSummary');
    const membershipDates = document.getElementById('membership-dates');
    const startDateInput = document.getElementById('subscription_start_date');
    const endDateInput = document.getElementById('subscription_end_date');
    const photoInput = document.getElementById('photo');
    const photoPreview = document.getElementById('photo-preview');
    const previewImg = document.getElementById('preview-img');

    function updateSummary(option) {
        if (!option || !option.value) {
            planSummary.textContent = 'Selecciona un plan para ver detalles.';
            return;
        }
        const duration = option.getAttribute('data-duration');
        const price = option.getAttribute('data-price');
        planSummary.innerHTML = `<strong>${option.text}</strong><br><span class="text-muted">Duración: ${duration} días · Precio: $${price}</span>`;
    }

    function calculateEndDate() {
        const selected = planSelect.options[planSelect.selectedIndex];
        const duration = parseInt(selected?.getAttribute('data-duration'));
        const startDate = startDateInput.value;
        if (!duration || !startDate) {
            endDateInput.value = '';
            return;
        }
        const start = new Date(startDate);
        start.setDate(start.getDate() + duration);
        endDateInput.value = start.toISOString().split('T')[0];
    }

    planSelect.addEventListener('change', function() {
        const option = this.options[this.selectedIndex];
        if (option.value) {
            membershipDates.style.display = 'flex';
            updateSummary(option);
            if (!startDateInput.value) {
                startDateInput.value = new Date().toISOString().split('T')[0];
            }
            calculateEndDate();
        } else {
            membershipDates.style.display = 'none';
            planSummary.textContent = 'Selecciona un plan para ver detalles.';
            endDateInput.value = '';
        }
    });

    startDateInput.addEventListener('change', calculateEndDate);

    photoInput.addEventListener('change', function(event) {
        const file = event.target.files[0];
        if (!file) {
            photoPreview.style.display = 'none';
            return;
        }
        const reader = new FileReader();
        reader.onload = function(e) {
            previewImg.src = e.target.result;
            photoPreview.style.display = 'block';
        };
        reader.readAsDataURL(file);
    });

    if (planSelect.value) {
        updateSummary(planSelect.options[planSelect.selectedIndex]);
        membershipDates.style.display = 'flex';
        if (startDateInput.value) {
            calculateEndDate();
        }
    }
});
</script>
@endpush
