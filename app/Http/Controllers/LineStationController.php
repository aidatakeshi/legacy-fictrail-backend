<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Ramsey\Uuid\Uuid;

use \App\Models\_Meta;
use \App\Models\Line;
use \App\Models\Station;
use \App\Models\Line_Station;

class LineStationController extends Controller{

    public function __construct(){}

    public function getStationsOfLine(Request $request, $line_id){
        //Select scope? "from_selecter" -> Only essential fields for selecter
        $selectScope = '*';
        if ($request->input('from_selecter')){
            $selectScope = 'station_id';
        }
        //Make query
        $line_stations = Line_Station::where('isDeleted', false)->where('line_id', $line_id)
        ->orderBy('sort', 'asc')->selectRaw($selectScope)->get();
        foreach ($line_stations as $i => $line_station){
            //Unset certain fields
            unset($line_stations[$i]->line_id);
            unset($line_stations[$i]->segments);
            //Get also station info
            //"from_selecter" -> Only essential fields for selecter
            if ($request->input('from_selecter')){
                $station = Station::where('id', $line_stations[$i]->station_id)
                ->selectRaw('name_chi, name_eng')->first();
                $line_stations[$i]->name_chi = ($station) ? $station->name_chi : null;
                $line_stations[$i]->name_eng = ($station) ? $station->name_eng : null;
            }else{
                $line_stations[$i]->station = $line_station->station;
            }
        }
        //Return results
        return [
            'data' => $line_stations,
        ];
    }

    public function getLinesOfStation(Request $request, $station_id){
        //Select scope? "from_selecter" -> Only essential fields for selecter
        $selectScope = '*';
        if ($request->input('from_selecter')){
            $selectScope = 'line_id';
        }
        //Make query
        $line_stations = Line_Station::where('isDeleted', false)->where('station_id', $station_id)
        ->orderBy('id', 'asc')->selectRaw($selectScope)->get();
        foreach ($line_stations as $i => $line_station){
            //Unset certain fields
            unset($line_stations[$i]->station_id);
            unset($line_stations[$i]->segments);
            //Get also station info
            //"from_selecter" -> Only essential fields for selecter
            if ($request->input('from_selecter')){
                $line = Line::where('id', $line_stations[$i]->line_id)
                ->selectRaw('name_chi, name_eng')->first();
                $line_stations[$i]->name_chi = ($line) ? $line->name_chi : null;
                $line_stations[$i]->name_eng = ($line) ? $line->name_eng : null;
            }else{
                $line_stations[$i]->line = $line_station->line;
            }
        }
        //Return results
        return [
            'data' => $line_stations,
        ];
    }

}