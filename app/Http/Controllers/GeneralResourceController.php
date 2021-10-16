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

class GeneralResourceController extends Controller{

    public function __construct(){}

    /**
     * GET items/{type}
     * Queries:
     * sort: according to $sortable, $sort_default
     * {filter}: according to filters()
     * limit: according to $limit_default. If null, no limit
     * page: default is 1
     */
    public function getItems(Request $request, $type){

        //If class not found, 404
        $class = _Meta::$class[$type] ?? null;
        if (!$class){
            return response()->json(['error' => 'Resource Type Not Found'], 404);
        }

        //Start to Build Query
        $query = ($class)::where('isDeleted', false);

        //Apply Filters
        if (method_exists($class, 'filters')){
            $requests = $request->all();
            foreach ($requests as $key => $value){
                $where_statement = ($class)::filters($key, $value);
                if ($where_statement){
                    $query = $query->whereRaw($where_statement['query'], $where_statement['params']);
                }
            }
        }

        //Do Sorting (starting with minus sign means descending)
        $sort = $request->input('sort');
        if (!$sort){
            $sort = ($class)::$sort_default ?? null;
        }
        if ($sort){
            $sort = explode(',', $sort);
            foreach ($sort as $sub_sort){
                $direction = 'asc';
                if (substr($sub_sort, 0, 1) == '-'){
                    $sub_sort = substr($sub_sort, 1);
                    $direction = 'desc';
                }
                //If found in $sortable, do sorting
                if (in_array($sub_sort, ($class)::$sortable ?? [])){
                    $query = $query->orderBy($sub_sort, $direction);
                }
            }
        }

        //Make Count
        $count = $query->count();

        //Handle Limit ($limit_default)
        $limit = intval($request->input('limit'));
        if (!$limit) $limit = ($class)::$limit_default ?? 0;
        if ($limit){
            $query = $query->limit($limit);
        }

        //Handle Page
        $page = intval($request->input('page') ?? 1);
        if ($limit && $page && $count){
            $pages = ceil($count / $limit);
            if ($page < 1) $page = 1;
            else if ($page > $pages) $page = $pages;
            $query = $query->offset($limit * ($page - 1));
        }else{
            $pages = null;
            $page = null;
        }

        //Get Results
        $results = $query->get();

        //Call displayData($request)
        if (method_exists($class, 'displayData')){
            foreach ($results as $i => $result){
                $results[$i] = $results[$i]->displayData($request) ?? $results[$i];
            }
        }

        //Call whenGet($request)
        if (method_exists($class, 'whenGet')){
            foreach ($results as $i => $result){
                $results[$i]->whenGet($request);
            }
        }

        //Return Result
        return [
            'count' => $count,
            'page' => $page,
            'pages' => $pages,
            'data' => $results,
        ];

    }
    
    /**
     * GET items/{type}/{id}
     */
    public function getItem(Request $request, $type, $id){

        //If class not found, 404
        $class = _Meta::$class[$type] ?? null;
        if (!$class){
            return response()->json(['error' => 'Resource Type Not Found'], 404);
        }

        //If item not found, 404
        $item = ($class)::where('id', $id)->where('isDeleted', false)->first();
        if (!$item){
            return response()->json(['error' => 'Item Not Found'], 404);
        }

        //Call displayData($request)
        if (method_exists($class, 'displayData')){
            $additional_data = (object)($item->displayData($request) ?? []);
            foreach ($additional_data as $k => $v){
                $item = $item->displayData($request) ?? $item;
            }
        }

        //Call whenGet($request)
        if (method_exists($class, 'whenGet')){
            $item->whenGet($request);
        }

        //Return Data
        return [
            'data' => $item,
        ];

    }

    /**
     * POST items/{type}
     */
    public function createItem(Request $request, $type){

        //If class not found, 404
        $class = _Meta::$class[$type] ?? null;
        if (!$class){
            return response()->json(['error' => 'Resource Type Not Found'], 404);
        }

        //For JSON requests, encode it to string first before proceeding to validation
        $data = $request->all();
        $data_for_validation = $request->all();
        foreach ($data_for_validation as $k => $v){
            if (is_object($data_for_validation[$k]) || is_array($data_for_validation[$k])){
                $data_for_validation[$k] = json_encode( $data_for_validation[$k]);
            }
        }

        //Do validation, if failed, 400
        $validator = Validator::make($data_for_validation, ($class)::$validations_new ?? [], _Meta::$validation_error_messages);
        if ($validator->fails()){
            return response()->json([
                'error' => 'Validation Errors',
                'details' => $validator->errors(),
            ], 400);
        }

        //If ID clashes, return 400
        $id = $request->input('id');
        if ($id){
            if (($class)::where('id', $id)->first()){
                return response()->json(['error' => 'ID Clash'], 400);
            }
        }
        //If ID not provided, use UUID
        else{
            do{
                $id = Uuid::uuid4();
            }while(($class)::where('id', $id)->first());
        }

        //Proceed, create new resource
        $item = new $class;
        $item->id = $id;
        foreach ($data as $k => $v) $item->{$k} = $v;
        $item->save();

        //Call whenCreated($request)
        if (method_exists($class, 'whenCreated')){
            $item->whenCreated($request);
        }
        
        //Return Data
        return [
            'data' => $item,
        ];

    }

    /**
     * PATCH items/{type}/{id}
     */
    public function updateItem(Request $request, $type, $id){

        //If class not found, 404
        $class = _Meta::$class[$type] ?? null;
        if (!$class){
            return response()->json(['error' => 'Resource Type Not Found'], 404);
        }

        //If item not found, 404
        $item = ($class)::where('id', $id)->where('isDeleted', false)->first();
        if (!$item){
            return response()->json(['error' => 'Item Not Found'], 404);
        }

        //For JSON requests, encode it to string first before proceeding to validation
        $data = $request->all();
        $data_for_validation = $request->all();
        foreach ( $data_for_validation as $k => $v){
            if (is_object($data_for_validation[$k]) || is_array($data_for_validation[$k])){
                $data_for_validation[$k] = json_encode( $data_for_validation[$k]);
            }
        }

        //Do validation, if failed, 400
        $validator = Validator::make($data_for_validation, ($class)::$validations_update ?? [], _Meta::$validation_error_messages);
        if ($validator->fails()){
            return response()->json([
                'error' => 'Validation Errors',
                'details' => $validator->errors(),
            ], 400);
        }

        //Proceed, make update
        $item->update($data);

        //Call whenSet($request)
        if (method_exists($class, 'whenSet')){
            $item->whenSet($request);
        }
        
        //Return Data
        return [
            'data' => $item,
        ];

    }

    /**
     * POST items/{type}/{id}
     */
    public function duplicateItem(Request $request, $type, $id){

        //If class not found, 404
        $class = _Meta::$class[$type] ?? null;
        if (!$class){
            return response()->json(['error' => 'Resource Type Not Found'], 404);
        }

        //If item not found, 404
        $item = ($class)::where('id', $id)->where('isDeleted', false)->first();
        if (!$item){
            return response()->json(['error' => 'Item Not Found'], 404);
        }

        //If ID clashes, return 400
        $new_id = $request->input('id');
        if ($new_id){
            if (($class)::where('id', $new_id)->first()){
                return response()->json(['error' => 'ID Clash'], 400);
            }
        }
        //If ID not provided, use UUID
        else{
            do{
                $new_id = Uuid::uuid4();
            }while(($class)::where('id', $new_id)->first());
        }

        //Make Duplication
        $new_item = new $class;
        $data = $item->toArray();
        foreach ($data as $k => $v){
            $new_item->{$k} = $v;
        }
        $new_item->id = $new_id;
        $new_item->save();

        //Call whenDuplicated($request)
        if (method_exists($class, 'whenDuplicated')){
            $new_item->whenDuplicated($request);
        }
        
        //Return Data
        return [
            'data' => $new_item,
        ];

    }

    /**
     * DELETE items/{type}/{id}
     */
    public function removeItem(Request $request, $type, $id){

        //If class not found, 404
        $class = _Meta::$class[$type] ?? null;
        if (!$class){
            return response()->json(['error' => 'Resource Type Not Found'], 404);
        }

        //If item not found, 404
        $item = ($class)::where('id', $id)->where('isDeleted', false)->first();
        if (!$item){
            return response()->json(['error' => 'Item Not Found'], 404);
        }

        //Call whenRemoved($request)
        if (method_exists($class, 'whenRemoved')){
            $item->whenRemoved($request);
        }

        //Hard delete
        $item_old = clone $item;
        $item->delete();
        
        //Return Data
        return [
            'data_old' => $item_old,
        ];

    }

}
