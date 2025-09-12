@props(['headers', 'emptyMessage' => 'No hay registros para mostrar.'])

<div class="table-responsive">
    <table {{ $attributes->class(['table table-striped table-hover']) }}>
        <thead>
            <tr>
                @foreach($headers as $header)
                    <th>{{ $header }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            {{ $slot }}
        </tbody>
    </table>
</div>

@if($emptyMessage)
    <div class="text-center py-4 text-muted empty-table-message" style="display: none;">
        {{ $emptyMessage }}
    </div>
@endif