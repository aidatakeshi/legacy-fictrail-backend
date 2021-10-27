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
use \App\Models\Operator;
use \App\Models\LineType;
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
            //"show_segments"
            if (!$request->input('show_segments')){
                unset($line_stations[$i]->segments);
            }
            //Get also station info
            //"from_selecter" -> Only essential fields for selecter
            if ($request->input('from_selecter')){
                $station = Station::where('id', $line_stations[$i]->station_id)
                ->selectRaw('name_chi, name_eng')->first();
                $line_stations[$i]->name_chi = ($station) ? $station->name_chi : null;
                $line_stations[$i]->name_eng = ($station) ? $station->name_eng : null;
            }else{
                $line_stations[$i]->station = $line_station->station;
                //Get also the prefecture as well
                if ($line_stations[$i]->station){
                    $line_stations[$i]->station->prefecture = $line_stations[$i]->station->prefecture;
                }
            }
        }
        //Return results
        return [
            'data_line' => Line::where('id', $line_id)->first(),
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
            'data_station' => Station::where('id', $station_id)->first(),
            'data' => $line_stations,
        ];
    }

    public function getLineStatsHR(Request $request){

        //Prepare Data
        $data = [
            'mileage_by_operator' => [],
            'mileage_by_line_type' => [],
            'mileage_by_line_type_operator' => [],
            'mileage_total' => 0,
            'operators' => [],
            'line_types' => [],
        ];

        //Get Operators
        $operators = Operator::where('is_passenger_hr', true)->where('isDeleted', false)
        ->orderBy('id', 'asc')->get();

        //Get Line Types
        $line_types = LineType::where('isDeleted', false)->orderBy('sort', 'asc')->get();

        //Prepare Array
        foreach ($operators as $operator){
            $data['operators'][$operator->id] = $operator;
            $data['mileage_by_operator'][$operator->id] = 0;
        }

        foreach ($line_types as $line_type){
            $data['line_types'][$line_type->id] = $line_type;
            $data['mileage_by_line_type'][$line_type->id] = 0;
            $data['mileage_by_line_type_operator'][$line_type->id] = [];
            foreach ($operators as $operator){
                $data['mileage_by_line_type_operator'][$line_type->id][$operator->id] = 0;
            }
        }

        //For Each Line
        $lines = Line::where('isDeleted', false)->get();
        foreach ($lines as $line){
            $operator_id = $line->operator_id;
            $line_type_id = $line->line_type_id;
            $length = $line->length_km;
            if (!isset($data['operators'][$operator_id])) continue;
            $data['mileage_by_line_type'][$line_type_id] += $length;
            $data['mileage_by_operator'][$operator_id] += $length;
            $data['mileage_by_line_type_operator'][$line_type_id][$operator_id] += $length;
            $data['mileage_total'] += $length;

        }

        //Round results
        foreach ($data['mileage_by_operator'] as $i => $value){
            $data['mileage_by_operator'][$i] = round($data['mileage_by_operator'][$i] * 10) / 10;
        }
        foreach ($data['mileage_by_line_type'] as $i => $value){
            $data['mileage_by_line_type'][$i] = round($data['mileage_by_line_type'][$i] * 10) / 10;
        }
        foreach ($data['mileage_by_line_type_operator'] as $i => $value){
            foreach ($data['mileage_by_line_type_operator'][$i] as $j => $value2){
                $data['mileage_by_line_type_operator'][$i][$j]
                = round($data['mileage_by_line_type_operator'][$i][$j] * 10) / 10;
            }
        }
        $data['mileage_total'] = round($data['mileage_total'] * 10) / 10;

        //Return Data
        return response()->json($data);

    }

    public function getStationByIDs(Request $request){
        $ids = $request->input('ids') ?? [];
        $from_selecter = $request->input('from_selecter');
        if (!is_array($ids)) $ids = [];
        //Retrieve Data
        $results = [];
        foreach ($ids as $id){
            $query = Station::where('isDeleted', false)->where('id', $id);
            if ($from_selecter){
                $query = $query->selectRaw('id, name_chi, name_eng');
            }
            $station = $query->first();
            if ($station) array_push($results, $station);
        }
        //Return Data
        return response()->json(['data' => $results]);
    }

    public function getLineName(Request $request, $line_id){
        $line = Line::where('id', $line_id)->selectRaw("name_chi, name_eng")->first();
        if (!$line) return response()->json(["name_chi" => null, "name_eng" => null]);
        return response()->json($line);
    }

    public function getStationName(Request $request, $station_id){
        $station = Station::where('id', $station_id)->selectRaw("name_chi, name_eng")->first();
        if (!$station) return response()->json(["name_chi" => null, "name_eng" => null]);
        return response()->json($station);
    }

}