<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Models\TrainName;
use App\Models\TrainType;

class importOldData_Trains extends Command{

    protected $signature = 'importOldData_Trains';
    protected $description = 'Import Old Data';

    public function __construct(){
        parent::__construct();
    }

    public function handle(){

        //Connect to DB
        $conn = pg_connect(env('OLD_DB_CONNECTION'));
        if (!$conn) return $this->info('Connection Error.');

        //train_types
        $result = pg_query($conn, "SELECT * FROM train_types");  
        if (!$result) return $this->info('Query Error.');
        $count = 0;

        while ($row = pg_fetch_row($result)) {
            $count++;
            $item = new TrainType;
            for ($j = 0; $j < count($row); $j++) {
                $column = pg_field_name($result, $j);
                if ($column == 'is_premium'){
                    $row[$j] = $row[$j] ?? false;
                }
                if ($column == 'other_info') $row[$j] = json_decode($row[$j]);
                $item->{$column} = $row[$j];
            }
            $item->save();
        }
        $this->info("train_types DONE ($count items).");

        //train_names
        $result = pg_query($conn, "SELECT * FROM train_names");  
        if (!$result) return $this->info('Query Error.');
        $count = 0;

        while ($row = pg_fetch_row($result)) {
            $count++;
            $item = new TrainName;
            for ($j = 0; $j < count($row); $j++) {
                $column = pg_field_name($result, $j);
                if ($column == 'sort') continue;
                if ($column == 'other_info') $row[$j] = json_decode($row[$j]);
                $item->{$column} = $row[$j];
            }
            $item->save();
        }
        $this->info("train_names DONE ($count items).");

    }
}