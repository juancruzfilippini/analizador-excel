@extends('layouts.app')

@section('content')
<h1 class="mb-4">Nuevo import</h1>
<form action="{{ route('imports.store') }}" method="POST" enctype="multipart/form-data" class="card p-4">
    @csrf
    <div class="mb-3">
        <label for="file" class="form-label">Archivo Excel (.xlsx o .xls)</label>
        <input type="file" class="form-control" id="file" name="file" accept=".xlsx,.xls" required>
    </div>
    <button type="submit" class="btn btn-primary">Subir e importar</button>
    <a href="{{ route('imports.index') }}" class="btn btn-link">Volver</a>
</form>
@endsection
