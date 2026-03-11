<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use App\Models\District;
use App\Models\School;

class SchoolsImport implements ToCollection
{
    public function collection(Collection $rows)
    {

        foreach ($rows as $index => $row) {

            // пропускаем заголовок
            if ($index == 0) {
                continue;
            }

            $districtName = trim($row[0]); // Район
            $schoolName   = trim($row[1]); // Наименование организации


            if (!$districtName || !$schoolName) {
                continue;
            }


            // ищем район
            $district = District::firstOrCreate([
                'name_ru' => $districtName
            ]);


            // создаем школу
            School::create([
                'district_id' => $district->id,
                'name_ru' => $schoolName
            ]);

        }

    }
}