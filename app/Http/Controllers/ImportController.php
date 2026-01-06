<?php

namespace App\Http\Controllers;

use App\Http\Requests\ImportUploadRequest;
use App\Jobs\ProcessSchoolsImport;
use App\Models\Import;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

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
            'status' => 'queued',
        ]);

        ProcessSchoolsImport::dispatch($import->id)->onConnection('database')->onQueue('imports');

        return redirect()->route('imports.index')->with('success', 'Import encolado. Ejecuta php artisan queue:work para procesarlo.');
    }
}
