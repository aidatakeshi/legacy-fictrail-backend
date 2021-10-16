<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Models\VehiclePerformanceGroup;
use App\Models\VehiclePerformanceItem;

class ImportOldData_VehiclePerformances extends Command{

    protected $signature = 'importOldData_VehiclePerformances';
    protected $description = 'Import Old Data';

    public function __construct(){
        parent::__construct();
    }

    public function handle(){

        //Connect to DB
        $conn = pg_connect(env('OLD_DB_CONNECTION'));
        if (!$conn) return $this->info('Connection Error.');

        //vehicle_performance_groups
        $result = pg_query($conn, "SELECT * FROM vehicle_performance_groups");  
        if (!$result) return $this->info('Query Error.');
        $count = 0;

        while ($row = pg_fetch_row($result)) {
            $count++;
            $item = new VehiclePerformanceGroup;
            for ($j = 0; $j < count($row); $j++) {
                $column = pg_field_name($result, $j);
                if ($column == 'sort') $row[$j] = intval($row[$j]);
                if ($column == 'other_info') $row[$j] = json_decode($row[$j]);
                $item->{$column} = $row[$j];
            }
            $item->save();
        }
        $this->info("vehicle_performance_groups DONE ($count items).");

        //vehicle_performance_items
        $result = pg_query($conn, "SELECT * FROM vehicle_performance_items");  
        if (!$result) return $this->info('Query Error.');
        $count = 0;

        while ($row = pg_fetch_row($result)) {
            $count++;
            $item = new VehiclePerformanceItem;
            for ($j = 0; $j < count($row); $j++) {
                $column = pg_field_name($result, $j);
                if ($column == 'sort') $row[$j] = intval($row[$j]);
                if ($column == 'other_info') $row[$j] = json_decode($row[$j]);
                $item->{$column} = $row[$j];
            }
            $item->save();
        }
        $this->info("vehicle_performance_items DONE ($count items).");

    }
}