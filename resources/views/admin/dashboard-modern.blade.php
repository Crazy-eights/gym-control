@extends('layouts.admin-modern')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@push('styles')
<style>
    /* Estilos específicos para el dashboard */
    .dashboard-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: var(--spacing-xl);
        margin-bottom: var(--spacing-2xl);
    }
    
    .chart-container {
        background: var(--bg-primary);
        border-radius: var(--border-radius-lg);
        padding: var(--spacing-xl);
        box-shadow: var(--shadow-md);
        height: 400px;
    }
    
    .recent-activity {
        background: var(--bg-primary);
        border-radius: var(--border-radius-lg);
        box-shadow: var(--shadow-md);
        overflow: hidden;
    }
    
    .activity-header {
        padding: var(--spacing-xl) var(--spacing-xl) var(--spacing-lg);
        border-bottom: 1px solid var(--bg-tertiary);
    }
    
    .activity-item {
        padding: var(--spacing-md) var(--spacing-xl);
        border-bottom: 1px solid var(--bg-tertiary);
        transition: background-color var(--transition-speed) ease;
    }
    
    .activity-item:hover {
        background: var(--bg-secondary);
    }
    
    .activity-item:last-child {
        border-bottom: none;
    }
    
    .quick-actions {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: var(--spacing-lg);
        margin-bottom: var(--spacing-2xl);
    }
    
    .quick-action-btn {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: var(--spacing-md);
        padding: var(--spacing-xl);
        background: var(--bg-primary);
        border: 2px solid var(--bg-tertiary);
        border-radius: var(--border-radius-lg);
        text-decoration: none;
        color: var(--text-primary);
        transition: all var(--transition-speed) ease;
    }
    
    .quick-action-btn:hover {
        border-color: var(--primary-color);
        color: var(--primary-color);
        transform: translateY(-2px);
        box-shadow: var(--shadow-lg);
    }
    
    .quick-action-icon {
        width: 60px;
        height: 60px;
        background: var(--primary-light);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: var(--font-size-2xl);
        color: var(--primary-color);
    }
</style>
@endpush

@section('content')
<div class="animate-fade-in-up">
    <!-- Statistics Cards -->
    <div class="dashboard-grid">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    @php
                        try {
                            $memberCount = \App\Models\Member::count();
                        } catch (Exception $e) {
                            $memberCount = 0;
                        }
                    @endphp
                    <div class="stat-number">{{ $memberCount }}</div>
                    <div class="stat-label">Socios Activos</div>
                </div>
                <div class="stat-icon">
                    <i class="fas fa-users" style="color: var(--primary-color); font-size: 2rem;"></i>
                </div>
            </div>
            <div class="mt-3">
                <small class="text-success">
                    <i class="fas fa-arrow-up"></i> +12% este mes
                </small>
            </div>
        </div>

        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    @php
                        try {
                            $planCount = \App\Models\MembershipPlan::count();
                        } catch (Exception $e) {
                            $planCount = 0;
                        }
                    @endphp
                    <div class="stat-number">{{ $planCount }}</div>
                    <div class="stat-label">Planes Activos</div>
                </div>
                <div class="stat-icon">
                    <i class="fas fa-id-card" style="color: var(--secondary-color); font-size: 2rem;"></i>
                </div>
            </div>
            <div class="mt-3">
                <small class="text-info">
                    <i class="fas fa-arrow-right"></i> Sin cambios
                </small>
            </div>
        </div>

        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="stat-number">${{ number_format(45750, 0) }}</div>
                    <div class="stat-label">Ingresos del Mes</div>
                </div>
                <div class="stat-icon">
                    <i class="fas fa-dollar-sign" style="color: var(--success); font-size: 2rem;"></i>
                </div>
            </div>
            <div class="mt-3">
                <small class="text-success">
                    <i class="fas fa-arrow-up"></i> +8% vs mes anterior
                </small>
            </div>
        </div>

        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="stat-number">98%</div>
                    <div class="stat-label">Satisfacción</div>
                </div>
                <div class="stat-icon">
                    <i class="fas fa-heart" style="color: var(--danger); font-size: 2rem;"></i>
                </div>
            </div>
            <div class="mt-3">
                <small class="text-success">
                    <i class="fas fa-arrow-up"></i> Excelente
                </small>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="quick-actions">
        <a href="{{ route('admin.socios.index') }}" class="quick-action-btn">
            <div class="quick-action-icon">
                <i class="fas fa-user-plus"></i>
            </div>
            <span class="fw-semibold">Gestionar Socios</span>
            <small class="text-muted">Ver y gestionar miembros</small>
        </a>

        <a href="{{ route('admin.classes.index') }}" class="quick-action-btn">
            <div class="quick-action-icon">
                <i class="fas fa-dumbbell"></i>
            </div>
            <span class="fw-semibold">Gestionar Clases</span>
            <small class="text-muted">Administrar clases</small>
        </a>

        <a href="{{ route('admin.membership-plans.index') }}" class="quick-action-btn">
            <div class="quick-action-icon">
                <i class="fas fa-id-card"></i>
            </div>
            <span class="fw-semibold">Planes</span>
            <small class="text-muted">Configurar membresías</small>
        </a>

        <a href="{{ route('admin.mail.config.index') }}" class="quick-action-btn">
            <div class="quick-action-icon">
                <i class="fas fa-envelope"></i>
            </div>
            <span class="fw-semibold">Email</span>
            <small class="text-muted">Configuración de correo</small>
        </a>
    </div>

    <!-- Content Grid -->
    <div class="row">
        <!-- Chart Section -->
        <div class="col-lg-8 mb-4">
            <div class="chart-container">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="mb-0">Nuevos Socios por Mes</h5>
                    <div class="btn-group btn-group-sm">
                        <button class="btn btn-outline-primary active">6M</button>
                        <button class="btn btn-outline-primary">1A</button>
                        <button class="btn btn-outline-primary">Todo</button>
                    </div>
                </div>
                <canvas id="membersChart" style="max-height: 300px;"></canvas>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="col-lg-4 mb-4">
            <div class="recent-activity">
                <div class="activity-header">
                    <h5 class="mb-0">Actividad Reciente</h5>
                </div>
                <div class="activity-item">
                    <div class="d-flex align-items-center">
                        <div class="me-3">
                            <div class="bg-success rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                <i class="fas fa-user-plus text-white"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1">
                            <div class="fw-semibold">Nuevo socio registrado</div>
                            <small class="text-muted">Juan Pérez se unió al gimnasio</small>
                            <div class="text-muted" style="font-size: 0.75rem;">Hace 2 minutos</div>
                        </div>
                    </div>
                </div>

                <div class="activity-item">
                    <div class="d-flex align-items-center">
                        <div class="me-3">
                            <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                <i class="fas fa-credit-card text-white"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1">
                            <div class="fw-semibold">Pago procesado</div>
                            <small class="text-muted">María García - Plan Premium</small>
                            <div class="text-muted" style="font-size: 0.75rem;">Hace 15 minutos</div>
                        </div>
                    </div>
                </div>

                <div class="activity-item">
                    <div class="d-flex align-items-center">
                        <div class="me-3">
                            <div class="bg-warning rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                <i class="fas fa-calendar text-white"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1">
                            <div class="fw-semibold">Clase programada</div>
                            <small class="text-muted">Yoga matutino - 7:00 AM</small>
                            <div class="text-muted" style="font-size: 0.75rem;">Hace 1 hora</div>
                        </div>
                    </div>
                </div>

                <div class="activity-item">
                    <div class="d-flex align-items-center">
                        <div class="me-3">
                            <div class="bg-info rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                <i class="fas fa-envelope text-white"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1">
                            <div class="fw-semibold">Email enviado</div>
                            <small class="text-muted">Newsletter semanal</small>
                            <div class="text-muted" style="font-size: 0.75rem;">Hace 2 horas</div>
                        </div>
                    </div>
                </div>

                <div class="text-center p-3 border-top">
                    <a href="#" class="btn btn-sm btn-outline-primary">Ver todo</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Chart.js configuration
    const ctx = document.getElementById('membersChart');
    if (ctx) {
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio'],
                datasets: [{
                    label: 'Nuevos Socios',
                    data: [12, 19, 15, 25, 22, 30],
                    borderColor: 'rgb(76, 175, 80)',
                    backgroundColor: 'rgba(76, 175, 80, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0,0,0,0.1)'
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });
    }
});
</script>
@endpush