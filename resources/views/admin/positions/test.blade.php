@extends('layouts.admin')

@section('title', 'Test Posiciones')

@section('content')
<div class="container-fluid">
    <h1>Módulo de Posiciones - Test</h1>
    
    <div class="alert alert-success">
        <h4>✅ Módulo de Posiciones Funcionando Correctamente</h4>
        <p>Si puedes ver esta página, el módulo está configurado correctamente.</p>
    </div>
    
    <div class="card">
        <div class="card-header">
            <h5>Posiciones en la Base de Datos</h5>
        </div>
        <div class="card-body">
            @if($positions->count() > 0)
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Descripción</th>
                                <th>Tarifa por Hora</th>
                                <th>Fecha de Creación</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($positions as $position)
                            <tr>
                                <td>{{ $position->id }}</td>
                                <td>{{ $position->description }}</td>
                                <td>${{ number_format($position->rate, 2) }}</td>
                                <td>{{ $position->created_at->format('d/m/Y H:i') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-muted">No hay posiciones registradas.</p>
            @endif
        </div>
    </div>
    
    <div class="mt-4">
        <a href="{{ route('admin.positions.index') }}" class="btn btn-primary">
            <i class="fas fa-arrow-left"></i> Volver al Módulo de Posiciones
        </a>
        <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">
            <i class="fas fa-home"></i> Ir al Dashboard
        </a>
    </div>
</div>
@endsection