<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Models\Line;
use App\Models\LineGroup;
use App\Models\LineType;

class importOldData_Lines extends Command{

    protected $signature = 'importOldData_Lines';
    protected $description = 'Import Old Data';

    public function __construct(){
        parent::__construct();
    }

    public function handle(){

        //Connect to DB
        $conn = pg_connect(env('OLD_DB_CONNECTION'));
        if (!$conn) return $this->info('Connection Error.');

        //lines
        $result = pg_query($conn, "SELECT * FROM lines");  
        if (!$result) return $this->info('Query Error.');
        $count = 0;

        while ($row = pg_fetch_row($result)) {
            $count++;
            $item = new Line;
            for ($j = 0; $j < count($row); $j++) {
                $column = pg_field_name($result, $j);
                if ($column == 'sort') continue;
                if ($column == 'other_info') $row[$j] = json_decode($row[$j]);
                $item->{$column} = $row[$j];
            }
            $item->save();
        }
        $this->info("lines DONE ($count items).");

        //line_groups
        $result = pg_query($conn, "SELECT * FROM line_groups");  
        if (!$result) return $this->info('Query Error.');
        $count = 0;

        while ($row = pg_fetch_row($result)) {
            $count++;
            $item = new LineGroup;
            for ($j = 0; $j < count($row); $j++) {
                $column = pg_field_name($result, $j);
                if ($column == 'sort') continue;
                if ($column == 'other_info') $row[$j] = json_decode($row[$j]);
                $item->{$column} = $row[$j];
            }
            $item->save();
        }
        $this->info("line_groups DONE ($count items).");

        //line_types
        $result = pg_query($conn, "SELECT * FROM line_types");  
        if (!$result) return $this->info('Query Error.');
        $count = 0;

        while ($row = pg_fetch_row($result)) {
            $count++;
            $item = new LineType;
            for ($j = 0; $j < count($row); $j++) {
                $column = pg_field_name($result, $j);
                if ($column == 'other_info') $row[$j] = json_decode($row[$j]);
                $item->{$column} = $row[$j];
            }
            $item->save();
        }
        $this->info("line_types DONE ($count items).");

    }
}