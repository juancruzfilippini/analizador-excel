<?php

namespace App\Jobs;

use App\Imports\SchoolsDetailImport;
use App\Models\Import;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Throwable;

class ProcessSchoolsImport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Allow more than 60s for large imports while still running in the worker.
     */
    public $timeout = 300;

    public function __construct(private int $importId)
    {
    }

    public function handle(): void
    {
        $import = Import::find($this->importId);

        if (!$import) {
            Log::warning('Import not found for job', ['import_id' => $this->importId]);
            return;
        }

        try {
            $importer = new SchoolsDetailImport($import->id);

            $import->update(['status' => 'processing']);

            $filePath = storage_path('app/' . ltrim($import->stored_path, '/'));

            Excel::import($importer, $filePath);
            $summary = $importer->getSummary();

            $import->update([
                'rows_total' => $summary['rows_total'],
                'rows_ok' => $summary['rows_ok'],
                'rows_failed' => $summary['rows_failed'],
                'status' => 'done',
                'notes' => implode("\n", array_slice($summary['errors'], 0, 20)),
            ]);
        } catch (Throwable $e) {
            $import->update([
                'status' => 'failed',
                'notes' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    public function failed(Throwable $exception): void
    {
        if ($import = Import::find($this->importId)) {
            $import->update([
                'status' => 'failed',
                'notes' => $exception->getMessage(),
            ]);
        }
    }
}
