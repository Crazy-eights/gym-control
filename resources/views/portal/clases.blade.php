@extends('layouts.portal-modern')

@section('title', 'Clases')

@section('content')
<!-- Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="mb-1">
            <i class="fas fa-calendar-alt me-2 text-primary"></i>Clases Disponibles
        </h2>
        <p class="text-muted mb-0">Descubre y reserva las clases que más te gusten</p>
    </div>
    <div>
        <button class="btn btn-outline-primary" onclick="viewMyReservations()">
            <i class="fas fa-bookmark me-2"></i>Mis Reservas
        </button>
    </div>
</div>

<!-- Filters -->
<div class="row mb-4">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-3 mb-2 mb-md-0">
                        <select class="form-select" id="filterLevel">
                            <option value="">Todos los niveles</option>
                            <option value="principiante">Principiante</option>
                            <option value="intermedio">Intermedio</option>
                            <option value="avanzado">Avanzado</option>
                        </select>
                    </div>
                    
                    <div class="col-md-3 mb-2 mb-md-0">
                        <select class="form-select" id="filterInstructor">
                            <option value="">Todos los instructores</option>
                            <option value="maria">María González</option>
                            <option value="carlos">Carlos Ruiz</option>
                            <option value="ana">Ana López</option>
                            <option value="laura">Laura Fernández</option>
                        </select>
                    </div>
                    
                    <div class="col-md-3 mb-2 mb-md-0">
                        <select class="form-select" id="filterTime">
                            <option value="">Cualquier horario</option>
                            <option value="morning">Mañana (6:00 - 12:00)</option>
                            <option value="afternoon">Tarde (12:00 - 18:00)</option>
                            <option value="evening">Noche (18:00 - 22:00)</option>
                        </select>
                    </div>
                    
                    <div class="col-md-3">
                        <button class="btn btn-primary w-100" onclick="applyFilters()">
                            <i class="fas fa-filter me-2"></i>Filtrar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <div class="card bg-light">
            <div class="card-body text-center">
                <i class="fas fa-info-circle fa-2x text-primary mb-2"></i>
                <h6 class="mb-1">Reservas Disponibles</h6>
                <p class="small text-muted mb-0">
                    Puedes reservar hasta 3 clases por semana con tu membresía actual
                </p>
            </div>
        </div>
    </div>
</div>

<!-- Classes Grid -->
<div class="row" id="classesGrid">
    @foreach($clases as $clase)
        <div class="col-lg-6 mb-4 class-card" 
             data-level="{{ strtolower($clase->nivel) }}" 
             data-instructor="{{ strtolower(str_replace(' ', '', $clase->instructor)) }}">
            <div class="card h-100 shadow-sm">
                <div class="card-header bg-gradient" style="background: linear-gradient(135deg, #28a745 0%, #007bff 100%);">
                    <div class="d-flex justify-content-between align-items-center text-white">
                        <h5 class="mb-0">{{ $clase->nombre }}</h5>
                        <span class="badge bg-light text-dark">{{ $clase->nivel }}</span>
                    </div>
                </div>
                
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-6">
                            <div class="d-flex align-items-center mb-2">
                                <i class="fas fa-user text-primary me-2"></i>
                                <small class="text-muted">Instructor:</small>
                            </div>
                            <div class="fw-bold">{{ $clase->instructor }}</div>
                        </div>
                        
                        <div class="col-6">
                            <div class="d-flex align-items-center mb-2">
                                <i class="fas fa-clock text-success me-2"></i>
                                <small class="text-muted">Duración:</small>
                            </div>
                            <div class="fw-bold">{{ $clase->duracion }}</div>
                        </div>
                    </div>
                    
                    <p class="text-muted mb-3">{{ $clase->descripcion }}</p>
                    
                    <!-- Schedule -->
                    <div class="mb-3">
                        <div class="d-flex align-items-center mb-2">
                            <i class="fas fa-calendar text-info me-2"></i>
                            <small class="text-muted">Horarios:</small>
                        </div>
                        
                        <div class="d-flex flex-wrap gap-1">
                            @foreach($clase->horarios as $horario)
                                <span class="badge bg-light text-dark border">{{ $horario }}</span>
                            @endforeach
                        </div>
                    </div>
                    
                    <!-- Capacity -->
                    <div class="row mb-3">
                        <div class="col-6">
                            <small class="text-muted">Cupo:</small>
                            <div class="progress" style="height: 8px;">
                                @php
                                    $porcentaje = ($clase->inscritos / $clase->cupo_maximo) * 100;
                                @endphp
                                <div class="progress-bar bg-{{ $porcentaje > 80 ? 'danger' : ($porcentaje > 60 ? 'warning' : 'success') }}" 
                                     style="width: {{ $porcentaje }}%"></div>
                            </div>
                            <small class="text-muted">{{ $clase->inscritos }}/{{ $clase->cupo_maximo }}</small>
                        </div>
                        
                        <div class="col-6 text-end">
                            <small class="text-muted">Disponibles:</small>
                            <div class="fw-bold text-{{ $clase->cupo_maximo - $clase->inscritos > 0 ? 'success' : 'danger' }}">
                                {{ $clase->cupo_maximo - $clase->inscritos }} cupos
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="card-footer bg-transparent">
                    <div class="d-grid gap-2">
                        @if($clase->cupo_maximo - $clase->inscritos > 0)
                            <button class="btn btn-primary" onclick="reserveClass('{{ $clase->nombre }}')">
                                <i class="fas fa-plus me-2"></i>Reservar Clase
                            </button>
                        @else
                            <button class="btn btn-secondary" disabled>
                                <i class="fas fa-times me-2"></i>Sin Cupo Disponible
                            </button>
                        @endif
                        
                        <button class="btn btn-outline-info btn-sm" onclick="viewClassDetails('{{ $clase->nombre }}')">
                            <i class="fas fa-info-circle me-2"></i>Ver Detalles
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
</div>

@if($clases->isEmpty())
    <!-- Empty State -->
    <div class="text-center py-5">
        <i class="fas fa-calendar fa-4x text-muted mb-3"></i>
        <h4 class="text-muted">No hay clases disponibles</h4>
        <p class="text-muted">Las clases aparecerán aquí cuando estén programadas.</p>
    </div>
@endif

<!-- My Reservations Modal -->
<div class="modal fade" id="reservationsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-bookmark me-2"></i>Mis Reservas
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <!-- Upcoming Reservations -->
                <h6 class="text-primary border-bottom pb-2 mb-3">
                    <i class="fas fa-clock me-2"></i>Próximas Clases
                </h6>
                
                <div id="upcomingClasses">
                    <!-- Simulated reservations -->
                    <div class="d-flex align-items-center mb-3 border-bottom pb-3">
                        <div class="flex-shrink-0 me-3">
                            <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center text-white"
                                 style="width: 50px; height: 50px;">
                                <i class="fas fa-dumbbell"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="mb-1">Yoga Matutino</h6>
                            <small class="text-muted d-block">
                                <i class="fas fa-user me-1"></i>María González
                            </small>
                            <small class="text-muted d-block">
                                <i class="fas fa-calendar me-1"></i>Mañana - 08:00 AM
                            </small>
                        </div>
                        <div class="flex-shrink-0">
                            <button class="btn btn-sm btn-outline-danger" onclick="cancelReservation('yoga')">
                                <i class="fas fa-times"></i> Cancelar
                            </button>
                        </div>
                    </div>
                    
                    <div class="d-flex align-items-center mb-3">
                        <div class="flex-shrink-0 me-3">
                            <div class="rounded-circle bg-success d-flex align-items-center justify-content-center text-white"
                                 style="width: 50px; height: 50px;">
                                <i class="fas fa-heartbeat"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="mb-1">CrossFit</h6>
                            <small class="text-muted d-block">
                                <i class="fas fa-user me-1"></i>Carlos Ruiz
                            </small>
                            <small class="text-muted d-block">
                                <i class="fas fa-calendar me-1"></i>Jueves - 19:00 PM
                            </small>
                        </div>
                        <div class="flex-shrink-0">
                            <button class="btn btn-sm btn-outline-danger" onclick="cancelReservation('crossfit')">
                                <i class="fas fa-times"></i> Cancelar
                            </button>
                        </div>
                    </div>
                </div>
                
                <!-- Reservation Limits -->
                <div class="alert alert-info">
                    <h6><i class="fas fa-info-circle me-2"></i>Información de Reservas</h6>
                    <ul class="mb-0">
                        <li>Tienes <strong>2 de 3</strong> reservas utilizadas esta semana</li>
                        <li>Puedes cancelar hasta 2 horas antes de la clase</li>
                        <li>Las reservas se renuevan cada lunes</li>
                    </ul>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" onclick="browseMoreClasses()">
                    <i class="fas fa-search me-2"></i>Buscar Más Clases
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Class Details Modal -->
<div class="modal fade" id="classDetailsModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="classDetailsTitle">
                    <i class="fas fa-info-circle me-2"></i>Detalles de la Clase
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="classDetailsBody">
                <!-- Content will be populated by JavaScript -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" id="reserveFromDetailsBtn">
                    <i class="fas fa-plus me-2"></i>Reservar Esta Clase
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Classes data for filtering and details
const classesData = @json($clases);

function applyFilters() {
    const level = document.getElementById('filterLevel').value.toLowerCase();
    const instructor = document.getElementById('filterInstructor').value;
    const time = document.getElementById('filterTime').value;
    
    const cards = document.querySelectorAll('.class-card');
    
    cards.forEach(card => {
        let show = true;
        
        // Filter by level
        if (level && !card.dataset.level.includes(level)) {
            show = false;
        }
        
        // Filter by instructor
        if (instructor && !card.dataset.instructor.includes(instructor)) {
            show = false;
        }
        
        // Show/hide card
        card.style.display = show ? 'block' : 'none';
    });
    
    // Show message if no results
    const visibleCards = Array.from(cards).filter(card => card.style.display !== 'none');
    const grid = document.getElementById('classesGrid');
    
    // Remove existing no-results message
    const existingMessage = document.getElementById('no-results-message');
    if (existingMessage) {
        existingMessage.remove();
    }
    
    if (visibleCards.length === 0) {
        const noResultsHTML = `
            <div id="no-results-message" class="col-12 text-center py-5">
                <i class="fas fa-search fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">No se encontraron clases</h5>
                <p class="text-muted">Prueba ajustando los filtros para ver más resultados.</p>
                <button class="btn btn-outline-primary" onclick="clearFilters()">
                    <i class="fas fa-times me-2"></i>Limpiar Filtros
                </button>
            </div>
        `;
        grid.insertAdjacentHTML('beforeend', noResultsHTML);
    }
}

function clearFilters() {
    document.getElementById('filterLevel').value = '';
    document.getElementById('filterInstructor').value = '';
    document.getElementById('filterTime').value = '';
    
    const cards = document.querySelectorAll('.class-card');
    cards.forEach(card => {
        card.style.display = 'block';
    });
    
    const noResultsMessage = document.getElementById('no-results-message');
    if (noResultsMessage) {
        noResultsMessage.remove();
    }
}

function reserveClass(className) {
    // Check reservation limits
    const currentReservations = 2; // This would come from the backend
    const maxReservations = 3;
    
    if (currentReservations >= maxReservations) {
        alert('Has alcanzado el límite de ' + maxReservations + ' reservas por semana.\nCancela una reserva existente para agregar una nueva.');
        return;
    }
    
    if (confirm('¿Deseas reservar la clase "' + className + '"?\n\nRecuerda que puedes cancelar hasta 2 horas antes del inicio.')) {
        // Simulate API call
        setTimeout(() => {
            alert('¡Reserva confirmada!\n\nClase: ' + className + '\nRecibirás un recordatorio 30 minutos antes.');
            
            // Update UI (in a real app, this would refresh the data)
            updateReservationCount();
        }, 1000);
    }
}

function viewClassDetails(className) {
    const classData = classesData.find(c => c.nombre === className);
    
    if (classData) {
        document.getElementById('classDetailsTitle').innerHTML = 
            '<i class="fas fa-info-circle me-2"></i>' + classData.nombre;
        
        document.getElementById('classDetailsBody').innerHTML = `
            <div class="row">
                <div class="col-12 mb-3">
                    <h6 class="text-primary">Descripción</h6>
                    <p>${classData.descripcion}</p>
                </div>
                
                <div class="col-md-6 mb-3">
                    <h6 class="text-primary">Instructor</h6>
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center text-white me-3"
                             style="width: 40px; height: 40px;">
                            <i class="fas fa-user"></i>
                        </div>
                        <div>
                            <div class="fw-bold">${classData.instructor}</div>
                            <small class="text-muted">Instructor Certificado</small>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6 mb-3">
                    <h6 class="text-primary">Información</h6>
                    <ul class="list-unstyled">
                        <li><i class="fas fa-clock text-muted me-2"></i>Duración: ${classData.duracion}</li>
                        <li><i class="fas fa-signal text-muted me-2"></i>Nivel: ${classData.nivel}</li>
                        <li><i class="fas fa-users text-muted me-2"></i>Cupo: ${classData.cupo_maximo} personas</li>
                    </ul>
                </div>
                
                <div class="col-12 mb-3">
                    <h6 class="text-primary">Horarios Disponibles</h6>
                    <div class="d-flex flex-wrap gap-2">
                        ${classData.horarios.map(horario => 
                            `<span class="badge bg-light text-dark border">${horario}</span>`
                        ).join('')}
                    </div>
                </div>
                
                <div class="col-12">
                    <h6 class="text-primary">Disponibilidad</h6>
                    <div class="progress mb-2">
                        <div class="progress-bar bg-${classData.inscritos / classData.cupo_maximo > 0.8 ? 'danger' : 'success'}" 
                             style="width: ${(classData.inscritos / classData.cupo_maximo) * 100}%"></div>
                    </div>
                    <small class="text-muted">${classData.inscritos} de ${classData.cupo_maximo} cupos ocupados</small>
                </div>
            </div>
        `;
        
        // Update reserve button
        const reserveBtn = document.getElementById('reserveFromDetailsBtn');
        if (classData.cupo_maximo - classData.inscritos > 0) {
            reserveBtn.style.display = 'inline-block';
            reserveBtn.onclick = () => {
                bootstrap.Modal.getInstance(document.getElementById('classDetailsModal')).hide();
                reserveClass(className);
            };
        } else {
            reserveBtn.style.display = 'none';
        }
        
        const modal = new bootstrap.Modal(document.getElementById('classDetailsModal'));
        modal.show();
    }
}

function viewMyReservations() {
    const modal = new bootstrap.Modal(document.getElementById('reservationsModal'));
    modal.show();
}

function cancelReservation(classType) {
    if (confirm('¿Estás seguro de que quieres cancelar esta reserva?')) {
        alert('Reserva cancelada exitosamente.\n\nAhora tienes un cupo disponible para una nueva reserva.');
        // Update UI
        updateReservationCount();
    }
}

function browseMoreClasses() {
    bootstrap.Modal.getInstance(document.getElementById('reservationsModal')).hide();
    // Scroll to classes grid
    document.getElementById('classesGrid').scrollIntoView({ behavior: 'smooth' });
}

function updateReservationCount() {
    // This would update the reservation counter in a real app

}

// Initialize filters on page load
document.addEventListener('DOMContentLoaded', function() {
    // Add event listeners for real-time filtering
    document.getElementById('filterLevel').addEventListener('change', applyFilters);
    document.getElementById('filterInstructor').addEventListener('change', applyFilters);
    document.getElementById('filterTime').addEventListener('change', applyFilters);
});
</script>
@endpush