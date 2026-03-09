<?php

namespace App\Imports;

use App\Models\TestBank;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\ToModel;

class TestBankImport implements ToCollection
{
    public function collection(Collection $rows)
    {
        foreach ($rows as $index => $row) {

            // пропускаем заголовок
            if ($index == 0) {
                continue;
            }

            // пропускаем строки без баллов
            if (!$row[11]) {
                continue;
            }

            \App\Models\TestBank::create([
                'num' => $row[0],
                'subject' => $row[1],
                'grade' => $row[2],
                'lang' => $row[3],
                'variant' => $row[4],
                'type' => $row[5],
                'img' => $row[6],
                'context' => $row[7],
                'question' => $row[8],
                'options' => $row[9],
                'correct_answer' => $row[10],
                'points' => $row[11],
            ]);
        }
    }
}
