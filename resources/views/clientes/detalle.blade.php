@extends('layouts.app', ['title' => 'Detalle del Cliente: ' . $cliente->nombre])

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('clientes.index') }}">Clientes</a></li>
    <li class="breadcrumb-item active">Detalle</li>
@endsection

@section('page-title')
    Detalle del Cliente: {{ $cliente->nombre }}
@endsection

@section('content')
<div class="row">
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5>Información del Cliente</h5>
            </div>
            <div class="card-body">
                <p><strong>Cédula:</strong> {{ $cliente->cedula ?? 'N/A' }}</p>
                <p><strong>Teléfono:</strong> {{ $cliente->telefono }}</p>
                <p><strong>Email:</strong> {{ $cliente->email ?? 'N/A' }}</p>
                <p><strong>Dirección:</strong> {{ $cliente->direccion ?? 'N/A' }}</p>
                <p><strong>Saldo Pendiente:</strong> 
                    <span class="badge bg-{{ $saldo > 0 ? 'warning' : 'success' }}">
                        C$ {{ number_format($saldo, 2) }}
                    </span>
                </p>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5>Historial de Créditos y Abonos</h5>
            </div>
            <div class="card-body">
                <ul class="nav nav-tabs" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" data-bs-toggle="tab" href="#creditos">Créditos</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" href="#abonos">Abonos</a>
                    </li>
                </ul>

                <div class="tab-content">
                    <div id="creditos" class="tab-pane active">
                        <x-table :headers="['Venta #', 'Monto', 'Saldo', 'Fecha Límite', 'Estado']">
                            @foreach($creditos as $credito)
                            <tr>
                                <td>#{{ $credito->venta_id }}</td>
                                <td>C$ {{ number_format($credito->monto, 2) }}</td>
                                <td>C$ {{ number_format($credito->saldo_pendiente, 2) }}</td>
                                <td>{{ $credito->fecha_limite->format('d/m/Y') }}</td>
                                <td>
                                    <span class="badge bg-{{ $credito->estado === 'pagado' ? 'success' : 'warning' }}">
                                        {{ ucfirst($credito->estado) }}
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </x-table>
                    </div>

                    <div id="abonos" class="tab-pane fade">
                        <x-table :headers="['Fecha', 'Monto', 'Método', 'Usuario']">
                            @foreach($abonos as $abono)
                            <tr>
                                <td>{{ $abono->created_at->format('d/m/Y H:i') }}</td>
                                <td>C$ {{ number_format($abono->monto, 2) }}</td>
                                <td>{{ ucfirst($abono->metodo_pago) }}</td>
                                <td>{{ $abono->user->name }}</td>
                            </tr>
                            @endforeach
                        </x-table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection