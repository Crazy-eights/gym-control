<div class="table-responsive">
    <table class="table table-hover align-middle">
        <thead class="table-light">
            <tr>
                <th class="fw-semibold text-dark">
                    <i class="fas fa-hashtag me-1"></i>ID
                </th>
                <th class="fw-semibold text-dark">
                    <i class="fas fa-briefcase me-1"></i>Descripción
                </th>
                {{-- Columna de tarifa oculta temporalmente
                <th class="fw-semibold text-dark">
                    <i class="fas fa-dollar-sign me-1"></i>Tarifa/Hora
                </th>
                --}}
                <th class="fw-semibold text-dark">
                    <i class="fas fa-users me-1"></i>Empleados
                </th>
                <th class="fw-semibold text-dark text-center">
                    <i class="fas fa-cogs me-1"></i>Acciones
                </th>
            </tr>
        </thead>
        <tbody>
            @forelse($positions as $position)
                <tr class="table-row-hover">
                    <td>
                        <span class="badge bg-primary-subtle text-primary px-2 py-1">
                            {{ $position->id }}
                        </span>
                    </td>
                    <td>
                        <div class="d-flex align-items-center">
                            <div class="position-icon me-3">
                                <i class="fas fa-briefcase text-success"></i>
                            </div>
                            <div>
                                <div class="fw-semibold text-dark">
                                    {{ $position->description }}
                                </div>
                                <small class="text-muted">
                                    Posición de trabajo
                                </small>
                            </div>
                        </div>
                    </td>
                    {{-- Celda de tarifa oculta temporalmente
                    <td>
                        <div class="d-flex align-items-center">
                            <span class="badge bg-success-subtle text-success px-3 py-2 fw-semibold">
                                ${{ number_format($position->rate, 2) }}
                            </span>
                        </div>
                    </td>
                    --}}
                    <td>
                        @if($position->employees_count > 0)
                            <span class="badge bg-info-subtle text-info px-2 py-1">
                                <i class="fas fa-users me-1"></i>
                                {{ $position->employees_count }} empleado{{ $position->employees_count > 1 ? 's' : '' }}
                            </span>
                        @else
                            <span class="badge bg-light text-muted px-2 py-1">
                                <i class="fas fa-user-plus me-1"></i>
                                Sin asignar
                            </span>
                        @endif
                    </td>
                    <td>
                        <div class="btn-group" role="group">
                            <button type="button" 
                                    class="btn btn-outline-success btn-sm edit-position" 
                                    data-id="{{ $position->id }}"
                                    title="Editar posición">
                                <i class="fas fa-edit"></i>
                            </button>
                            
                            @if($position->employees_count == 0)
                                <button type="button" 
                                        class="btn btn-outline-danger btn-sm delete-position" 
                                        data-id="{{ $position->id }}"
                                        data-name="{{ $position->description }}"
                                        title="Eliminar posición">
                                    <i class="fas fa-trash"></i>
                                </button>
                            @else
                                <button type="button" 
                                        class="btn btn-outline-secondary btn-sm" 
                                        disabled
                                        title="No se puede eliminar - tiene empleados asignados">
                                    <i class="fas fa-lock"></i>
                                </button>
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center py-4">
                        <div class="empty-state">
                            <i class="fas fa-briefcase fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No se encontraron posiciones</h5>
                            <p class="text-muted mb-0">
                                @if(request('search'))
                                    No hay posiciones que coincidan con tu búsqueda.
                                @else
                                    Comienza agregando tu primera posición de trabajo.
                                @endif
                            </p>
                        </div>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if($positions->hasPages())
    <div class="d-flex justify-content-between align-items-center mt-4 px-3">
        <div class="text-muted small">
            Mostrando {{ $positions->firstItem() ?? 0 }} - {{ $positions->lastItem() ?? 0 }} 
            de {{ $positions->total() }} posiciones
        </div>
        <div>
            {{ $positions->links() }}
        </div>
    </div>
@endif

<style>
.position-icon {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: linear-gradient(135deg, #e8f5e8, #d4edda);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.2rem;
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

.btn-group .btn:hover:not(:disabled) {
    transform: translateY(-1px);
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.badge {
    font-size: 0.75rem;
    padding: 0.375rem 0.75rem;
    border-radius: 0.5rem;
}
</style>