@extends('layouts.app')

@section('content')
<h1 class="mb-4">Reporte de Escuelas</h1>
<form method="GET" action="{{ route('reports.schools') }}" class="card p-4 mb-4">
    <div class="row g-3">
        <div class="col-md-3">
            <label class="form-label">CUE</label>
            <input type="text" name="cue" value="{{ request('cue') }}" class="form-control" placeholder="CUE Predio">
        </div>
        <div class="col-md-3">
            <label class="form-label">Provincia</label>
            <input type="text" name="province" value="{{ request('province') }}" class="form-control">
        </div>
        <div class="col-md-3">
            <label class="form-label">Departamento</label>
            <input type="text" name="department" value="{{ request('department') }}" class="form-control">
        </div>
        <div class="col-md-3">
            <label class="form-label">Ciudad</label>
            <input type="text" name="city" value="{{ request('city') }}" class="form-control">
        </div>
        <div class="col-md-3">
            <label class="form-label">Estado Conectividad</label>
            <input type="text" name="connectivity_status" value="{{ request('connectivity_status') }}" class="form-control">
        </div>
        <div class="col-md-3">
            <label class="form-label">Estado red local</label>
            <input type="text" name="lan_status" value="{{ request('lan_status') }}" class="form-control">
        </div>
        <div class="col-md-3">
            <label class="form-label">Matrícula mín</label>
            <input type="number" name="enrollment_min" value="{{ request('enrollment_min') }}" class="form-control" min="0">
        </div>
        <div class="col-md-3">
            <label class="form-label">Matrícula máx</label>
            <input type="number" name="enrollment_max" value="{{ request('enrollment_max') }}" class="form-control" min="0">
        </div>
        <div class="col-md-3 d-flex align-items-end">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" value="1" id="only_latest" name="only_latest" {{ request()->boolean('only_latest', true) ? 'checked' : '' }}>
                <label class="form-check-label" for="only_latest">
                    Solo último snapshot por escuela
                </label>
            </div>
        </div>
    </div>
    <div class="mt-3 d-flex gap-2">
        <button class="btn btn-primary" type="submit">Filtrar</button>
        <a class="btn btn-outline-secondary" href="{{ route('reports.schools') }}">Limpiar</a>
        <a class="btn btn-success" href="{{ route('reports.schools.export', request()->query()) }}">Exportar</a>
    </div>
</form>

<div class="table-responsive">
<table class="table table-striped table-bordered">
    <thead>
    <tr>
        <th>CUE</th>
        <th>Nombre</th>
        <th>Provincia</th>
        <th>Departamento</th>
        <th>Ciudad</th>
        <th>Dirección</th>
        <th>Matrícula</th>
        <th>MB calculado</th>
        <th>Conectividad</th>
        <th>Red local</th>
        <th>Import</th>
    </tr>
    </thead>
    <tbody>
    @forelse($snapshots as $snapshot)
        <tr>
            <td>{{ $snapshot->cue }}</td>
            <td>{{ $snapshot->name }}</td>
            <td>{{ $snapshot->province }}</td>
            <td>{{ $snapshot->department }}</td>
            <td>{{ $snapshot->city }}</td>
            <td>{{ $snapshot->address }}</td>
            <td>{{ $snapshot->enrollment }}</td>
            <td>{{ $snapshot->mb_calculated }}</td>
            <td>{{ $snapshot->connectivity_status }}</td>
            <td>{{ $snapshot->lan_status }}</td>
            <td>#{{ $snapshot->import_id }}</td>
        </tr>
    @empty
        <tr><td colspan="11" class="text-center">Sin resultados</td></tr>
    @endforelse
    </tbody>
</table>
</div>

{{ $snapshots->links() }}
@endsection
