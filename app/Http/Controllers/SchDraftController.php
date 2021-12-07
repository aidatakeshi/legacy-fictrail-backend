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
                //Get Line
                $line = Line::where('id', $line_id)->first();
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
                $item['travel_time'] = $vp_item->getTravelTime($line, $item['line_stations'], $is_upbound, $is_express_track);
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

        //Sort Data
        foreach ($data as $dt => $data_sub){
            usort($data[$dt], function($a, $b){
                $time_a = $a['pivot_time'] + $a['pivot_shift'];
                $time_b = $b['pivot_time'] + $b['pivot_shift'];
                if ($time_a > $time_b) return +1;
                if ($time_a < $time_b) return -1;
                return 0;
            });
        }

        //Get Data
        foreach ($data as $dt => $data_sub){
            foreach ($data_sub as $i => $item){
                //operator
                $operator_id = $item['operator_id'] ?? null;
                if ($operator_id){
                    if (!isset($operator[$operator_id])){
                        $operator[$operator_id] = Operator::where('id', $operator_id)
                        ->selectRaw('name_chi, name_eng, color, color_text')->first();
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
                        ->selectRaw('name_chi, name_eng, color, color_text')->first();
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
                        ->selectRaw('name_chi, name_eng, color, color_text')->first();
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
                'data_line' => $line,
                'data' => $this->getLineSchedule_sub($line, $direction, $daytype),
            ]);
        }else if ($direction){
            return response()->json([
                'data_line' => $line,
                'data' => [
                    'wk' => $this->getLineSchedule_sub($line, $direction, 'wk'),
                    'ph' => $this->getLineSchedule_sub($line, $direction, 'ph'),
                ],
            ]);
        }else{
            return response()->json([
                'data_line' => $line,
                'data' => [
                    'up' => [
                        'wk' => $this->getLineSchedule_sub($line, 'up', 'wk'),
                        'ph' => $this->getLineSchedule_sub($line, 'up', 'ph'),
                    ],
                    'dn' => [
                        'wk' => $this->getLineSchedule_sub($line, 'dn', 'wk'),
                        'ph' => $this->getLineSchedule_sub($line, 'dn', 'ph'),
                    ],
                ],
            ]);
        }
    }

    private function getLineSchedule_sub($line, $direction, $daytype){
        $is_upbound = ($direction == 'up');

        //Get Station List
        $station_list = $line->getStationList($direction == 'up');
        foreach ($station_list as $i => $item){
            $station = Station::where('id', $item->station_id)->first();
            $station_list[$i]['name_chi'] = $station ? $station->name_chi : null;
            $station_list[$i]['name_eng'] = $station ? $station->name_eng : null;
        }

        //Get Templates related to this line
        $templates = SchdraftTemplate::where('isDeleted', false)->where('is_enabled', true)
        ->whereRaw('line_ids_involved LIKE ?', '%'.$line->id.'%')->get();

        //Prepare data
        $schedule = [];

        //For each template
        foreach ($templates as $template){
            if (!$template->isEnabled()) continue;
            $trips = $template->sch_output[$daytype] ?? [];
            //For each trip
            foreach ($trips as $trip){
                //Check if line involved in this trip
                $line_involved = false;
                foreach ($trip['schedule'] as $item){
                    if (($item['line_id'] == $line->id) && ($item['is_upbound'] == $is_upbound)){
                        $line_involved = true;
                        break;
                    }
                }
                //Push to schedule array
                if ($line_involved){
                    array_push($schedule, $this->matchLineFormat($station_list, $trip, $line, $direction));
                }
            }
        }

        //Order Trips by pivot_time, pivot_shift
        usort($schedule, function ($a, $b){
            $a_time = ($a['pivot_time'] ?? 0) + ($a['pivot_shift'] ?? 0);
            $b_time = ($b['pivot_time'] ?? 0) + ($b['pivot_shift'] ?? 0);
            if ($a_time > $b_time) return +1;
            if ($a_time < $b_time) return -1;
            return 0;
        });

        //Prepare Additional Data for Each Trip
        $operator = [];
        $train_type = [];
        $train_name = [];
        foreach ($schedule as $i => $item){
            //operator
            $operator_id = $item['operator_id'] ?? null;
            if ($operator_id){
                if (!isset($operator[$operator_id])){
                    $operator[$operator_id] = Operator::where('id', $operator_id)
                    ->selectRaw('name_chi, name_eng, color, color_text')->first();
                }
                $schedule[$i]['operator'] = $operator[$operator_id];
            }else{
                $schedule[$i]['operator'] = null;
            }
            //train_type
            $train_type_id = $item['train_type_id'] ?? null;
            if ($train_type_id){
                if (!isset($train_type[$train_type_id])){
                    $train_type[$train_type_id] = TrainType::where('id', $train_type_id)
                    ->selectRaw('name_chi, name_eng, color, color_text')->first();
                }
                $schedule[$i]['train_type'] = $train_type[$train_type_id];
            }else{
                $schedule[$i]['train_type'] = null;
            }
            //train_name
            $train_name_id = $item['train_name_id'] ?? null;
            if ($train_name_id){
                if (!isset($train_name[$train_name_id])){
                    $train_name[$train_name_id] = TrainName::where('id', $train_name_id)
                    ->selectRaw('name_chi, name_eng, color, color_text')->first();
                }
                $schedule[$i]['train_name'] = $train_name[$train_name_id];
            }else{
                $schedule[$i]['train_name'] = null;
            }
        }

        //Return Data
        return [
            'stations' => $station_list,
            'schedule' => $schedule,
        ];
    }

    public function matchLineFormat($station_list, $trip, $line, $direction){
        $is_upbound = ($direction == 'up');

        //Remove unwanted attributes
        unset($trip['wk'], $trip['ph']);
        unset($trip['crossings']);
        unset($trip['begin_index'], $trip['terminate_index']);

        //Determine starting / ending station
        $last = count($trip['schedule']) - 1;
        $trip['station_begin_id'] = $trip['schedule'][0]['station_id'];
        $trip['station_terminate_id'] = $trip['schedule'][$last]['station_id'];
        $station = Station::where('id', $trip['station_begin_id'])->first();
        $trip['station_begin_name_chi'] = $station ? $station->name_chi : null;
        $trip['station_begin_name_eng'] = $station ? $station->name_eng : null;
        $station = Station::where('id', $trip['station_terminate_id'])->first();
        $trip['station_terminate_name_chi'] = $station ? $station->name_chi : null;
        $trip['station_terminate_name_eng'] = $station ? $station->name_eng : null;

        //Duplicate schedule
        $schedule_new = array_fill(0, count($station_list), null);
        $last = count($trip['schedule']) - 1;
        foreach ($trip['schedule'] as $i => $trip_item){
            $trip_item_prev = $trip['schedule'][$i - 1] ?? null;
            $station_id = $trip_item['station_id'];
            $create_new_item = false;
            $new_item = [
                'is_express_track' => null, 'time1' => null, 'time2' => null, 'is_pass' => null, 'track' => null,
                'mileage_km' => null, 'no_tracks' => null,
            ];
            //time1 (arrive)
            if ($trip_item['time1'] !== null && $trip_item_prev){
                //Same Line && Direction
                if ($trip_item_prev['line_id'] == $line->id && $trip_item_prev['is_upbound'] == $is_upbound){
                    $index = Line::getIndexOfStation($station_list, $station_id, true);
                    if ($index !== null){
                        $new_item['is_express_track'] = $trip_item_prev['is_express_track'];
                        $new_item['time1'] = $trip_item['time1'];
                        $create_new_item = true;
                    }
                }
            }
            //time2 (depart)
            if ($trip_item['time2'] !== null){
                //Same Line & Direction
                if ($trip_item['line_id'] == $line->id && $trip_item['is_upbound'] == $is_upbound){
                    $index = Line::getIndexOfStation($station_list, $station_id, false);
                    if ($index !== null){
                        $new_item['is_express_track'] = $trip_item['is_express_track'];
                        $new_item['time2'] = $trip_item['time2'];
                        $create_new_item = true;
                    }
                }
                //Special Case: Changing Line
                else if ($trip_item_prev){
                    if ($trip_item_prev['line_id'] != $trip_item['line_id']){
                        $index = Line::getIndexOfStation($station_list, $station_id, true);
                        if ($index !== null){
                            $new_item['is_express_track'] = $trip_item_prev['is_express_track'];
                            $new_item['time1'] = $trip_item['time1'] ?? $trip_item['time2'];
                            $create_new_item = true;
                        }
                    }
                }
            }
            //Add to $schedule_new
            if ($create_new_item){
                $new_item['track'] = $trip_item['track'];
                $new_item['is_pass'] = $trip_item['is_pass'];
                $new_item['mileage_km'] = $station_list[$index]['mileage_km'];
                if ($direction == 'dn'){
                    if ($station_list[$index + 1] ?? null){
                        $new_item['no_tracks'] = $station_list[$index + 1]['no_tracks'];
                    }else{
                        $new_item['no_tracks'] = null;
                    }
                }else{
                    if ($station_list[$index] ?? null){
                        $new_item['no_tracks'] = $station_list[$index]['no_tracks'];
                    }else{
                        $new_item['no_tracks'] = null;
                    }
                }
                $on_line = $line->id == ($trip_item ? $trip_item['line_id'] : null);
                $on_line_prev = $line->id == ($trip_item_prev ? $trip_item_prev['line_id'] : null);
                //Flags: is_trip_begin, is_trip_terminate, is_thru_in, is_thru_out (returned only when true)
                if ($i == 0){
                    $new_item['is_trip_begin'] = true;
                }else if ($i == $last){
                    $new_item['is_trip_terminate'] = true;
                }else if ($on_line && !$on_line_prev){
                    $new_item['is_thru_in'] = true;
                }else if (!$on_line && $on_line_prev){
                    $new_item['is_thru_out'] = true;
                }
                $schedule_new[$index] = $new_item;
            }
        }

        //Fill null items in line
        $in_line = false;
        $mileage_ref_1 = 0;
        $no_tracks = null;
        foreach ($schedule_new as $i => $item){
            //Not Null
            if ($item !== null){
                if (isset($item['is_trip_begin']) || isset($item['is_thru_in'])){
                    $in_line = true;
                }else if (isset($item['is_trip_terminate']) || isset($item['is_thru_out'])){
                    $in_line = false;
                }
                $is_express_track = $item['is_express_track'];
                $mileage_ref_1 = $item['mileage_km'];
                $time_ref_1 = $item['time2'] ?? $item['time1'];
                $no_tracks = $item['no_tracks'];
            }
            //Null & Between Non-Null Items
            else if ($in_line){
                //Find Next Non-Null Item
                $mileage_ref_2 = 0;
                $time_ref_2 = 0;
                for ($j = $i + 1; $j < count($schedule_new); $j++){
                    if ($schedule_new[$j]){
                        $mileage_ref_2 = $schedule_new[$j]['mileage_km'];
                        $time_ref_2 = $schedule_new[$j]['time1'] ?? $schedule_new[$j]['time2'];
                        break;
                    }
                }
                //Do Intepolation
                $mileage_here = $station_list[$i]['mileage_km'];
                $schedule_new[$i] = [
                    'is_express_track' => $is_express_track,
                    'time1' => null, 'time2' => null, 'is_pass' => true, 'track' => null,
                    'time_intepolated' => $time_ref_1 + ($time_ref_2 - $time_ref_1)
                    / ($mileage_ref_2 - $mileage_ref_1) * ($mileage_here - $mileage_ref_1),
                    'mileage_km' => $mileage_here,
                    'no_tracks' => $no_tracks,
                ];
            }
        }

        //Replace schedule to the converted one (schedule_line)
        $trip['schedule_line'] = $schedule_new;
        unset($trip['schedule']);
        return $trip;
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