@extends('layouts.admin')

@section('title', 'Crear Nuevo Plan de Membresía')

@section('content')
<div class="container-fluid">
    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Crear Nuevo Plan de Membresía</h1>
            <p class="mb-0 text-muted">Completa la información para crear un nuevo plan</p>
        </div>
        <a href="{{ route('admin.membership-plans.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Volver al listado
        </a>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <!-- Formulario Principal -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Información del Plan</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.membership-plans.store') }}" method="POST">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label for="plan_name" class="form-label">Nombre del Plan <span class="text-danger">*</span></label>
                                <input type="text" 
                                       class="form-control @error('plan_name') is-invalid @enderror" 
                                       id="plan_name" 
                                       name="plan_name" 
                                       value="{{ old('plan_name') }}" 
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
                                           value="{{ old('price') }}" 
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
                                       value="{{ old('duration_days') }}" 
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
                                          required>{{ old('description') }}</textarea>
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
                                        <i class="fas fa-save"></i> Guardar Plan
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Panel de Ayuda -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-info">Consejos para Crear Planes</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <h6 class="text-primary"><i class="fas fa-lightbulb"></i> Nombres Sugeridos</h6>
                        <ul class="list-unstyled">
                            <li>• Membresía Básica</li>
                            <li>• Plan Premium</li>
                            <li>• Acceso VIP</li>
                            <li>• Pase Estudiantil</li>
                        </ul>
                    </div>

                    <div class="mb-3">
                        <h6 class="text-success"><i class="fas fa-clock"></i> Duraciones Comunes</h6>
                        <ul class="list-unstyled">
                            <li>• 1 día = Pase diario</li>
                            <li>• 7 días = Semanal</li>
                            <li>• 30 días = Mensual</li>
                            <li>• 90 días = Trimestral</li>
                            <li>• 365 días = Anual</li>
                        </ul>
                    </div>

                    <div class="mb-3">
                        <h6 class="text-warning"><i class="fas fa-exclamation-triangle"></i> Importante</h6>
                        <p class="small text-muted">
                            Una vez creado el plan, podrás editarlo, pero ten en cuenta que los cambios 
                            pueden afectar a los miembros actuales con este plan asignado.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Vista Previa del Plan -->
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-secondary">Vista Previa</h6>
                </div>
                <div class="card-body">
                    <div class="text-center">
                        <h5 id="preview-name" class="card-title text-primary">Nombre del Plan</h5>
                        <h3 id="preview-price" class="text-success mb-2">$0.00</h3>
                        <p id="preview-duration" class="text-muted mb-3">Duración no especificada</p>
                        <p id="preview-description" class="card-text text-muted small">
                            La descripción aparecerá aquí...
                        </p>
                    </div>
                </div>
            </div>
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