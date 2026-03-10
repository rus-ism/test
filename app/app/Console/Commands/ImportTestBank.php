<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use Maatwebsite\Excel\Facades\Excel;
use App\Imports\TestBankImport;

class ImportTestBank extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
protected $signature = 'import:test-bank';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        Excel::import(new TestBankImport, storage_path('app/TestBank.xlsx'));

        $this->info('Import completed');
    }
}
