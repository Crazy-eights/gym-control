@if($classes->count() > 0)
    <div class="table-responsive">
        <table class="table table-modern">
            <thead>
                <tr>
                    <th>Clase</th>
                    <th>Instructor</th>
                    <th>Duración</th>
                    <th>Capacidad</th>
                    <th>Precio</th>
                    <th>Dificultad</th>
                    <th>Horarios</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($classes as $class)
                    <tr>
                        <td>
                            <div class="d-flex flex-column">
                                <strong>{{ $class->name }}</strong>
                                @if($class->description)
                                    <small class="text-muted">{{ Str::limit($class->description, 50) }}</small>
                                @endif
                            </div>
                        </td>
                        <td>
                            <i class="fas fa-user-tie text-primary"></i>
                            {{ $class->instructor_name }}
                        </td>
                        <td>
                            <i class="fas fa-clock text-info"></i>
                            {{ $class->duration_minutes }} min
                        </td>
                        <td>
                            <i class="fas fa-users text-secondary"></i>
                            {{ $class->max_participants }} personas
                        </td>
                        <td>
                            <strong class="text-success">
                                ${{ number_format($class->price, 2) }}
                            </strong>
                        </td>
                        <td>
                            @switch($class->difficulty_level)
                                @case('principiante')
                                    <span class="badge badge-modern badge-success">
                                        <i class="fas fa-star"></i> Principiante
                                    </span>
                                    @break
                                @case('intermedio')
                                    <span class="badge badge-modern badge-warning">
                                        <i class="fas fa-star"></i><i class="fas fa-star"></i> Intermedio
                                    </span>
                                    @break
                                @case('avanzado')
                                    <span class="badge badge-modern badge-danger">
                                        <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i> Avanzado
                                    </span>
                                    @break
                            @endswitch
                        </td>
                        <td>
                            @if($class->schedules->count() > 0)
                                <div class="d-flex flex-wrap">
                                    @foreach($class->schedules->take(3) as $schedule)
                                        <small class="badge badge-modern badge-secondary me-1 mb-1">
                                            {{ ucfirst($schedule->day_of_week) }}
                                            {{ \Carbon\Carbon::parse($schedule->start_time)->format('H:i') }}
                                        </small>
                                    @endforeach
                                    @if($class->schedules->count() > 3)
                                        <small class="text-muted">+{{ $class->schedules->count() - 3 }} más</small>
                                    @endif
                                </div>
                            @else
                                <span class="text-muted">Sin horarios</span>
                            @endif
                        </td>
                        <td>
                            @if($class->active)
                                <span class="badge badge-modern badge-success">
                                    <i class="fas fa-check"></i> Activa
                                </span>
                            @else
                                <span class="badge badge-modern badge-secondary">
                                    <i class="fas fa-pause"></i> Inactiva
                                </span>
                            @endif
                        </td>
                        <td>
                            <div class="action-buttons">
                                <a href="{{ route('admin.classes.show', $class) }}"
                                   class="btn btn-sm btn-outline-success"
                                   title="Ver detalles">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <button type="button"
                                        class="btn btn-sm btn-outline-warning"
                                        title="Editar"
                                        onclick="editClass('{{ $class->id }}', '{{ $class->name }}', '{{ $class->instructor_name }}', '{{ $class->duration_minutes }}', '{{ $class->max_participants }}', '{{ $class->price }}', '{{ $class->difficulty_level }}', '{{ $class->active }}', '{{ addslashes($class->description ?? '') }}')">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button type="button"
                                        class="btn btn-sm btn-outline-danger"
                                        title="Eliminar"
                                        onclick="confirmDelete({{ $class->id }}, '{{ $class->name }}')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Paginación -->
    <div class="d-flex justify-content-center mt-4">
        {{ $classes->links() }}
    </div>
@else
    <div class="empty-state text-center py-5">
        <div class="empty-icon mb-3">
            <i class="fas fa-dumbbell fa-3x text-muted"></i>
        </div>
        <h5 class="mb-2">No se encontraron clases</h5>
        <p class="text-muted mb-3">
            @if(request()->hasAny(['search', 'active', 'price_min', 'price_max']))
                No hay clases que coincidan con los filtros aplicados.
                <a href="{{ route('admin.classes.index') }}" class="text-success">Limpiar filtros</a>
            @else
                Comienza creando tu primera clase del gimnasio.
            @endif
        </p>
        @if(!request()->hasAny(['search', 'active', 'price_min', 'price_max']))
            <button type="button" class="btn btn-success btn-modern" data-bs-toggle="modal" data-bs-target="#createClassModal">
                <i class="fas fa-plus me-2"></i>Crear Primera Clase
            </button>
        @endif
    </div>
@endif