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
     * GET schdraft-output/template/{id}
     */
    public function getSchTemplateOutput(Request $request, $id){
        $sd_template = SchdraftTemplate::where('isDeleted', false)->where('id', $id)->first();
        if (!$sd_template) return response()->json(['error' => 'Not Found'], 404);

        //Prepare Data
        $data = $sd_template->sch_output;
        $operator = [];
        $train_type = [];
        $train_name = [];
        $station = [];

        //Get Data
        foreach ($data as $dt => $data_sub){
            foreach ($data_sub as $i => $item){
                //operator
                $operator_id = $item['operator_id'] ?? null;
                if ($operator_id){
                    if (!isset($operator[$operator_id])){
                        $operator[$operator_id] = Operator::where('id', $operator_id)
                        ->selectRaw('name_chi, name_eng, color')->first();
                    }
                    $data[$dt][$i]['operator'] = $operator[$operator_id];
                }else{
                    $data[$dt][$i]['operator'] = null;
                }
                //train_type
                $train_type_id = $item['train_type_id'] ?? null;
                if ($train_type_id){
                    if (!isset($train_type[$train_type_id])){
                        $train_type[$train_type_id] = TrainType::where('id', $train_type_id)
                        ->selectRaw('name_chi, name_eng, color')->first();
                    }
                    $data[$dt][$i]['train_type'] = $train_type[$train_type_id];
                }else{
                    $data[$dt][$i]['train_type'] = null;
                }
                //train_name
                $train_name_id = $item['train_name_id'] ?? null;
                if ($train_name_id){
                    if (!isset($train_name[$train_name_id])){
                        $train_name[$train_name_id] = TrainName::where('id', $train_name_id)
                        ->selectRaw('name_chi, name_eng, color')->first();
                    }
                    $data[$dt][$i]['train_name'] = $train_name[$train_name_id];
                }else{
                    $data[$dt][$i]['train_name'] = null;
                }
                //station
                foreach ($item['schedule'] as $j => $item_sch){
                    $station_id = $item_sch['station_id'] ?? null;
                    if ($station_id){
                        if (!isset($station[$station_id])){
                            $station[$station_id] = Station::where('id', $station_id)
                            ->selectRaw('name_chi, name_eng')->first();
                        }
                        $data[$dt][$i]['schedule'][$j]['station_id'] = $station[$station_id];
                    }else{
                        $data[$dt][$i]['schedule'][$j]['station_id'] = null;
                    }
                }
            }
        }

        //Return Data
        return response()->json(['data' => $data]);
    }

    /**
     * GET schdraft-output/line/{line_id}
     * GET schdraft-output/line/{line_id}/{direction}
     * GET schdraft-output/line/{line_id}/{direction}/{daytype}
     */
    public function getLineSchedule(Request $request, $line_id, $direction = null, $daytype = null){
        //Check if valid params, otherwise 404
        $line = Line::where('id', $line_id)->first();
        if (!$line){
            return response()->json(['error' => 'Line Not Found'], 404);
        }
        if ($direction !== null && $direction !== 'up' && $direction !== 'dn'){
            return response()->json(['error' => 'Invalid Direction'], 404);
        }
        if ($daytype !== null && $daytype !== 'wk' && $daytype !== 'ph'){
            return response()->json(['error' => 'Invalid Day Type'], 404);
        }
        //Proceed
        if ($direction && $daytype){
            return response()->json([
                'data' => $this->getLineSchedule_sub($line_id, $direction, $daytype),
            ]);
        }else if ($direction){
            return response()->json([
                'data' => [
                    'wk' => $this->getLineSchedule_sub($line_id, $direction, 'wk'),
                    'ph' => $this->getLineSchedule_sub($line_id, $direction, 'ph'),
                ],
            ]);
        }else{
            return response()->json([
                'data' => [
                    'up' => [
                        'wk' => $this->getLineSchedule_sub($line_id, 'up', 'wk'),
                        'ph' => $this->getLineSchedule_sub($line_id, 'up', 'ph'),
                    ],
                    'dn' => [
                        'wk' => $this->getLineSchedule_sub($line_id, 'dn', 'wk'),
                        'ph' => $this->getLineSchedule_sub($line_id, 'dn', 'ph'),
                    ],
                ],
            ]);
        }
    }

    private function getLineSchedule_sub($line_id, $direction, $daytype){
        return [$line_id, $direction, $daytype];
    }

    /**
     * GET schdraft-output/station/{station_id}/track/{track_no}
     * GET schdraft-output/station/{station_id}/track/{track_no}/{daytype}
     */
    public function getStationTrackSchedule(Request $request, $station_id, $track_no, $daytype = null){
        //Check if valid params, otherwise 404
        $station = Station::where('id', $station_id)->first();
        if (!$station){
            return response()->json(['error' => 'Station Not Found'], 404);
        }
        if ($daytype !== null && $daytype !== 'wk' && $daytype !== 'ph'){
            return response()->json(['error' => 'Invalid Day Type'], 404);
        }
        //Proceed
        if ($daytype){
            return response()->json([
                'data' => $this->getStationTrackSchedule_sub($station_id, $track_no, $daytype),
            ]);
        }else{
            return response()->json([
                'data' => [
                    'wk' => $this->getStationTrackSchedule_sub($station_id, $track_no, 'wk'),
                    'ph' => $this->getStationTrackSchedule_sub($station_id, $track_no, 'ph'),
                ],
            ]);
        }
    }

    private function getStationTrackSchedule_sub($station_id, $track_no, $daytype){
        return [$station_id, $track_no, $daytype];
    }

    /**
     * GET schdraft-output/station/{station_id}/line/{line_id}
     * GET schdraft-output/station/{station_id}/line/{line_id}/{direction}
     * GET schdraft-output/station/{station_id}/line/{line_id}/{direction}/{daytype}
     */
    public function getStationLineSchedule(Request $request, $station_id, $line_id, $direction = null, $daytype = null){
        //Check if valid params, otherwise 404
        $station = Station::where('id', $station_id)->first();
        if (!$station){
            return response()->json(['error' => 'Station Not Found'], 404);
        }
        $line = Line::where('id', $line_id)->first();
        if (!$line){
            return response()->json(['error' => 'Line Not Found'], 404);
        }
        if ($direction !== null && $direction !== 'up' && $direction !== 'dn'){
            return response()->json(['error' => 'Invalid Direction'], 404);
        }
        if ($daytype !== null && $daytype !== 'wk' && $daytype !== 'ph'){
            return response()->json(['error' => 'Invalid Day Type'], 404);
        }
        //Proceed
        if ($direction && $daytype){
            return response()->json([
                'data' => $this->getStationLineSchedule_sub($station_id, $line_id, $direction, $daytype),
            ]);
        }else if ($direction){
            return response()->json([
                'data' => [
                    'wk' => $this->getStationLineSchedule_sub($station_id, $line_id, $direction, 'wk'),
                    'ph' => $this->getStationLineSchedule_sub($station_id, $line_id, $direction, 'ph'),
                ],
            ]);
        }else{
            return response()->json([
                'data' => [
                    'up' => [
                        'wk' => $this->getStationLineSchedule_sub($station_id, $line_id, 'up', 'wk'),
                        'ph' => $this->getStationLineSchedule_sub($station_id, $line_id, 'up', 'ph'),
                    ],
                    'dn' => [
                        'wk' => $this->getStationLineSchedule_sub($station_id, $line_id, 'dn', 'wk'),
                        'ph' => $this->getStationLineSchedule_sub($station_id, $line_id, 'dn', 'ph'),
                    ],
                ],
            ]);
        }
    }

    private function getStationLineSchedule_sub($station_id, $line_id, $direction, $daytype){
        return [$station_id, $line_id, $direction, $daytype];
    }

    /**
     * GET schdraft-output/station/{station_id}/track-crossing-points
     */
    public function getStationTrackCrossingPoints(Request $request, $station_id){

        //Check if valid params, otherwise 404
        $station = Station::where('id', $station_id)->first();
        if (!$station){
            return response()->json(['error' => 'Station Not Found'], 404);
        }

        //Proceed
        

    }

}