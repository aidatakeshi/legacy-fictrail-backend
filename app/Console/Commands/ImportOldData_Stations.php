<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Models\Station;
use App\Models\Line_Station;

class importOldData_Stations extends Command{

    protected $signature = 'importOldData_Stations';
    protected $description = 'Import Old Data';

    public function __construct(){
        parent::__construct();
    }

    public function handle(){

        //Connect to DB
        $conn = pg_connect(env('OLD_DB_CONNECTION'));
        if (!$conn) return $this->info('Connection Error.');

        //stations
        $result = pg_query($conn, "SELECT * FROM stations");  
        if (!$result) return $this->info('Query Error.');
        $count = 0;

        while ($row = pg_fetch_row($result)) {
            $count++;
            $item = new Station;
            for ($j = 0; $j < count($row); $j++) {
                $column = pg_field_name($result, $j);
                if ($column == 'sort') continue;
                if ($column == 'height_m'){
                    $row[$j] = intval($row[$j]);
                    if (!$row[$j]) $row[$j] = null;
                }
                $item->{$column} = $row[$j];
            }
            $item->save();
        }
        $this->info("stations DONE ($count items).");

        //lines_stations
        $result = pg_query($conn, "SELECT * FROM lines_stations");  
        if (!$result) return $this->info('Query Error.');
        $count = 0;

        while ($row = pg_fetch_row($result)) {
            $count++;
            $item = new Line_Station;
            for ($j = 0; $j < count($row); $j++) {
                $column = pg_field_name($result, $j);
                if ($column == 'sort') continue;
                if ($column == 'distance_km' || $column == 'mileage_km'){
                    $row[$j] = floatval($row[$j]);
                }
                if ($column == 'no_tracks'){
                    $row[$j] = floatval($row[$j]);
                    if (!$row[$j]) $row[$j] = 2;
                }
                $item->{$column} = $row[$j];
            }
            $item->save();
        }
        $this->info("lines_stations DONE ($count items).");

    }
}