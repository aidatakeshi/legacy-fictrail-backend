<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Models\PrefectureArea;
use App\Models\Prefecture;

class importOldData_Prefectures extends Command{

    protected $signature = 'importOldData_Prefectures';
    protected $description = 'Import Old Data';

    public function __construct(){
        parent::__construct();
    }

    public function handle(){

        //Connect to DB
        $conn = pg_connect(env('OLD_DB_CONNECTION'));
        if (!$conn) return $this->info('Connection Error.');

        //prefecture_areas
        $result = pg_query($conn, "SELECT * FROM prefecture_areas");  
        if (!$result) return $this->info('Query Error.');
        $count = 0;

        while ($row = pg_fetch_row($result)) {
            $count++;
            $item = new PrefectureArea;
            for ($j = 0; $j < count($row); $j++) {
                $column = pg_field_name($result, $j);
                if ($column == 'other_info') $row[$j] = json_decode($row[$j]);
                $item->{$column} = $row[$j];
            }
            $item->save();
        }
        $this->info("prefecture_areas DONE ($count items).");

        //prefectures
        $result = pg_query($conn, "SELECT * FROM prefectures");  
        if (!$result) return $this->info('Query Error.');
        $count = 0;

        while ($row = pg_fetch_row($result)) {
            $count++;
            $item = new Prefecture;
            for ($j = 0; $j < count($row); $j++) {
                $column = pg_field_name($result, $j);
                if ($column == 'other_info') $row[$j] = json_decode($row[$j]);
                $item->{$column} = $row[$j];
            }
            $item->save();
        }
        $this->info("prefectures DONE ($count items).");

    }
}