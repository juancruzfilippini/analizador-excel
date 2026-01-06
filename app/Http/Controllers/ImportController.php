<?php

namespace App\Http\Controllers;

use App\Http\Requests\ImportUploadRequest;
use App\Imports\SchoolsDetailImport;
use App\Models\Import;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Throwable;

class ImportController extends Controller
{
    public function index(): View
    {
        $imports = Import::latest()->paginate(15);

        return view('imports.index', compact('imports'));
    }

    public function create(): View
    {
        return view('imports.create');
    }

    public function store(ImportUploadRequest $request): RedirectResponse
    {
        $file = $request->file('file');
        $storedPath = $file->storeAs('imports', now()->format('Ymd_His_') . $file->getClientOriginalName());

        $import = Import::create([
            'original_name' => $file->getClientOriginalName(),
            'stored_path' => $storedPath,
            'status' => 'processing',
        ]);

        $importer = new SchoolsDetailImport($import->id);

        try {
            Excel::import($importer, $storedPath);
            $summary = $importer->getSummary();

            $import->update([
                'rows_total' => $summary['rows_total'],
                'rows_ok' => $summary['rows_ok'],
                'rows_failed' => $summary['rows_failed'],
                'status' => 'done',
                'notes' => implode('\n', array_slice($summary['errors'], 0, 20)),
            ]);

            return redirect()->route('imports.index')->with('success', 'Importación completada.');
        } catch (Throwable $e) {
            Log::error('Error procesando import', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            $import->update([
                'status' => 'failed',
                'notes' => $e->getMessage(),
            ]);
            Storage::delete($storedPath);

            return back()->withErrors('Ocurrió un error durante la importación. Ver logs.');
        }
    }
}
