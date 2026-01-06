<?php

namespace App\Exports;

use App\Models\SchoolSnapshot;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class SchoolsReportExport implements FromCollection, WithHeadings, WithMapping
{
    public function __construct(private array $filters, private bool $onlyLatest)
    {
    }

    public function collection()
    {
        return $this->query()->get();
    }

    public function headings(): array
    {
        return [
            'CUE',
            'Nombre',
            'Provincia',
            'Departamento',
            'Ciudad',
            'Dirección',
            'Lat',
            'Lng',
            'Matrícula',
            'MB calculado',
            'Estado Conectividad',
            'Estado red local',
            'Import ID',
        ];
    }

    public function map($snapshot): array
    {
        return [
            $snapshot->cue,
            $snapshot->name,
            $snapshot->province,
            $snapshot->department,
            $snapshot->city,
            $snapshot->address,
            $snapshot->lat,
            $snapshot->lng,
            $snapshot->enrollment,
            $snapshot->mb_calculated,
            $snapshot->connectivity_status,
            $snapshot->lan_status,
            $snapshot->import_id,
        ];
    }

    protected function query(): Builder
    {
        $query = SchoolSnapshot::query()
            ->join('schools', 'school_snapshots.school_id', '=', 'schools.id')
            ->select('school_snapshots.*', 'schools.cue', 'schools.name', 'schools.province', 'schools.department', 'schools.city', 'schools.address', 'schools.lat', 'schools.lng')
            ->orderByDesc('school_snapshots.id');

        if ($this->onlyLatest) {
            $query->whereIn('school_snapshots.id', function ($q) {
                $q->selectRaw('MAX(id)')->from('school_snapshots')->groupBy('school_id');
            });
        }

        if ($this->filters['cue']) {
            $query->where('schools.cue', 'like', '%' . $this->filters['cue'] . '%');
        }

        foreach (['province', 'department', 'city'] as $field) {
            if ($this->filters[$field]) {
                $query->where('schools.' . $field, 'like', '%' . $this->filters[$field] . '%');
            }
        }

        if ($this->filters['connectivity_status']) {
            $query->where('school_snapshots.connectivity_status', 'like', '%' . $this->filters['connectivity_status'] . '%');
        }

        if ($this->filters['lan_status']) {
            $query->where('school_snapshots.lan_status', 'like', '%' . $this->filters['lan_status'] . '%');
        }

        if (!is_null($this->filters['enrollment_min'])) {
            $query->where('school_snapshots.enrollment', '>=', $this->filters['enrollment_min']);
        }

        if (!is_null($this->filters['enrollment_max'])) {
            $query->where('school_snapshots.enrollment', '<=', $this->filters['enrollment_max']);
        }

        return $query;
    }
}
