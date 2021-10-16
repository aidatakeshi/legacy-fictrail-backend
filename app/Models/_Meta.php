<?php

namespace App\Models;

class _Meta{

    public static $class = [
        //Not Applicable
        'fares_hr' => \App\Models\FaresHr::class,
        'map_editor_settings' => \App\Models\MapEditorSettings::class,

        //Plural Form
        'lines_stations' => \App\Models\Line_Station::class,
        'lines' => \App\Models\Line::class,
        'line_groups' => \App\Models\LineGroup::class,
        'line_types' => \App\Models\LineType::class,
        'operators' => \App\Models\Operator::class,
        'operator_types' => \App\Models\OperatorType::class,
        'prefectures' => \App\Models\Prefecture::class,
        'prefecture_areas' => \App\Models\PrefectureArea::class,
        'schdraft_categories' => \App\Models\SchdraftCategory::class,
        'schdraft_groups' => \App\Models\SchdraftGroup::class,
        'schdraft_templates' => \App\Models\SchdraftTemplate::class,
        'schedule_trips' => \App\Models\ScheduleTrip::class,
        'schedule_trip_stations' => \App\Models\ScheduleTripStation::class,
        'stations' => \App\Models\Station::class,
        'train_names' => \App\Models\TrainName::class,
        'train_types' => \App\Models\TrainType::class,
        'vehicle_performance_groups' => \App\Models\VehiclePerformanceGroup::class,
        'vehicle_performance_items' => \App\Models\VehiclePerformanceItem::class,

        //Singular Form
        'line_station' => \App\Models\Line_Station::class,
        'line' => \App\Models\Line::class,
        'line_group' => \App\Models\LineGroup::class,
        'line_type' => \App\Models\LineType::class,
        'operator' => \App\Models\Operator::class,
        'operator_type' => \App\Models\OperatorType::class,
        'prefecture' => \App\Models\Prefecture::class,
        'prefecture_area' => \App\Models\PrefectureArea::class,
        'schdraft_category' => \App\Models\SchdraftCategory::class,
        'schdraft_group' => \App\Models\SchdraftGroup::class,
        'schdraft_template' => \App\Models\SchdraftTemplate::class,
        'schedule_trip' => \App\Models\ScheduleTrip::class,
        'schedule_trip_station' => \App\Models\ScheduleTripStation::class,
        'station' => \App\Models\Station::class,
        'train_name' => \App\Models\TrainName::class,
        'train_type' => \App\Models\TrainType::class,
        'vehicle_performance_group' => \App\Models\VehiclePerformanceGroup::class,
        'vehicle_performance_item' => \App\Models\VehiclePerformanceItem::class,

        //Others
        'test_item' => \App\Models\TestItem::class,
        'test_items' => \App\Models\TestItem::class,
    ];

    public static $validation_error_messages = [
        'exists' => 'Not Exists',
        'gt' => 'Value Too Small',
        'gte' => 'Value Too Small',
        'lt' => 'Value Too Large',
        'lte' => 'Value Too Large',
        'min' => 'Too Short',
        'max' => 'Too Long',
        'size' => 'Incorrect Length',
        'string' => 'String Required',
        'uuid' => 'UUID Required',
        'integer' => 'Integer Required',
        'json' => 'JSON Required',
        'numeric' => 'Numeric Required',
        'boolean' => 'Boolean Required',
        'regex' => 'Invalid Format',
        'required' => 'Required',
        'unique' => 'Should be Unique',
    ];

}
