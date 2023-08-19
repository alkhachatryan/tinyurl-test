<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CalculateDBSize extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:calculate-db-size';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Shows DB size in KBs';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $tableSizes = DB::select("
    SELECT
        table_name,
        ROUND(data_length / 1024, 2) as data_kb,
        ROUND(index_length / 1024, 2) as index_kb
    FROM information_schema.tables
    WHERE table_schema = DATABASE()
");

        $total = 0;
        foreach ($tableSizes as $tableSize) {
            $tableName = $tableSize->TABLE_NAME;
            $dataSizeKB = $tableSize->data_kb;
            $indexSizeKB = $tableSize->index_kb;
            $totalSizeKB = $dataSizeKB + $indexSizeKB;
            $total += $totalSizeKB;

            echo "Table: $tableName | Data Size: $dataSizeKB KB | Index Size: $indexSizeKB KB | Total Size: $totalSizeKB KB\n";
        }

        echo "TOTAL: $total KB\n";
    }
}
