@extends('layouts.app')
@php use Illuminate\Support\Str; @endphp

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1>Historial de Imports</h1>
    <a href="{{ route('imports.create') }}" class="btn btn-primary">Nuevo import</a>
</div>

<table class="table table-bordered table-striped">
    <thead>
    <tr>
        <th>ID</th>
        <th>Archivo</th>
        <th>Fecha</th>
        <th>Status</th>
        <th>Rows total</th>
        <th>OK</th>
        <th>Failed</th>
        <th>Notas</th>
    </tr>
    </thead>
    <tbody>
    @forelse($imports as $import)
        <tr>
            <td>{{ $import->id }}</td>
            <td>{{ $import->original_name }}</td>
            <td>{{ $import->created_at?->format('Y-m-d H:i') }}</td>
            <td><span class="badge bg-{{ $import->status === 'done' ? 'success' : ($import->status === 'failed' ? 'danger' : 'secondary') }}">{{ $import->status }}</span></td>
            <td>{{ $import->rows_total }}</td>
            <td>{{ $import->rows_ok }}</td>
            <td>{{ $import->rows_failed }}</td>
            <td>{{ Str::limit($import->notes, 120) }}</td>
        </tr>
    @empty
        <tr>
            <td colspan="8" class="text-center">Sin imports aún</td>
        </tr>
    @endforelse
    </tbody>
</table>

{{ $imports->links() }}
@endsection
