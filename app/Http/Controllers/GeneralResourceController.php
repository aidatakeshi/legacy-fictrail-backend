<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;

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
            $direction = 'asc';
            if (substr($sort, 0, 1) == '-'){
                $sort = substr($sort, 1);
                $direction = 'desc';
            }
            //If found in $sortable, do sorting
            if (in_array($sort, ($class)::$sortable ?? [])){
                $query = $query->orderBy($sort, $direction);
            }
        }

        //Make Count
        $count = $query->count();

        //Handle Limit
        $limit = intval($request->input('limit'));
        if (!$limit) $limit = ($class)::$limit_default ?? 0;
        if ($limit){
            $query = $query->limit($limit);
        }

        //Handle Page
        $page = intval($request->input('page'));
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
        $item = ($class)::where('id', $id)->first();
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

    }

    /**
     * POST items/{type}/{id}
     */
    public function duplicateItem(Request $request, $type, $id){

    }

    /**
     * PATCH items/{type}/{id}
     */
    public function updateItem(Request $request, $type, $id){

    }

    /**
     * DELETE items/{type}/{id}
     */
    public function removeItem(Request $request, $type, $id){

    }

}
