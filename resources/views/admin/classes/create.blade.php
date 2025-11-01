@extends('layouts.admin')

@section('title', 'Nueva Clase')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0 text-gray-800">
                    <i class="fas fa-plus"></i> Nueva Clase
                </h1>
                <a href="{{ route('admin.classes.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Volver a Clases
                </a>
            </div>

            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <h6><i class="fas fa-exclamation-circle"></i> Errores de validación:</h6>
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="close" data-dismiss="alert">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            <form action="{{ route('admin.classes.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                <div class="row">
                    <!-- Información Básica -->
                    <div class="col-lg-8">
                        <div class="card shadow mb-4">
                            <div class="card-header py-3">
                                <h6 class="m-0 font-weight-bold text-primary">
                                    <i class="fas fa-info-circle"></i> Información de la Clase
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="name" class="form-label">
                                                <i class="fas fa-dumbbell text-primary"></i> Nombre de la Clase *
                                            </label>
                                            <input type="text" 
                                                   class="form-control @error('name') is-invalid @enderror" 
                                                   id="name" 
                                                   name="name" 
                                                   value="{{ old('name') }}" 
                                                   placeholder="Ej: Yoga Matutino, CrossFit Avanzado"
                                                   required>
                                            @error('name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="instructor_name" class="form-label">
                                                <i class="fas fa-user-tie text-primary"></i> Instructor *
                                            </label>
                                            <input type="text" 
                                                   class="form-control @error('instructor_name') is-invalid @enderror" 
                                                   id="instructor_name" 
                                                   name="instructor_name" 
                                                   value="{{ old('instructor_name') }}" 
                                                   placeholder="Nombre del instructor"
                                                   required>
                                            @error('instructor_name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="description" class="form-label">
                                        <i class="fas fa-align-left text-primary"></i> Descripción
                                    </label>
                                    <textarea class="form-control @error('description') is-invalid @enderror" 
                                              id="description" 
                                              name="description" 
                                              rows="3" 
                                              placeholder="Describe en qué consiste la clase, beneficios, requisitos, etc.">{{ old('description') }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="duration_minutes" class="form-label">
                                                <i class="fas fa-clock text-primary"></i> Duración (min) *
                                            </label>
                                            <input type="number" 
                                                   class="form-control @error('duration_minutes') is-invalid @enderror" 
                                                   id="duration_minutes" 
                                                   name="duration_minutes" 
                                                   value="{{ old('duration_minutes', 60) }}" 
                                                   min="15" 
                                                   max="240" 
                                                   required>
                                            @error('duration_minutes')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="max_participants" class="form-label">
                                                <i class="fas fa-users text-primary"></i> Capacidad máx. *
                                            </label>
                                            <input type="number" 
                                                   class="form-control @error('max_participants') is-invalid @enderror" 
                                                   id="max_participants" 
                                                   name="max_participants" 
                                                   value="{{ old('max_participants', 20) }}" 
                                                   min="1" 
                                                   max="100" 
                                                   required>
                                            @error('max_participants')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="price" class="form-label">
                                                <i class="fas fa-dollar-sign text-primary"></i> Precio *
                                            </label>
                                            <input type="number" 
                                                   class="form-control @error('price') is-invalid @enderror" 
                                                   id="price" 
                                                   name="price" 
                                                   value="{{ old('price', 0) }}" 
                                                   step="0.01" 
                                                   min="0" 
                                                   required>
                                            @error('price')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="difficulty_level" class="form-label">
                                                <i class="fas fa-signal text-primary"></i> Dificultad *
                                            </label>
                                            <select class="form-control @error('difficulty_level') is-invalid @enderror" 
                                                    id="difficulty_level" 
                                                    name="difficulty_level" 
                                                    required>
                                                <option value="">Seleccionar...</option>
                                                <option value="principiante" {{ old('difficulty_level') == 'principiante' ? 'selected' : '' }}>
                                                    🟢 Principiante
                                                </option>
                                                <option value="intermedio" {{ old('difficulty_level') == 'intermedio' ? 'selected' : '' }}>
                                                    🟡 Intermedio
                                                </option>
                                                <option value="avanzado" {{ old('difficulty_level') == 'avanzado' ? 'selected' : '' }}>
                                                    🔴 Avanzado
                                                </option>
                                            </select>
                                            @error('difficulty_level')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Configuración -->
                    <div class="col-lg-4">
                        <div class="card shadow mb-4">
                            <div class="card-header py-3">
                                <h6 class="m-0 font-weight-bold text-primary">
                                    <i class="fas fa-cog"></i> Configuración
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="form-group">
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" 
                                               class="custom-control-input" 
                                               id="active" 
                                               name="active" 
                                               {{ old('active', true) ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="active">
                                            <i class="fas fa-toggle-on text-success"></i> Clase Activa
                                        </label>
                                    </div>
                                    <small class="form-text text-muted">
                                        Las clases inactivas no estarán disponibles para reservas.
                                    </small>
                                </div>

                                <hr>

                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle"></i>
                                    <strong>Nota:</strong> Los horarios de la clase se pueden configurar después de crearla.
                                </div>
                            </div>
                        </div>

                        <!-- Vista Previa -->
                        <div class="card shadow mb-4">
                            <div class="card-header py-3">
                                <h6 class="m-0 font-weight-bold text-primary">
                                    <i class="fas fa-eye"></i> Vista Previa
                                </h6>
                            </div>
                            <div class="card-body">
                                <div id="preview-card" class="card border-left-primary">
                                    <div class="card-body py-2">
                                        <div class="row no-gutters align-items-center">
                                            <div class="col">
                                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                                    <span id="preview-name">Nombre de la Clase</span>
                                                </div>
                                                <div class="h6 mb-0 font-weight-bold text-gray-800">
                                                    <i class="fas fa-user-tie"></i> <span id="preview-instructor">Instructor</span>
                                                </div>
                                                <div class="text-xs text-gray-600 mt-1">
                                                    <i class="fas fa-clock"></i> <span id="preview-duration">60</span> min | 
                                                    <i class="fas fa-users"></i> <span id="preview-capacity">20</span> personas | 
                                                    <strong>$<span id="preview-price">0.00</span></strong>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Botones de Acción -->
                <div class="row">
                    <div class="col-12">
                        <div class="card shadow">
                            <div class="card-body py-3">
                                <div class="d-flex justify-content-between">
                                    <a href="{{ route('admin.classes.index') }}" class="btn btn-secondary">
                                        <i class="fas fa-times"></i> Cancelar
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i> Crear Clase
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Actualizar vista previa en tiempo real
    function updatePreview() {
        $('#preview-name').text($('#name').val() || 'Nombre de la Clase');
        $('#preview-instructor').text($('#instructor_name').val() || 'Instructor');
        $('#preview-duration').text($('#duration_minutes').val() || '60');
        $('#preview-capacity').text($('#max_participants').val() || '20');
        $('#preview-price').text(parseFloat($('#price').val() || 0).toFixed(2));
    }

    // Eventos para actualizar la vista previa
    $('#name, #instructor_name, #duration_minutes, #max_participants, #price').on('input', updatePreview);
    
    // Actualizar al cargar la página
    updatePreview();
});
</script>
@endpush