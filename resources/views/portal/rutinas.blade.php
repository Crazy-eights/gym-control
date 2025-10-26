@extends('layouts.portal')

@section('title', 'Rutinas')

@section('content')
<!-- Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="mb-1">
            <i class="fas fa-list-alt me-2 text-primary"></i>Rutinas de Entrenamiento
        </h2>
        <p class="text-muted mb-0">Encuentra la rutina perfecta para alcanzar tus objetivos</p>
    </div>
    <div>
        <button class="btn btn-outline-success" onclick="viewMyRoutines()">
            <i class="fas fa-star me-2"></i>Mis Favoritas
        </button>
    </div>
</div>

<!-- Filters and Quick Actions -->
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
                        <select class="form-select" id="filterType">
                            <option value="">Todos los tipos</option>
                            <option value="cuerpo completo">Cuerpo Completo</option>
                            <option value="fuerza">Fuerza</option>
                            <option value="cardio">Cardio</option>
                            <option value="flexibilidad">Flexibilidad</option>
                        </select>
                    </div>
                    
                    <div class="col-md-3 mb-2 mb-md-0">
                        <select class="form-select" id="filterDuration">
                            <option value="">Cualquier duración</option>
                            <option value="short">Corta (< 30 min)</option>
                            <option value="medium">Media (30-60 min)</option>
                            <option value="long">Larga (> 60 min)</option>
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
                <i class="fas fa-lightbulb fa-2x text-warning mb-2"></i>
                <h6 class="mb-1">Rutina Personalizada</h6>
                <p class="small text-muted mb-2">¿No encuentras lo que buscas?</p>
                <button class="btn btn-warning btn-sm" onclick="requestCustomRoutine()">
                    <i class="fas fa-plus me-1"></i>Solicitar Rutina
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Routines Grid -->
<div class="row" id="routinesGrid">
    @foreach($rutinas as $rutina)
        <div class="col-lg-6 mb-4 routine-card" 
             data-level="{{ strtolower($rutina->nivel) }}" 
             data-type="{{ strtolower($rutina->tipo) }}"
             data-duration="{{ $rutina->duracion }}">
            <div class="card h-100 shadow-sm">
                <!-- Card Header -->
                <div class="card-header d-flex justify-content-between align-items-center"
                     style="background: linear-gradient(135deg, 
                        @switch($rutina->tipo)
                            @case('Fuerza')
                                #dc3545 0%, #c82333 100%
                                @break
                            @case('Cardio')
                                #fd7e14 0%, #e8600a 100%
                                @break
                            @case('Flexibilidad')
                                #20c997 0%, #1aa085 100%
                                @break
                            @default
                                #007bff 0%, #0056b3 100%
                        @endswitch
                     ); color: white;">
                    <h5 class="mb-0">{{ $rutina->nombre }}</h5>
                    <div class="d-flex align-items-center">
                        <span class="badge bg-light text-dark me-2">{{ $rutina->nivel }}</span>
                        <button class="btn btn-outline-light btn-sm" onclick="addToFavorites('{{ $rutina->nombre }}')">
                            <i class="fas fa-heart"></i>
                        </button>
                    </div>
                </div>
                
                <!-- Card Body -->
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-4 text-center">
                            <div class="border-end">
                                <i class="fas fa-clock fa-lg text-primary mb-1"></i>
                                <div class="small text-muted">Duración</div>
                                <div class="fw-bold">{{ $rutina->duracion }}</div>
                            </div>
                        </div>
                        
                        <div class="col-4 text-center">
                            <div class="border-end">
                                <i class="fas fa-dumbbell fa-lg text-success mb-1"></i>
                                <div class="small text-muted">Tipo</div>
                                <div class="fw-bold">{{ $rutina->tipo }}</div>
                            </div>
                        </div>
                        
                        <div class="col-4 text-center">
                            <i class="fas fa-signal fa-lg text-info mb-1"></i>
                            <div class="small text-muted">Nivel</div>
                            <div class="fw-bold">{{ $rutina->nivel }}</div>
                        </div>
                    </div>
                    
                    <p class="text-muted mb-3">{{ $rutina->descripcion }}</p>
                    
                    <!-- Exercise Preview -->
                    <div class="mb-3">
                        <h6 class="text-primary">
                            <i class="fas fa-list me-2"></i>Ejercicios ({{ count($rutina->ejercicios) }})
                        </h6>
                        
                        <ul class="list-group list-group-flush">
                            @foreach(array_slice($rutina->ejercicios, 0, 3) as $ejercicio)
                                <li class="list-group-item px-0 py-1 border-0">
                                    <small>
                                        <i class="fas fa-check-circle text-success me-2"></i>
                                        {{ $ejercicio }}
                                    </small>
                                </li>
                            @endforeach
                            
                            @if(count($rutina->ejercicios) > 3)
                                <li class="list-group-item px-0 py-1 border-0">
                                    <small class="text-muted">
                                        <i class="fas fa-ellipsis-h me-2"></i>
                                        +{{ count($rutina->ejercicios) - 3 }} ejercicios más
                                    </small>
                                </li>
                            @endif
                        </ul>
                    </div>
                </div>
                
                <!-- Card Footer -->
                <div class="card-footer bg-transparent">
                    <div class="d-grid gap-2">
                        <div class="row">
                            <div class="col-6">
                                <button class="btn btn-primary w-100" onclick="startRoutine('{{ $rutina->nombre }}')">
                                    <i class="fas fa-play me-2"></i>Iniciar
                                </button>
                            </div>
                            <div class="col-6">
                                <button class="btn btn-outline-info w-100" onclick="viewRoutineDetails('{{ $rutina->nombre }}')">
                                    <i class="fas fa-eye me-2"></i>Ver
                                </button>
                            </div>
                        </div>
                        
                        <button class="btn btn-outline-success btn-sm" onclick="customizeRoutine('{{ $rutina->nombre }}')">
                            <i class="fas fa-edit me-2"></i>Personalizar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
</div>

@if($rutinas->isEmpty())
    <!-- Empty State -->
    <div class="text-center py-5">
        <i class="fas fa-list-alt fa-4x text-muted mb-3"></i>
        <h4 class="text-muted">No hay rutinas disponibles</h4>
        <p class="text-muted">Las rutinas aparecerán aquí cuando estén disponibles.</p>
    </div>
@endif

<!-- My Favorites Modal -->
<div class="modal fade" id="favoritesModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-star me-2"></i>Mis Rutinas Favoritas
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="favoritesContent">
                    <div class="text-center py-4">
                        <i class="fas fa-heart fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">No tienes rutinas favoritas</h5>
                        <p class="text-muted">Agrega rutinas a favoritos haciendo clic en el ❤️ de cualquier rutina.</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<!-- Routine Details Modal -->
<div class="modal fade" id="routineDetailsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="routineDetailsTitle">
                    <i class="fas fa-info-circle me-2"></i>Detalles de la Rutina
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="routineDetailsBody">
                <!-- Content will be populated by JavaScript -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-success" id="startFromDetailsBtn">
                    <i class="fas fa-play me-2"></i>Iniciar Rutina
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Routine Timer Modal -->
<div class="modal fade" id="routineTimerModal" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="timerRoutineTitle">
                    <i class="fas fa-play-circle me-2"></i>Rutina en Progreso
                </h5>
            </div>
            <div class="modal-body text-center">
                <!-- Timer Display -->
                <div class="mb-4">
                    <div class="display-4 text-primary" id="routineTimer">00:00</div>
                    <p class="text-muted">Tiempo transcurrido</p>
                </div>
                
                <!-- Current Exercise -->
                <div class="card bg-light mb-3">
                    <div class="card-body">
                        <h6 class="card-title" id="currentExercise">Preparándose...</h6>
                        <p class="card-text" id="exerciseInstructions">La rutina comenzará en breve</p>
                    </div>
                </div>
                
                <!-- Progress -->
                <div class="mb-3">
                    <div class="d-flex justify-content-between mb-1">
                        <span class="small">Progreso</span>
                        <span class="small" id="progressText">0/5</span>
                    </div>
                    <div class="progress">
                        <div class="progress-bar" id="progressBar" style="width: 0%"></div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-warning" onclick="pauseRoutine()">
                    <i class="fas fa-pause me-2"></i>Pausar
                </button>
                <button type="button" class="btn btn-danger" onclick="stopRoutine()">
                    <i class="fas fa-stop me-2"></i>Terminar
                </button>
                <button type="button" class="btn btn-success" onclick="nextExercise()">
                    <i class="fas fa-forward me-2"></i>Siguiente
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Custom Routine Request Modal -->
<div class="modal fade" id="customRoutineModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-plus me-2"></i>Solicitar Rutina Personalizada
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="customRoutineForm">
                    <div class="mb-3">
                        <label for="goal" class="form-label">Objetivo Principal</label>
                        <select class="form-select" id="goal" required>
                            <option value="">Seleccionar objetivo...</option>
                            <option value="perder_peso">Perder peso</option>
                            <option value="ganar_masa">Ganar masa muscular</option>
                            <option value="tonificar">Tonificar</option>
                            <option value="resistencia">Mejorar resistencia</option>
                            <option value="flexibilidad">Aumentar flexibilidad</option>
                            <option value="fuerza">Ganar fuerza</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="experience" class="form-label">Nivel de Experiencia</label>
                        <select class="form-select" id="experience" required>
                            <option value="">Seleccionar nivel...</option>
                            <option value="principiante">Principiante (0-6 meses)</option>
                            <option value="intermedio">Intermedio (6 meses - 2 años)</option>
                            <option value="avanzado">Avanzado (2+ años)</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="timeAvailable" class="form-label">Tiempo disponible por sesión</label>
                        <select class="form-select" id="timeAvailable" required>
                            <option value="">Seleccionar tiempo...</option>
                            <option value="30">30 minutos</option>
                            <option value="45">45 minutos</option>
                            <option value="60">1 hora</option>
                            <option value="90">1.5 horas</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="preferences" class="form-label">Preferencias y restricciones</label>
                        <textarea class="form-control" id="preferences" rows="3" 
                                  placeholder="Ejercicios que prefieres, lesiones, limitaciones, etc."></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" onclick="submitCustomRequest()">
                    <i class="fas fa-paper-plane me-2"></i>Enviar Solicitud
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Routines data for filtering and details
const routinesData = @json($rutinas);
let favorites = JSON.parse(localStorage.getItem('favoriteRoutines') || '[]');
let currentRoutine = null;
let routineTimer = null;
let routineStartTime = null;
let currentExerciseIndex = 0;

function applyFilters() {
    const level = document.getElementById('filterLevel').value.toLowerCase();
    const type = document.getElementById('filterType').value.toLowerCase();
    const duration = document.getElementById('filterDuration').value;
    
    const cards = document.querySelectorAll('.routine-card');
    
    cards.forEach(card => {
        let show = true;
        
        // Filter by level
        if (level && !card.dataset.level.includes(level)) {
            show = false;
        }
        
        // Filter by type
        if (type && !card.dataset.type.includes(type)) {
            show = false;
        }
        
        // Filter by duration
        if (duration) {
            const cardDuration = parseInt(card.dataset.duration);
            switch(duration) {
                case 'short':
                    if (cardDuration >= 30) show = false;
                    break;
                case 'medium':
                    if (cardDuration < 30 || cardDuration > 60) show = false;
                    break;
                case 'long':
                    if (cardDuration <= 60) show = false;
                    break;
            }
        }
        
        card.style.display = show ? 'block' : 'none';
    });
    
    // Show no results message if needed
    const visibleCards = Array.from(cards).filter(card => card.style.display !== 'none');
    updateNoResultsMessage(visibleCards.length === 0);
}

function updateNoResultsMessage(show) {
    const grid = document.getElementById('routinesGrid');
    const existingMessage = document.getElementById('no-results-message');
    
    if (existingMessage) {
        existingMessage.remove();
    }
    
    if (show) {
        const noResultsHTML = `
            <div id="no-results-message" class="col-12 text-center py-5">
                <i class="fas fa-search fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">No se encontraron rutinas</h5>
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
    document.getElementById('filterType').value = '';
    document.getElementById('filterDuration').value = '';
    
    const cards = document.querySelectorAll('.routine-card');
    cards.forEach(card => {
        card.style.display = 'block';
    });
    
    updateNoResultsMessage(false);
}

function addToFavorites(routineName) {
    if (!favorites.includes(routineName)) {
        favorites.push(routineName);
        localStorage.setItem('favoriteRoutines', JSON.stringify(favorites));
        
        // Update UI
        const btn = event.target.closest('button');
        btn.innerHTML = '<i class="fas fa-heart text-danger"></i>';
        btn.classList.remove('btn-outline-light');
        btn.classList.add('btn-light');
        
        showToast('Rutina agregada a favoritos', 'success');
    } else {
        // Remove from favorites
        favorites = favorites.filter(name => name !== routineName);
        localStorage.setItem('favoriteRoutines', JSON.stringify(favorites));
        
        // Update UI
        const btn = event.target.closest('button');
        btn.innerHTML = '<i class="fas fa-heart"></i>';
        btn.classList.remove('btn-light');
        btn.classList.add('btn-outline-light');
        
        showToast('Rutina removida de favoritos', 'info');
    }
}

function viewMyFavorites() {
    const favoritesContent = document.getElementById('favoritesContent');
    
    if (favorites.length === 0) {
        favoritesContent.innerHTML = `
            <div class="text-center py-4">
                <i class="fas fa-heart fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">No tienes rutinas favoritas</h5>
                <p class="text-muted">Agrega rutinas a favoritos haciendo clic en el ❤️ de cualquier rutina.</p>
            </div>
        `;
    } else {
        let favoritesHTML = '';
        favorites.forEach(routineName => {
            const routine = routinesData.find(r => r.nombre === routineName);
            if (routine) {
                favoritesHTML += `
                    <div class="d-flex align-items-center mb-3 border-bottom pb-3">
                        <div class="flex-shrink-0 me-3">
                            <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center text-white"
                                 style="width: 50px; height: 50px;">
                                <i class="fas fa-dumbbell"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="mb-1">${routine.nombre}</h6>
                            <small class="text-muted d-block">
                                <i class="fas fa-clock me-1"></i>${routine.duracion} | 
                                <i class="fas fa-signal me-1"></i>${routine.nivel}
                            </small>
                        </div>
                        <div class="flex-shrink-0">
                            <button class="btn btn-sm btn-primary me-1" onclick="startRoutine('${routine.nombre}')">
                                <i class="fas fa-play"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-danger" onclick="removeFromFavorites('${routine.nombre}')">
                                <i class="fas fa-heart"></i>
                            </button>
                        </div>
                    </div>
                `;
            }
        });
        favoritesContent.innerHTML = favoritesHTML;
    }
    
    const modal = new bootstrap.Modal(document.getElementById('favoritesModal'));
    modal.show();
}

function viewMyRoutines() {
    viewMyFavorites();
}

function removeFromFavorites(routineName) {
    favorites = favorites.filter(name => name !== routineName);
    localStorage.setItem('favoriteRoutines', JSON.stringify(favorites));
    viewMyFavorites();
    showToast('Rutina removida de favoritos', 'info');
}

function viewRoutineDetails(routineName) {
    const routine = routinesData.find(r => r.nombre === routineName);
    
    if (routine) {
        document.getElementById('routineDetailsTitle').innerHTML = 
            '<i class="fas fa-info-circle me-2"></i>' + routine.nombre;
        
        document.getElementById('routineDetailsBody').innerHTML = `
            <div class="row">
                <div class="col-12 mb-3">
                    <h6 class="text-primary">Descripción</h6>
                    <p>${routine.descripcion}</p>
                </div>
                
                <div class="col-md-4 mb-3 text-center">
                    <div class="border-end">
                        <i class="fas fa-clock fa-2x text-primary mb-2"></i>
                        <div class="small text-muted">Duración</div>
                        <div class="fw-bold">${routine.duracion}</div>
                    </div>
                </div>
                
                <div class="col-md-4 mb-3 text-center">
                    <div class="border-end">
                        <i class="fas fa-dumbbell fa-2x text-success mb-2"></i>
                        <div class="small text-muted">Tipo</div>
                        <div class="fw-bold">${routine.tipo}</div>
                    </div>
                </div>
                
                <div class="col-md-4 mb-3 text-center">
                    <i class="fas fa-signal fa-2x text-info mb-2"></i>
                    <div class="small text-muted">Nivel</div>
                    <div class="fw-bold">${routine.nivel}</div>
                </div>
                
                <div class="col-12">
                    <h6 class="text-primary">Ejercicios Incluidos</h6>
                    <ol class="list-group list-group-numbered">
                        ${routine.ejercicios.map(ejercicio => 
                            `<li class="list-group-item">${ejercicio}</li>`
                        ).join('')}
                    </ol>
                </div>
            </div>
        `;
        
        document.getElementById('startFromDetailsBtn').onclick = () => {
            bootstrap.Modal.getInstance(document.getElementById('routineDetailsModal')).hide();
            startRoutine(routineName);
        };
        
        const modal = new bootstrap.Modal(document.getElementById('routineDetailsModal'));
        modal.show();
    }
}

function startRoutine(routineName) {
    currentRoutine = routinesData.find(r => r.nombre === routineName);
    if (!currentRoutine) return;
    
    // Reset variables
    currentExerciseIndex = 0;
    routineStartTime = Date.now();
    
    // Update modal
    document.getElementById('timerRoutineTitle').innerHTML = 
        '<i class="fas fa-play-circle me-2"></i>' + currentRoutine.nombre;
    
    // Start timer
    routineTimer = setInterval(updateTimer, 1000);
    
    // Show first exercise
    showCurrentExercise();
    
    // Show modal
    const modal = new bootstrap.Modal(document.getElementById('routineTimerModal'));
    modal.show();
    
    showToast('¡Rutina iniciada! ¡Dale con todo!', 'success');
}

function updateTimer() {
    if (!routineStartTime) return;
    
    const elapsed = Math.floor((Date.now() - routineStartTime) / 1000);
    const minutes = Math.floor(elapsed / 60);
    const seconds = elapsed % 60;
    
    document.getElementById('routineTimer').textContent = 
        `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
}

function showCurrentExercise() {
    if (!currentRoutine || currentExerciseIndex >= currentRoutine.ejercicios.length) return;
    
    const exercise = currentRoutine.ejercicios[currentExerciseIndex];
    document.getElementById('currentExercise').textContent = exercise;
    document.getElementById('exerciseInstructions').textContent = 
        'Sigue las instrucciones del ejercicio y toma tu tiempo.';
    
    // Update progress
    const progress = ((currentExerciseIndex + 1) / currentRoutine.ejercicios.length) * 100;
    document.getElementById('progressBar').style.width = progress + '%';
    document.getElementById('progressText').textContent = 
        `${currentExerciseIndex + 1}/${currentRoutine.ejercicios.length}`;
}

function nextExercise() {
    currentExerciseIndex++;
    
    if (currentExerciseIndex >= currentRoutine.ejercicios.length) {
        completeRoutine();
    } else {
        showCurrentExercise();
    }
}

function pauseRoutine() {
    if (routineTimer) {
        clearInterval(routineTimer);
        routineTimer = null;
        showToast('Rutina pausada', 'warning');
        
        // Update button to resume
        const pauseBtn = event.target;
        pauseBtn.innerHTML = '<i class="fas fa-play me-2"></i>Reanudar';
        pauseBtn.onclick = resumeRoutine;
    }
}

function resumeRoutine() {
    routineTimer = setInterval(updateTimer, 1000);
    showToast('Rutina reanudada', 'success');
    
    // Update button back to pause
    const resumeBtn = event.target;
    resumeBtn.innerHTML = '<i class="fas fa-pause me-2"></i>Pausar';
    resumeBtn.onclick = pauseRoutine;
}

function stopRoutine() {
    if (confirm('¿Estás seguro de que quieres terminar la rutina?')) {
        if (routineTimer) {
            clearInterval(routineTimer);
            routineTimer = null;
        }
        
        bootstrap.Modal.getInstance(document.getElementById('routineTimerModal')).hide();
        showToast('Rutina terminada', 'info');
        
        // Reset variables
        currentRoutine = null;
        currentExerciseIndex = 0;
        routineStartTime = null;
    }
}

function completeRoutine() {
    if (routineTimer) {
        clearInterval(routineTimer);
        routineTimer = null;
    }
    
    const totalTime = Math.floor((Date.now() - routineStartTime) / 1000);
    const minutes = Math.floor(totalTime / 60);
    
    bootstrap.Modal.getInstance(document.getElementById('routineTimerModal')).hide();
    
    setTimeout(() => {
        alert(`¡Felicidades! 🎉\n\nHas completado la rutina "${currentRoutine.nombre}"\n\nTiempo total: ${minutes} minutos\n\n¡Excelente trabajo!`);
    }, 500);
    
    // Reset variables
    currentRoutine = null;
    currentExerciseIndex = 0;
    routineStartTime = null;
}

function customizeRoutine(routineName) {
    alert(`Próximamente: Personalización de rutinas\n\nPodrás modificar "${routineName}" según tus necesidades específicas.`);
}

function requestCustomRoutine() {
    const modal = new bootstrap.Modal(document.getElementById('customRoutineModal'));
    modal.show();
}

function submitCustomRequest() {
    const form = document.getElementById('customRoutineForm');
    const formData = new FormData(form);
    
    // Validate form
    if (!form.checkValidity()) {
        form.reportValidity();
        return;
    }
    
    // Simulate submission
    bootstrap.Modal.getInstance(document.getElementById('customRoutineModal')).hide();
    
    setTimeout(() => {
        alert('¡Solicitud enviada exitosamente!\n\nNuestro equipo de entrenadores revisará tus requerimientos y te contactará en 24-48 horas con tu rutina personalizada.\n\n¡Gracias por confiar en nosotros!');
        form.reset();
    }, 500);
}

function showToast(message, type) {
    // Create toast notification (simplified version)
    const toast = document.createElement('div');
    toast.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
    toast.style.top = '20px';
    toast.style.right = '20px';
    toast.style.zIndex = '9999';
    toast.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    document.body.appendChild(toast);
    
    setTimeout(() => {
        toast.remove();
    }, 3000);
}

// Initialize favorites on page load
document.addEventListener('DOMContentLoaded', function() {
    // Update favorite buttons based on localStorage
    favorites.forEach(routineName => {
        const cards = document.querySelectorAll('.routine-card');
        cards.forEach(card => {
            const cardTitle = card.querySelector('.card-header h5').textContent;
            if (cardTitle === routineName) {
                const btn = card.querySelector('.btn-outline-light');
                if (btn) {
                    btn.innerHTML = '<i class="fas fa-heart text-danger"></i>';
                    btn.classList.remove('btn-outline-light');
                    btn.classList.add('btn-light');
                }
            }
        });
    });
    
    // Add event listeners for filters
    document.getElementById('filterLevel').addEventListener('change', applyFilters);
    document.getElementById('filterType').addEventListener('change', applyFilters);
    document.getElementById('filterDuration').addEventListener('change', applyFilters);
});
</script>
@endpush