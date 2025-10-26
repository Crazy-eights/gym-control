@extends('layouts.admin')

@section('title', 'Editar Plan de Membresía')

@section('content')
<div class="container-fluid">
    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Editar Plan: {{ $membershipPlan->plan_name }}</h1>
            <p class="mb-0 text-muted">Modifica la información del plan de membresía</p>
        </div>
        <div>
            <a href="{{ route('admin.membership-plans.show', $membershipPlan) }}" class="btn btn-info me-2">
                <i class="fas fa-eye"></i> Ver Detalles
            </a>
            <a href="{{ route('admin.membership-plans.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Volver al listado
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <!-- Formulario Principal -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Información del Plan</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.membership-plans.update', $membershipPlan) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label for="plan_name" class="form-label">Nombre del Plan <span class="text-danger">*</span></label>
                                <input type="text" 
                                       class="form-control @error('plan_name') is-invalid @enderror" 
                                       id="plan_name" 
                                       name="plan_name" 
                                       value="{{ old('plan_name', $membershipPlan->plan_name) }}" 
                                       placeholder="Ej: Membresía Premium Mensual"
                                       required>
                                @error('plan_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="price" class="form-label">Precio <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" 
                                           class="form-control @error('price') is-invalid @enderror" 
                                           id="price" 
                                           name="price" 
                                           value="{{ old('price', $membershipPlan->price) }}" 
                                           placeholder="0.00"
                                           step="0.01"
                                           min="0"
                                           max="999999.99"
                                           required>
                                </div>
                                @error('price')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="duration_days" class="form-label">Duración (días) <span class="text-danger">*</span></label>
                                <input type="number" 
                                       class="form-control @error('duration_days') is-invalid @enderror" 
                                       id="duration_days" 
                                       name="duration_days" 
                                       value="{{ old('duration_days', $membershipPlan->duration_days) }}" 
                                       placeholder="30"
                                       min="1"
                                       max="3650"
                                       required>
                                @error('duration_days')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">
                                    <span id="duration-helper">Equivale a: <span class="fw-bold">-</span></span>
                                </small>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label for="description" class="form-label">Descripción <span class="text-danger">*</span></label>
                                <textarea class="form-control @error('description') is-invalid @enderror" 
                                          id="description" 
                                          name="description" 
                                          rows="4" 
                                          placeholder="Describe los beneficios y características de este plan..."
                                          required>{{ old('description', $membershipPlan->description) }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">
                                    <span id="char-count">0</span>/1000 caracteres
                                </small>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <div class="d-flex justify-content-end gap-2">
                                    <a href="{{ route('admin.membership-plans.index') }}" class="btn btn-secondary">
                                        <i class="fas fa-times"></i> Cancelar
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i> Actualizar Plan
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Información Actual -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-info">Información Actual</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <h6 class="text-primary">{{ $membershipPlan->plan_name }}</h6>
                        <p class="text-success h5">${{ number_format($membershipPlan->price, 2) }}</p>
                        <p class="text-muted mb-2">
                            @if($membershipPlan->duration_days <= 7)
                                {{ $membershipPlan->duration_days }} día(s)
                            @elseif($membershipPlan->duration_days <= 31)
                                {{ round($membershipPlan->duration_days/7, 1) }} semana(s)
                            @elseif($membershipPlan->duration_days <= 93)
                                {{ round($membershipPlan->duration_days/30, 1) }} mes(es)
                            @else
                                {{ round($membershipPlan->duration_days/365, 1) }} año(s)
                            @endif
                        </p>
                        <p class="card-text text-muted small">
                            {{ $membershipPlan->description }}
                        </p>
                    </div>

                    <div class="border-top pt-3">
                        <h6 class="text-secondary">Estadísticas</h6>
                        <ul class="list-unstyled">
                            <li><strong>Miembros activos:</strong> {{ $membershipPlan->members()->count() }}</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Vista Previa de Cambios -->
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-warning">Vista Previa de Cambios</h6>
                </div>
                <div class="card-body">
                    <div class="text-center">
                        <h5 id="preview-name" class="card-title text-primary">{{ $membershipPlan->plan_name }}</h5>
                        <h3 id="preview-price" class="text-success mb-2">${{ number_format($membershipPlan->price, 2) }}</h3>
                        <p id="preview-duration" class="text-muted mb-3">-</p>
                        <p id="preview-description" class="card-text text-muted small">
                            {{ $membershipPlan->description }}
                        </p>
                    </div>
                </div>
            </div>

            @if($membershipPlan->members()->count() > 0)
            <!-- Advertencia -->
            <div class="alert alert-warning mt-3" role="alert">
                <h6 class="alert-heading"><i class="fas fa-exclamation-triangle"></i> Importante</h6>
                <p class="mb-0">Este plan tiene {{ $membershipPlan->members()->count() }} miembro(s) asignado(s). 
                Los cambios en precio y duración no afectarán a las membresías existentes, solo a las nuevas.</p>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const planNameInput = document.getElementById('plan_name');
    const priceInput = document.getElementById('price');
    const durationInput = document.getElementById('duration_days');
    const descriptionInput = document.getElementById('description');
    
    const previewName = document.getElementById('preview-name');
    const previewPrice = document.getElementById('preview-price');
    const previewDuration = document.getElementById('preview-duration');
    const previewDescription = document.getElementById('preview-description');
    
    const durationHelper = document.getElementById('duration-helper');
    const charCount = document.getElementById('char-count');

    // Función para calcular la duración equivalente
    function calculateDurationEquivalent(days) {
        if (!days || days <= 0) return '-';
        
        if (days === 1) return '1 día';
        if (days <= 7) return `${days} días`;
        if (days <= 31) {
            const weeks = Math.round(days / 7 * 10) / 10;
            return weeks === 1 ? '1 semana' : `${weeks} semanas`;
        }
        if (days <= 93) {
            const months = Math.round(days / 30 * 10) / 10;
            return months === 1 ? '1 mes' : `${months} meses`;
        }
        const years = Math.round(days / 365 * 10) / 10;
        return years === 1 ? '1 año' : `${years} años`;
    }

    // Actualizar vista previa
    function updatePreview() {
        previewName.textContent = planNameInput.value || 'Nombre del Plan';
        previewPrice.textContent = '$' + (priceInput.value ? parseFloat(priceInput.value).toFixed(2) : '0.00');
        
        const days = parseInt(durationInput.value);
        const equivalent = calculateDurationEquivalent(days);
        previewDuration.textContent = equivalent !== '-' ? equivalent : 'Duración no especificada';
        durationHelper.innerHTML = 'Equivale a: <span class="fw-bold">' + equivalent + '</span>';
        
        previewDescription.textContent = descriptionInput.value || 'La descripción aparecerá aquí...';
    }

    // Contador de caracteres
    function updateCharCount() {
        const count = descriptionInput.value.length;
        charCount.textContent = count;
        charCount.className = count > 1000 ? 'text-danger' : 'text-muted';
    }

    // Event listeners
    planNameInput.addEventListener('input', updatePreview);
    priceInput.addEventListener('input', updatePreview);
    durationInput.addEventListener('input', updatePreview);
    descriptionInput.addEventListener('input', function() {
        updatePreview();
        updateCharCount();
    });

    // Inicializar
    updatePreview();
    updateCharCount();

    // Validación en tiempo real
    priceInput.addEventListener('blur', function() {
        if (this.value && parseFloat(this.value) < 0) {
            this.value = 0;
            updatePreview();
        }
    });

    durationInput.addEventListener('blur', function() {
        if (this.value && parseInt(this.value) < 1) {
            this.value = 1;
            updatePreview();
        }
    });
});
</script>
@endsection