<div class="table-responsive">
    <table class="table table-hover align-middle">
        <thead class="table-light">
            <tr>
                <th class="fw-semibold text-dark">
                    <i class="fas fa-id-card me-1"></i>ID Empleado
                </th>
                <th class="fw-semibold text-dark">
                    <i class="fas fa-user me-1"></i>Nombre Completo
                </th>
                <th class="fw-semibold text-dark">
                    <i class="fas fa-briefcase me-1"></i>Puesto
                </th>
                <th class="fw-semibold text-dark">
                    <i class="fas fa-clock me-1"></i>Horario
                </th>
                <th class="fw-semibold text-dark">
                    <i class="fas fa-phone me-1"></i>Contacto
                </th>
                <th class="fw-semibold text-dark">
                    <i class="fas fa-map-marker-alt me-1"></i>Dirección
                </th>
                <th class="fw-semibold text-dark text-center">
                    <i class="fas fa-cogs me-1"></i>Acciones
                </th>
            </tr>
        </thead>
        <tbody>
            @forelse($instructors as $instructor)
                <tr class="table-row-hover">
                    <td>
                        <span class="badge bg-primary-subtle text-primary px-2 py-1">
                            {{ $instructor->employee_id ?? 'N/A' }}
                        </span>
                    </td>
                    <td>
                        <div class="d-flex align-items-center">
                            <div class="avatar-circle me-3">
                                {{ strtoupper(substr($instructor->firstname, 0, 1) . substr($instructor->lastname, 0, 1)) }}
                            </div>
                            <div>
                                <div class="fw-semibold text-dark">
                                    {{ $instructor->firstname }} {{ $instructor->lastname }}
                                </div>
                                <small class="text-muted">
                                    Instructor
                                </small>
                            </div>
                        </div>
                    </td>
                    <td>
                        @if($instructor->position)
                            <span class="badge bg-info-subtle text-info px-2 py-1">
                                {{ $instructor->position->description }}
                            </span>
                        @else
                            <span class="text-muted">Sin Puesto</span>
                        @endif
                    </td>
                    <td>
                        @if($instructor->schedule)
                            <div class="d-flex align-items-center">
                                <i class="fas fa-clock me-2 text-primary"></i>
                                <div>
                                    <div class="fw-semibold small">
                                        {{ \Carbon\Carbon::parse($instructor->schedule->time_in)->format('H:i') }} - 
                                        {{ \Carbon\Carbon::parse($instructor->schedule->time_out)->format('H:i') }}
                                    </div>
                                    @php
                                        $timeIn = \Carbon\Carbon::createFromFormat('H:i:s', $instructor->schedule->time_in);
                                        $timeOut = \Carbon\Carbon::createFromFormat('H:i:s', $instructor->schedule->time_out);
                                        $duration = $timeOut->diff($timeIn);
                                    @endphp
                                    <small class="text-muted">{{ $duration->h }}h {{ $duration->i }}m</small>
                                </div>
                            </div>
                        @else
                            <span class="text-muted">
                                <i class="fas fa-exclamation-circle me-1"></i>Sin horario
                            </span>
                        @endif
                    </td>
                    <td>
                        <div>
                            <i class="fas fa-phone-alt me-1 text-success"></i>
                            {{ $instructor->contact_info }}
                        </div>
                    </td>
                    <td>
                        <small class="text-muted">
                            {{ Str::limit($instructor->address, 30) }}
                        </small>
                    </td>
                    <td>
                        <div class="btn-group" role="group">
                            <button type="button" 
                                    class="btn btn-outline-success btn-sm edit-instructor" 
                                    data-id="{{ $instructor->id }}"
                                    title="Editar instructor">
                                <i class="fas fa-edit"></i>
                            </button>
                            
                            <button type="button" 
                                    class="btn btn-outline-danger btn-sm delete-instructor" 
                                    data-id="{{ $instructor->id }}"
                                    data-name="{{ $instructor->firstname }} {{ $instructor->lastname }}"
                                    title="Eliminar instructor">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center py-4">
                        <div class="empty-state">
                            <i class="fas fa-chalkboard-teacher fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No se encontraron instructores</h5>
                            <p class="text-muted mb-0">
                                @if(request('search'))
                                    No hay instructores que coincidan con tu búsqueda.
                                @else
                                    Comienza agregando tu primer instructor.
                                @endif
                            </p>
                        </div>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if($instructors->hasPages())
    <div class="d-flex justify-content-between align-items-center mt-4 px-3">
        <div class="text-muted small">
            Mostrando {{ $instructors->firstItem() ?? 0 }} - {{ $instructors->lastItem() ?? 0 }} 
            de {{ $instructors->total() }} instructores
        </div>
        <div>
            {{ $instructors->links() }}
        </div>
    </div>
@endif

<style>
.avatar-circle {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: 600;
    font-size: 0.875rem;
    text-shadow: 0 1px 2px rgba(0,0,0,0.1);
}

.table-row-hover:hover {
    background-color: rgba(var(--bs-success-rgb), 0.05);
    transform: translateY(-1px);
    transition: all 0.2s ease;
}

.empty-state {
    padding: 3rem 1rem;
}

.btn-group .btn {
    border-radius: 0.375rem;
    margin-right: 0.25rem;
}

.btn-group .btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.badge {
    font-size: 0.75rem;
    padding: 0.375rem 0.75rem;
    border-radius: 0.5rem;
}
</style>