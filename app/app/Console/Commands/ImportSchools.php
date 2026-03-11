<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\SchoolsImport;

class ImportSchools extends Command
{

    protected $signature = 'schools:import';

    protected $description = 'Import districts and schools';

    public function handle()
    {

        Excel::import(
            new SchoolsImport,
            storage_path('app/schools.xlsx')
        );

        $this->info('Schools imported');

    }

}