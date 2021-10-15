<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Models\OperatorType;
use App\Models\Operator;

class importOldData_Operators extends Command{

    protected $signature = 'importOldData_Operators';
    protected $description = 'Import Old Data';

    public function __construct(){
        parent::__construct();
    }

    public function handle(){

        //Connect to DB
        $conn = pg_connect(env('OLD_DB_CONNECTION'));
        if (!$conn) return $this->info('Connection Error.');

        //operator_types
        $result = pg_query($conn, "SELECT * FROM operator_types");  
        if (!$result) return $this->info('Query Error.');
        $count = 0;

        while ($row = pg_fetch_row($result)) {
            $count++;
            $item = new OperatorType;
            for ($j = 0; $j < count($row); $j++) {
                $column = pg_field_name($result, $j);
                $item->{$column} = $row[$j];
            }
            $item->save();
        }
        $this->info("operator_types DONE ($count items).");

        //operators
        $result = pg_query($conn, "SELECT * FROM operators");  
        if (!$result) return $this->info('Query Error.');
        $count = 0;

        while ($row = pg_fetch_row($result)) {
            $count++;
            $item = new Operator;
            for ($j = 0; $j < count($row); $j++) {
                $column = pg_field_name($result, $j);
                if ($column == 'sort') continue;
                if ($column == 'logo') continue;
                $item->{$column} = $row[$j];
            }
            $item->save();
        }
        $this->info("operators DONE ($count items).");

    }
}