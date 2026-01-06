<?php

namespace App\Imports;

use App\Models\SchoolSnapshot;
use Maatwebsite\Excel\Concerns\ToModel;

class SchoolsDetailImport implements ToModel
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new SchoolSnapshot([
            //
        ]);
    }
}
