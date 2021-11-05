<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Ramsey\Uuid\Uuid;

use App\Models\SchdraftCategory;
use App\Models\SchdraftGroup;
use App\Models\SchdraftTemplate;
use App\Models\Operator;
use App\Models\TrainType;
use App\Models\TrainName;
use App\Models\Line;
use App\Models\Station;
use App\Models\Line_Station;
use App\Models\VehiclePerformanceItem;

class SchDraftController extends Controller{

    public function __construct(){}

    /**
     * POST schdraft-editor/sch-template-info
     */
    public function provideSchTemplateInfo(Request $request){

        //Check vehicle performance (v.p.) item
        $vp_id = $request->input('vehicle_performance_id');
        if (!$vp_id){
            return response()->json(['error' => 'Vehicle Performance ID Required'], 400);
        }
        $vp_item = VehiclePerformanceItem::where('id', $vp_id)->where('isDeleted', false)->first();
        if (!$vp_item){
            return response()->json(['error' => 'Vehicle Performance Item Not Found'], 400);
        }
        if (!$vp_item->has_calc_results){
            return response()->json(['error' => 'Vehicle Performance Item Has No Calculation Results'], 400);
        }

        //Check sch_template
        $sch_template = $request->input('sch_template');
        if (!is_array($sch_template)){
            return response()->json(['error' => 'Invalid sch_template'], 400);
        }

        //Prepare Variables
        $mileage_km = 0;
        $data = [];
        $isFirst = true;
        
        //For each sch_template item
        foreach ($sch_template as $i => $st_item){
            $item = [
                'distance_km' => null,
                'mileage_km' => 0,
            ];

            //If is_cross, skip
            if ($st_item['is_cross'] ?? null){
                array_push($data, ['is_cross' => true]);
                continue;
            }

            if (!$isFirst){
                $line_id = $st_item_prev['line_id'] ?? null;
                $station1_id = $st_item_prev['station_id'] ?? null;
                $station2_id = $st_item['station_id'] ?? null;
                $is_upbound = $st_item_prev['is_upbound'] ?? false;
                $is_express_track = $st_item_prev['is_express_track'] ?? false;
                //Get Line-Station Items
                $item['line_stations'] = Line_Station::getLineStationItems($line_id, $station1_id, $station2_id, $is_upbound);
                foreach ($item['line_stations'] as $ls_item){
                    $ls_item['station1_name_chi'] = Station::getNameById($ls_item['station1_id']);
                    $ls_item['station2_name_chi'] = Station::getNameById($ls_item['station2_id']);
                }
                //Get Distance
                $distance_km = 0;
                foreach ($item['line_stations'] as $ls_item) $distance_km += $ls_item->distance_km ?? 0;
                $mileage_km += $distance_km;
                $distance_km = round($distance_km, 1);
                $mileage_km = round($mileage_km, 1);
                $item['distance_km'] = $distance_km;
                $item['mileage_km'] = $mileage_km;
                //Get Travel Time
                $item['travel_time'] = $vp_item->getTravelTime($item['line_stations'], $is_upbound, $is_express_track);
            }
            
            //Add to returned Data
            array_push($data, (object)$item);
            $st_item_prev = $st_item;
            $isFirst = false;
        }

        //Return Data
        return response()->json(['data' => $data]);

    }

    /**
     * GET schdraft-template/{id}/sch-output
     */
    public function getSchTemplateOutput(Request $request, $id){
        $sd_template = SchdraftTemplate::where('isDeleted', false)->where('id', $id)->first();
        if (!$sd_template) return response()->json(['error' => 'Not Found'], 404);
        //Return Data
        $sd_template->whenSet($request);
        return response()->json(['data' => $sd_template->sch_output]);
    }

}