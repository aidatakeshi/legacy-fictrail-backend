<?php

namespace App\Models;

class _Meta{

    public static $class = [
        'fares_hr' => \App\Models\FaresHr::class,
        'lines_stations' => \App\Models\Line_Station::class,
        'lines' => \App\Models\Line::class,
        'line_groups' => \App\Models\LineGroup::class,
        'line_types' => \App\Models\LineType::class,
        'map_editor_settings' => \App\Models\MapEditorSettings::class,
        'operators' => \App\Models\Operator::class,
        'operator_types' => \App\Models\OperatorType::class,
        'prefectures' => \App\Models\Prefecture::class,
        'prefecture_areas' => \App\Models\OperatorType::class,
        'schdraft_categories' => \App\Models\SchdraftCategory::class,
        'schdraft_groups' => \App\Models\SchdraftGroup::class,
        'schdraft_templates' => \App\Models\SchdraftTemplate::class,
        'schedule_trips' => \App\Models\ScheduleTrip::class,
        'schedule_trip_stations' => \App\Models\ScheduleTripStation::class,
        'train_names' => \App\Models\TrainNames::class,
        'train_types' => \App\Models\TrainTypes::class,
        'vehicle_performance_groups' => \App\Models\VehiclePerformanceGroup::class,
        'vehicle_performance_items' => \App\Models\VehiclePerformanceItem::class,
    ],

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
        'regex' => 'Invalid Format',
        'required' => 'Required',
        'unique' => 'Should be Unique',
    ],

}
