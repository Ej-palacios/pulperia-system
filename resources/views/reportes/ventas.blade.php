@extends('layouts.app', ['title' => 'Reportes de Ventas'])

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('reportes.index') }}">Reportes</a></li>
    <li class="breadcrumb-item active">Ventas</li>
@endsection

@section('page-title')
    Reportes de Ventas
@endsection

@section('content')
<div class="card mb-4">
    <div class="card-header">
        <h5>Filtros de Búsqueda</h5>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('reportes.ventas') }}">
            <div class="row">
                <div class="col-md-3">
                    <x-input label="Fecha Inicio" name="fecha_inicio" type="date" :value="$fechaInicio" />
                </div>
                <div class="col-md-3">
                    <x-input label="Fecha Fin" name="fecha_fin" type="date" :value="$fechaFin" />
                </div>
                <div class="col-md-3">
                    <div class="mb-3">
                        <label class="form-label">Tipo de Pago</label>
                        <select name="tipo_pago" class="form-select">
                            <option value="">Todos</option>
                            <option value="contado" {{ request('tipo_pago') === 'contado' ? 'selected' : '' }}>Contado</option>
                            <option value="credito" {{ request('tipo_pago') === 'credito' ? 'selected' : '' }}>Crédito</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-3 align-self-end">
                    <x-button type="submit" color="primary">Generar Reporte</x-button>
                    <x-button type="button" color="success" icon="fas fa-download" onclick="exportarExcel()">
                        Excel
                    </x-button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h5>Resultados del Reporte</h5>
    </div>
    <div class="card-body">
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card bg-light">
                    <div class="card-body text-center">
                        <h6>Total Ventas</h6>
                        <h4>C$ {{ number_format($totalVentas, 2) }}</h4>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-light">
                    <div class="card-body text-center">
                        <h6>Ventas Contado</h6>
                        <h4>C$ {{ number_format($ventasContado, 2) }}</h4>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-light">
                    <div class="card-body text-center">
                        <h6>Ventas Crédito</h6>
                        <h4>C$ {{ number_format($ventasCredito, 2) }}</h4>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-light">
                    <div class="card-body text-center">
                        <h6>Total Ventas</h6>
                        <h4>{{ $totalRegistros }}</h4>
                    </div>
                </div>
            </div>
        </div>

        <x-table :headers="['# Venta', 'Cliente', 'Fecha', 'Tipo Pago', 'Subtotal', 'Impuestos', 'Total']">
            @foreach($ventas as $venta)
            <tr>
                <td>#{{ $venta->id }}</td>
                <td>{{ $venta->cliente->nombre ?? 'Cliente General' }}</td>
                <td>{{ $venta->created_at->format('d/m/Y H:i') }}</td>
                <td>
                    <span class="badge bg-{{ $venta->tipo_pago === 'contado' ? 'success' : 'warning' }}">
                        {{ ucfirst($venta->tipo_pago) }}
                    </span>
                </td>
                <td>C$ {{ number_format($venta->subtotal, 2) }}</td>
                <td>C$ {{ number_format($venta->impuestos, 2) }}</td>
                <td><strong>C$ {{ number_format($venta->total, 2) }}</strong></td>
            </tr>
            @endforeach
        </x-table>

        {{ $ventas->links() }}
    </div>
</div>
@endsection

@section('scripts')
<script>
function exportarExcel() {
    // Lógica para exportar a Excel
    alert('Funcionalidad de exportación a Excel');
}
</script>
@endsection