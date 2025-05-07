<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ImportSql extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:sql';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Импортировать SQL-данные в базу данных';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $sql = file_get_contents("public/data.sql");
        DB::unprepared($sql);

        $this->info("Успешно импортировано!");
    }
}
