<?php

namespace App\Http\Controllers;

use App\Exports\SchoolsReportExport;
use App\Models\SchoolSnapshot;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    public function schools(Request $request): View
    {
        $filters = $this->filters($request);
        $onlyLatest = $request->boolean('only_latest', true);

        $snapshots = $this->buildQuery($filters, $onlyLatest)
            ->paginate(15)
            ->withQueryString();

        return view('reports.schools', compact('snapshots'));
    }

    public function exportSchools(Request $request)
    {
        $filters = $this->filters($request);
        $onlyLatest = $request->boolean('only_latest', true);

        return Excel::download(new SchoolsReportExport($filters, $onlyLatest), 'reporte_escuelas_' . now()->format('Ymd_His') . '.xlsx');
    }

    /**
     * @return Builder<SchoolSnapshot>
     */
    protected function buildQuery(array $filters, bool $onlyLatest): Builder
    {
        $query = SchoolSnapshot::query()
            ->join('schools', 'school_snapshots.school_id', '=', 'schools.id')
            ->select('school_snapshots.*', 'schools.cue', 'schools.name', 'schools.province', 'schools.department', 'schools.city', 'schools.address', 'schools.lat', 'schools.lng')
            ->orderByDesc('school_snapshots.id');

        if ($onlyLatest) {
            $query->whereIn('school_snapshots.id', function ($q) {
                $q->selectRaw('MAX(id)')->from('school_snapshots')->groupBy('school_id');
            });
        }

        if ($filters['cue']) {
            $query->where('schools.cue', 'like', '%' . $filters['cue'] . '%');
        }

        foreach (['province', 'department', 'city'] as $field) {
            if ($filters[$field]) {
                $query->where('schools.' . $field, 'like', '%' . $filters[$field] . '%');
            }
        }

        if ($filters['connectivity_status']) {
            $query->where('school_snapshots.connectivity_status', 'like', '%' . $filters['connectivity_status'] . '%');
        }

        if ($filters['lan_status']) {
            $query->where('school_snapshots.lan_status', 'like', '%' . $filters['lan_status'] . '%');
        }

        if (!is_null($filters['enrollment_min'])) {
            $query->where('school_snapshots.enrollment', '>=', $filters['enrollment_min']);
        }

        if (!is_null($filters['enrollment_max'])) {
            $query->where('school_snapshots.enrollment', '<=', $filters['enrollment_max']);
        }

        return $query;
    }

    protected function filters(Request $request): array
    {
        return [
            'cue' => $request->string('cue')->trim()->toString(),
            'province' => $request->string('province')->trim()->toString(),
            'department' => $request->string('department')->trim()->toString(),
            'city' => $request->string('city')->trim()->toString(),
            'connectivity_status' => $request->string('connectivity_status')->trim()->toString(),
            'lan_status' => $request->string('lan_status')->trim()->toString(),
            'enrollment_min' => $request->filled('enrollment_min') ? (int) $request->input('enrollment_min') : null,
            'enrollment_max' => $request->filled('enrollment_max') ? (int) $request->input('enrollment_max') : null,
        ];
    }
}
