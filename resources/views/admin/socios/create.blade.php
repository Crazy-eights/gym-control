@extends('layouts.admin-modern')@extends('layouts.admin')



@section('title', 'Registrar Nuevo Socio')@section('title', 'Registrar Nuevo Socio')

@section('page-title', 'Socios')

@section('content')

@section('content')<div class="container-fluid">

<div class="animate-fade-in-up">    <div class="row">

    <div class="d-flex justify-content-between align-items-center mb-4">        <div class="col-12">

        <div>            <div class="d-flex justify-content-between align-items-center mb-4">

            <h2 class="text-success mb-1"><i class="fas fa-user-plus me-2"></i>Registrar Nuevo Socio</h2>                <h1 class="h3">

            <p class="text-muted mb-0">Ingresa los datos básicos y asigna un plan de membresía</p>                    <i class="fas fa-user-plus me-2"></i>

        </div>                    Registrar Nuevo Socio

        <a href="{{ route('admin.socios.index') }}" class="btn btn-secondary btn-modern">                </h1>

            <i class="fas fa-arrow-left me-2"></i>Volver al listado                <a href="{{ route('admin.socios.index') }}" class="btn btn-secondary">

        </a>                    <i class="fas fa-arrow-left me-1"></i>

    </div>                    Volver a la Lista

                </a>

    <div class="row g-4">            </div>

        <div class="col-xl-8">        </div>

            <div class="modern-card">    </div>

                <div class="modern-card-header">

                    <h6 class="mb-0 fw-bold text-success"><i class="fas fa-id-card me-2"></i>Datos del Socio</h6>    <div class="row">

                </div>        <div class="col-lg-8 mx-auto">

                <div class="modern-card-body">            <div class="card shadow">

                    <form action="{{ route('admin.socios.store') }}" method="POST" enctype="multipart/form-data" id="socioForm" novalidate>                <div class="card-header py-3">

                        @csrf                    <h6 class="m-0 font-weight-bold text-primary">Datos del Socio</h6>

                </div>

                        <div class="row g-3">                <div class="card-body">

                            <div class="col-md-6">                    <form action="{{ route('admin.socios.store') }}" method="POST" enctype="multipart/form-data" id="socioForm">

                                <label for="member_id" class="form-label fw-semibold text-success">ID del Socio <span class="text-danger">*</span></label>                        @csrf

                                <input type="text" id="member_id" name="member_id"

                                       class="form-control form-control-sm @error('member_id') is-invalid @enderror"                        <!-- Datos Básicos -->

                                       value="{{ old('member_id') }}" placeholder="Ej: SOC001" required>                        <div class="row mb-4">

                                @error('member_id')<div class="invalid-feedback">{{ $message }}</div>@enderror                            <div class="col-12">

                                <small class="text-muted">Identificador único para el registro.</small>                                <h5 class="text-primary border-bottom pb-2">

                            </div>                                    <i class="fas fa-id-card me-2"></i>Datos Básicos

                            <div class="col-md-6">                                </h5>

                                <label for="photo" class="form-label fw-semibold text-success">Foto del Socio</label>                            </div>

                                <input type="file" id="photo" name="photo" accept="image/*"                        </div>

                                       class="form-control form-control-sm @error('photo') is-invalid @enderror">

                                @error('photo')<div class="invalid-feedback">{{ $message }}</div>@enderror                        <div class="row mb-3">

                                <small class="text-muted">Formatos: JPG, PNG, GIF. Máx. 2MB.</small>                            <div class="col-md-6">

                            </div>                                <label for="member_id" class="form-label">ID del Socio <span class="text-danger">*</span></label>

                        </div>                                <input type="text" class="form-control @error('member_id') is-invalid @enderror"

                                       id="member_id" name="member_id" value="{{ old('member_id') }}"

                        <div class="row g-3 mt-1">                                       placeholder="Ej: SOC001" required>

                            <div class="col-md-6">                                @error('member_id')

                                <label for="firstname" class="form-label fw-semibold text-success">Nombre <span class="text-danger">*</span></label>                                    <div class="invalid-feedback">{{ $message }}</div>

                                <input type="text" id="firstname" name="firstname"                                @enderror

                                       class="form-control form-control-sm @error('firstname') is-invalid @enderror"                                <div class="form-text">Identificador único del socio</div>

                                       value="{{ old('firstname') }}" placeholder="Nombre" required>                            </div>

                                @error('firstname')<div class="invalid-feedback">{{ $message }}</div>@enderror                            <div class="col-md-6">

                            </div>                                <label for="photo" class="form-label">Foto del Socio</label>

                            <div class="col-md-6">                                <input type="file" class="form-control @error('photo') is-invalid @enderror"

                                <label for="lastname" class="form-label fw-semibold text-success">Apellidos <span class="text-danger">*</span></label>                                       id="photo" name="photo" accept="image/*">

                                <input type="text" id="lastname" name="lastname"                                @error('photo')

                                       class="form-control form-control-sm @error('lastname') is-invalid @enderror"                                    <div class="invalid-feedback">{{ $message }}</div>

                                       value="{{ old('lastname') }}" placeholder="Apellidos" required>                                @enderror

                                @error('lastname')<div class="invalid-feedback">{{ $message }}</div>@enderror                                <div class="form-text">Formatos: JPG, PNG, GIF. Máximo 2MB</div>

                            </div>                            </div>

                        </div>                        </div>



                        <div class="row g-3 mt-1">                        <div class="row mb-3">

                            <div class="col-md-6">                            <div class="col-md-6">

                                <label for="contact_info" class="form-label fw-semibold text-success">Información de Contacto <span class="text-danger">*</span></label>                                <label for="firstname" class="form-label">Nombre <span class="text-danger">*</span></label>

                                <input type="text" id="contact_info" name="contact_info"                                <input type="text" class="form-control @error('firstname') is-invalid @enderror"

                                       class="form-control form-control-sm @error('contact_info') is-invalid @enderror"                                       id="firstname" name="firstname" value="{{ old('firstname') }}"

                                       value="{{ old('contact_info') }}" placeholder="Teléfono o email" required>                                       placeholder="Nombre del socio" required>

                                @error('contact_info')<div class="invalid-feedback">{{ $message }}</div>@enderror                                @error('firstname')

                            </div>                                    <div class="invalid-feedback">{{ $message }}</div>

                            <div class="col-md-6">                                @enderror

                                <label for="gender" class="form-label fw-semibold text-success">Género <span class="text-danger">*</span></label>                            </div>

                                <select id="gender" name="gender" class="form-select form-select-sm @error('gender') is-invalid @enderror" required>                            <div class="col-md-6">

                                    <option value="">Seleccionar género</option>                                <label for="lastname" class="form-label">Apellidos <span class="text-danger">*</span></label>

                                    <option value="M" {{ old('gender') == 'M' ? 'selected' : '' }}>Masculino</option>                                <input type="text" class="form-control @error('lastname') is-invalid @enderror"

                                    <option value="F" {{ old('gender') == 'F' ? 'selected' : '' }}>Femenino</option>                                       id="lastname" name="lastname" value="{{ old('lastname') }}"

                                    <option value="Otro" {{ old('gender') == 'Otro' ? 'selected' : '' }}>Otro</option>                                       placeholder="Apellidos del socio" required>

                                </select>                                @error('lastname')

                                @error('gender')<div class="invalid-feedback">{{ $message }}</div>@enderror                                    <div class="invalid-feedback">{{ $message }}</div>

                            </div>                                @enderror

                        </div>                            </div>

                        </div>

                        <div class="row g-3 mt-1">

                            <div class="col-md-6">                        <div class="row mb-3">

                                <label for="birthdate" class="form-label fw-semibold text-success">Fecha de Nacimiento</label>                            <div class="col-md-6">

                                <input type="date" id="birthdate" name="birthdate"                                <label for="contact_info" class="form-label">Información de Contacto <span class="text-danger">*</span></label>

                                       class="form-control form-control-sm @error('birthdate') is-invalid @enderror"                                <input type="text" class="form-control @error('contact_info') is-invalid @enderror"

                                       value="{{ old('birthdate') }}">                                       id="contact_info" name="contact_info" value="{{ old('contact_info') }}"

                                @error('birthdate')<div class="invalid-feedback">{{ $message }}</div>@enderror                                       placeholder="Teléfono o email" required>

                            </div>                                @error('contact_info')

                            <div class="col-md-6">                                    <div class="invalid-feedback">{{ $message }}</div>

                                <label for="address" class="form-label fw-semibold text-success">Dirección</label>                                @enderror

                                <input type="text" id="address" name="address"                            </div>

                                       class="form-control form-control-sm @error('address') is-invalid @enderror"                            <div class="col-md-6">

                                       value="{{ old('address') }}" placeholder="Dirección completa">                                <label for="gender" class="form-label">Género <span class="text-danger">*</span></label>

                                @error('address')<div class="invalid-feedback">{{ $message }}</div>@enderror                                <select class="form-select @error('gender') is-invalid @enderror" id="gender" name="gender" required>

                            </div>                                    <option value="">Seleccionar género</option>

                        </div>                                    <option value="M" {{ old('gender') == 'M' ? 'selected' : '' }}>Masculino</option>

                                    <option value="F" {{ old('gender') == 'F' ? 'selected' : '' }}>Femenino</option>

                        <div class="section-divider my-4">                                    <option value="Otro" {{ old('gender') == 'Otro' ? 'selected' : '' }}>Otro</option>

                            <span class="text-muted text-uppercase small"><i class="fas fa-calendar-alt me-2 text-success"></i>Membresía</span>                                </select>

                        </div>                                @error('gender')

                                    <div class="invalid-feedback">{{ $message }}</div>

                        <div class="row g-3">                                @enderror

                            <div class="col-md-8">                            </div>

                                <label for="plan_id" class="form-label fw-semibold text-success">Plan de Membresía</label>                        </div>

                                <select id="plan_id" name="plan_id" class="form-select form-select-sm @error('plan_id') is-invalid @enderror">

                                    <option value="">Sin plan asignado</option>                        <div class="row mb-3">

                                    @foreach($planes as $plan)                            <div class="col-md-6">

                                        <option value="{{ $plan->id }}" {{ old('plan_id') == $plan->id ? 'selected' : '' }}                                <label for="birthdate" class="form-label">Fecha de Nacimiento</label>

                                                data-duration="{{ $plan->duration_days }}" data-price="{{ number_format($plan->price, 2) }}">                                <input type="date" class="form-control @error('birthdate') is-invalid @enderror"

                                            {{ $plan->plan_name }} - ${{ number_format($plan->price, 2) }} ({{ $plan->duration_days }} días)                                       id="birthdate" name="birthdate" value="{{ old('birthdate') }}">

                                        </option>                                @error('birthdate')

                                    @endforeach                                    <div class="invalid-feedback">{{ $message }}</div>

                                </select>                                @enderror

                                @error('plan_id')<div class="invalid-feedback">{{ $message }}</div>@enderror                            </div>

                            </div>                            <div class="col-md-6">

                            <div class="col-md-4">                                <label for="address" class="form-label">Dirección</label>

                                <label class="form-label fw-semibold text-success">Resumen</label>                                <input type="text" class="form-control @error('address') is-invalid @enderror"

                                <div class="bg-light rounded p-2 small" id="planSummary">Selecciona un plan para ver detalles.</div>                                       id="address" name="address" value="{{ old('address') }}"

                            </div>                                       placeholder="Dirección completa">

                        </div>                                @error('address')

                                    <div class="invalid-feedback">{{ $message }}</div>

                        <div class="row g-3 mt-1" id="membership-dates" style="display: none;">                                @enderror

                            <div class="col-md-6">                            </div>

                                <label for="subscription_start_date" class="form-label fw-semibold text-success">Fecha de Inicio</label>                        </div>

                                <input type="date" id="subscription_start_date" name="subscription_start_date"

                                       class="form-control form-control-sm @error('subscription_start_date') is-invalid @enderror"                        <!-- Membresía -->

                                       value="{{ old('subscription_start_date', now()->format('Y-m-d')) }}">                        <div class="row mb-4">

                                @error('subscription_start_date')<div class="invalid-feedback">{{ $message }}</div>@enderror                            <div class="col-12">

                            </div>                                <h5 class="text-primary border-bottom pb-2">

                            <div class="col-md-6">                                    <i class="fas fa-calendar-alt me-2"></i>Membresía

                                <label for="subscription_end_date" class="form-label fw-semibold text-success">Fecha de Vencimiento</label>                                </h5>

                                <input type="date" id="subscription_end_date" name="subscription_end_date"                            </div>

                                       class="form-control form-control-sm @error('subscription_end_date') is-invalid @enderror"                        </div>

                                       value="{{ old('subscription_end_date') }}" readonly>

                                @error('subscription_end_date')<div class="invalid-feedback">{{ $message }}</div>@enderror                        <div class="row mb-3">

                                <small class="text-muted">Se calcula automáticamente según la duración del plan.</small>                            <div class="col-md-6">

                            </div>                                <label for="plan_id" class="form-label">Plan de Membresía</label>

                        </div>                                <select class="form-select @error('plan_id') is-invalid @enderror" id="plan_id" name="plan_id">

                                    <option value="">Sin plan asignado</option>

                        <div class="row mt-4" id="photo-preview" style="display: none;">                                    @foreach($planes as $plan)

                            <div class="col-12">                                        <option value="{{ $plan->id }}" {{ old('plan_id') == $plan->id ? 'selected' : '' }}

                                <div class="alert alert-light d-flex align-items-center gap-3 mb-0">                                                data-duration="{{ $plan->duration_days }}" data-price="${{ number_format($plan->price, 0) }}">

                                    <img id="preview-img" class="rounded-circle shadow" style="width: 80px; height: 80px; object-fit: cover;" alt="Vista previa">                                            {{ $plan->plan_name }} - ${{ number_format($plan->price, 0) }} ({{ $plan->duration_days }} días)

                                    <div>                                        </option>

                                        <h6 class="mb-1">Vista previa de la foto</h6>                                    @endforeach

                                        <p class="text-muted small mb-0">Esta será la imagen utilizada en el perfil del socio.</p>                                </select>

                                    </div>                                @error('plan_id')

                                </div>                                    <div class="invalid-feedback">{{ $message }}</div>

                            </div>                                @enderror

                        </div>                            </div>

                        </div>

                        <div class="d-flex justify-content-end gap-2 mt-4">

                            <a href="{{ route('admin.socios.index') }}" class="btn btn-outline-secondary btn-modern">                        <div class="row mb-3" id="membership-dates" style="display: none;">

                                <i class="fas fa-times me-2"></i>Cancelar                            <div class="col-md-6">

                            </a>                                <label for="subscription_start_date" class="form-label">Fecha de Inicio</label>

                            <button type="submit" class="btn btn-success btn-modern">                                <input type="date" class="form-control @error('subscription_start_date') is-invalid @enderror"

                                <i class="fas fa-save me-2"></i>Registrar Socio                                       id="subscription_start_date" name="subscription_start_date" value="{{ old('subscription_start_date', now()->format('Y-m-d')) }}">

                            </button>                                @error('subscription_start_date')

                        </div>                                    <div class="invalid-feedback">{{ $message }}</div>

                    </form>                                @enderror

                </div>                            </div>

            </div>                            <div class="col-md-6">

        </div>                                <label for="subscription_end_date" class="form-label">Fecha de Vencimiento</label>

                                <input type="date" class="form-control @error('subscription_end_date') is-invalid @enderror"

        <div class="col-xl-4">                                       id="subscription_end_date" name="subscription_end_date" value="{{ old('subscription_end_date') }}" readonly>

            <div class="modern-card mb-4">                                @error('subscription_end_date')

                <div class="modern-card-header">                                    <div class="invalid-feedback">{{ $message }}</div>

                    <h6 class="mb-0 fw-bold text-info"><i class="fas fa-lightbulb me-2"></i>Tips de registro</h6>                                @enderror

                </div>                                <div class="form-text">Se calcula automáticamente según el plan seleccionado</div>

                <div class="modern-card-body">                            </div>

                    <ul class="list-unstyled text-muted small mb-0">                        </div>

                        <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Utiliza ID secuenciales para facilitar las búsquedas.</li>

                        <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Completa contacto y dirección para emergencias.</li>                        <!-- Vista previa de la foto -->

                        <li><i class="fas fa-check text-success me-2"></i>Selecciona un plan solo si ya confirmó el pago.</li>                        <div class="row mb-3" id="photo-preview" style="display: none;">

                    </ul>                            <div class="col-12">

                </div>                                <h6>Vista previa de la foto:</h6>

            </div>                                <img id="preview-img" src="" alt="Vista previa" class="img-thumbnail" style="max-width: 200px;">

                            </div>

            <div class="modern-card">                        </div>

                <div class="modern-card-header">

                    <h6 class="mb-0 fw-bold text-secondary"><i class="fas fa-camera me-2"></i>Recomendaciones de foto</h6>                        <div class="row">

                </div>                            <div class="col-12">

                <div class="modern-card-body">                                <div class="d-flex justify-content-between">

                    <p class="text-muted small mb-2">• Fondos lisos ayudan a identificar rápidamente al socio.</p>                                    <a href="{{ route('admin.socios.index') }}" class="btn btn-secondary">

                    <p class="text-muted small mb-2">• Evita fotos borrosas o muy oscuras.</p>                                        <i class="fas fa-times me-1"></i>Cancelar

                    <p class="text-muted small mb-0">• Peso máximo: 2 MB / Resolución recomendada: 600x600.</p>                                    </a>

                </div>                                    <button type="submit" class="btn btn-primary">

            </div>                                        <i class="fas fa-save me-1"></i>Registrar Socio

        </div>                                    </button>

    </div>                                </div>

</div>                            </div>

@endsection                        </div>

                    </form>

@push('scripts')                </div>

<script>            </div>

document.addEventListener('DOMContentLoaded', function() {        </div>

    const planSelect = document.getElementById('plan_id');    </div>

    const planSummary = document.getElementById('planSummary');</div>

    const membershipDates = document.getElementById('membership-dates');@endsection

    const startDateInput = document.getElementById('subscription_start_date');

    const endDateInput = document.getElementById('subscription_end_date');@push('scripts')

    const photoInput = document.getElementById('photo');<script>

    const photoPreview = document.getElementById('photo-preview');document.addEventListener('DOMContentLoaded', function() {

    const previewImg = document.getElementById('preview-img');    const planSelect = document.getElementById('plan_id');

    const membershipDates = document.getElementById('membership-dates');

    function updateSummary(option) {    const startDateInput = document.getElementById('subscription_start_date');

        if (!option || !option.value) {    const endDateInput = document.getElementById('subscription_end_date');

            planSummary.textContent = 'Selecciona un plan para ver detalles.';    const photoInput = document.getElementById('photo');

            return;    const photoPreview = document.getElementById('photo-preview');

        }    const previewImg = document.getElementById('preview-img');

        const duration = option.getAttribute('data-duration');

        const price = option.getAttribute('data-price');    // Manejar cambio de plan

        planSummary.innerHTML = `<strong>${option.text}</strong><br><span class="text-muted">Duración: ${duration} días · Precio: $${price}</span>`;    planSelect.addEventListener('change', function() {

    }        if (this.value) {

            membershipDates.style.display = 'block';

    function calculateEndDate() {            calculateEndDate();

        const selected = planSelect.options[planSelect.selectedIndex];        } else {

        const duration = parseInt(selected?.getAttribute('data-duration'));            membershipDates.style.display = 'none';

        const startDate = startDateInput.value;            endDateInput.value = '';

        if (!duration || !startDate) {        }

            endDateInput.value = '';    });

            return;

        }    // Calcular fecha de vencimiento

        const start = new Date(startDate);    startDateInput.addEventListener('change', calculateEndDate);

        start.setDate(start.getDate() + duration);

        endDateInput.value = start.toISOString().split('T')[0];    function calculateEndDate() {

    }        const planOption = planSelect.options[planSelect.selectedIndex];

        const duration = planOption.getAttribute('data-duration');

    planSelect.addEventListener('change', function() {        const startDate = startDateInput.value;

        const option = this.options[this.selectedIndex];

        if (option.value) {        if (duration && startDate) {

            membershipDates.style.display = 'flex';            const start = new Date(startDate);

            updateSummary(option);            start.setDate(start.getDate() + parseInt(duration));

            if (!startDateInput.value) {            endDateInput.value = start.toISOString().split('T')[0];

                startDateInput.value = new Date().toISOString().split('T')[0];        }

            }    }

            calculateEndDate();

        } else {    // Vista previa de la foto

            membershipDates.style.display = 'none';    photoInput.addEventListener('change', function() {

            planSummary.textContent = 'Selecciona un plan para ver detalles.';        const file = this.files[0];

            endDateInput.value = '';        if (file) {

        }            const reader = new FileReader();

    });            reader.onload = function(e) {

                previewImg.src = e.target.result;

    startDateInput.addEventListener('change', calculateEndDate);                photoPreview.style.display = 'block';

            };

    photoInput.addEventListener('change', function(event) {            reader.readAsDataURL(file);

        const file = event.target.files[0];        } else {

        if (!file) {            photoPreview.style.display = 'none';

            photoPreview.style.display = 'none';        }

            return;    });

        }

        const reader = new FileReader();    // Inicializar si hay un plan seleccionado

        reader.onload = function(e) {    if (planSelect.value) {

            previewImg.src = e.target.result;        membershipDates.style.display = 'block';

            photoPreview.style.display = 'block';        calculateEndDate();

        };    }

        reader.readAsDataURL(file);});

    });</script>

@endpush

    // Inicializar estado según valores antiguos
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
