<?php

namespace App\Imports;

use App\Models\School;
use App\Models\SchoolSnapshot;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Throwable;

class SchoolsDetailImport implements WithMultipleSheets
{
    protected int $importId;
    protected int $rowsTotal = 0;
    protected int $rowsOk = 0;
    protected int $rowsFailed = 0;
    protected array $errors = [];

    public function __construct(int $importId)
    {
        $this->importId = $importId;
    }

    public function sheets(): array
    {
        return [
            'Detalle' => new class($this) implements ToCollection, WithHeadingRow, WithChunkReading {
                public function __construct(private SchoolsDetailImport $parent)
                {
                }

                public function collection(Collection $rows)
                {
                    foreach ($rows as $row) {
                        $this->parent->processRow($row);
                    }
                }

                public function chunkSize(): int
                {
                    return 200;
                }
            },
        ];
    }

    public function processRow($row): void
    {
        $rowArray = $row instanceof Collection ? $row->toArray() : (array) $row;
        if ($this->isEmptyRow($rowArray)) {
            return;
        }

        $this->rowsTotal++;

        $cue = $this->normalizeString($this->getValue($rowArray, ['cue_predio', 'cue', 'cuepredio', 'cue_predio_']));
        if (!$cue) {
            $this->rowsFailed++;
            $this->errors[] = 'Fila sin CUE Predio';
            return;
        }

        $stableData = [
            'name' => $this->normalizeString($this->getValue($rowArray, ['nombre', 'name', 'establecimiento'])),
            'province' => $this->normalizeString($this->getValue($rowArray, ['provincia', 'province'])),
            'department' => $this->normalizeString($this->getValue($rowArray, ['departamento', 'depto', 'department'])),
            'city' => $this->normalizeString($this->getValue($rowArray, ['localidad', 'ciudad', 'city'])),
            'address' => $this->normalizeString($this->getValue($rowArray, ['domicilio', 'direccion', 'address'])),
            'lat' => $this->normalizeFloat($this->getValue($rowArray, ['latitud', 'lat'])),
            'lng' => $this->normalizeFloat($this->getValue($rowArray, ['longitud', 'lng', 'lon'])),
        ];

        $variableData = [
            'enrollment' => $this->normalizeInt($this->getValue($rowArray, ['matricula', 'enrollment'])),
            'mb_calculated' => $this->normalizeFloat($this->getValue($rowArray, ['mb_calculado', 'mb calculado', 'mb'])),
            'connectivity_status' => $this->normalizeString($this->getValue($rowArray, ['estado_conectividad', 'estado conectividad', 'conectividad'])),
            'lan_status' => $this->normalizeString($this->getValue($rowArray, ['estado_red_local', 'estado red local', 'red_local'])),
        ];

        try {
            DB::transaction(function () use ($cue, $stableData, $variableData, $rowArray) {
                $school = School::firstOrNew(['cue' => $cue]);
                foreach ($stableData as $key => $value) {
                    if (!is_null($value)) {
                        $school->$key = $value;
                    }
                }
                $school->save();

                $snapshotData = array_merge($variableData, [
                    'import_id' => $this->importId,
                    'school_id' => $school->id,
                    'raw' => $rowArray,
                ]);

                SchoolSnapshot::create($snapshotData);
            });

            $this->rowsOk++;
        } catch (Throwable $e) {
            $this->rowsFailed++;
            $this->errors[] = 'CUE ' . $cue . ': ' . $e->getMessage();
        }
    }

    public function getSummary(): array
    {
        return [
            'rows_total' => $this->rowsTotal,
            'rows_ok' => $this->rowsOk,
            'rows_failed' => $this->rowsFailed,
            'errors' => $this->errors,
        ];
    }

    protected function isEmptyRow(array $row): bool
    {
        return collect($row)->filter(fn($value) => $value !== null && $value !== '')->isEmpty();
    }

    protected function normalizeString($value): ?string
    {
        if (is_null($value)) {
            return null;
        }
        $value = trim((string) $value);
        return $value === '' ? null : $value;
    }

    protected function normalizeInt($value): ?int
    {
        if ($value === null || $value === '') {
            return null;
        }
        $clean = preg_replace("/[^0-9-]/", "", (string) $value);
        return $clean === "" ? null : (int) $clean;
    }

    protected function normalizeFloat($value): ?float
    {
        if ($value === null || $value === '') {
            return null;
        }
        return (float) Str::replace(',', '.', (string) $value);
    }

    protected function getValue(array $row, array $possibleKeys)
    {
        foreach ($possibleKeys as $key) {
            if (Arr::exists($row, $key)) {
                return $row[$key];
            }
        }

        return null;
    }
}
