<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;

use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Ramsey\Uuid\Uuid;

use App\Models\File;

class FileController extends Controller{

    public function __construct(){}

    /**
     * POST /file (file)
     */
    public function postFile(Request $request){

        //If file is missing -> 400
        if (!$request->hasFile('file')){
            return response()->json(['error' => 'Invalid File1'], 400);
        }

        //Get request
        $post_file = $request->file('file');

        //Array not supported -> 400
        if (gettype($post_file) === 'array'){
            return response()->json(['error' => 'Invalid File2'], 400);
        }

        //If not valid -> 400
        if (!$post_file->isValid()){
            return response()->json(['error' => 'Invalid File3'], 400);
        }

        //Get File Information
        $size = $post_file->getSize();
        $extension = $post_file->getClientOriginalExtension();
        $mimetype = $post_file->getClientMimeType();
        $directory = date('Y-m-d');
        $upload_time = time();

        //Determine UUID
        do{
            $uuid = Uuid::uuid4();
        }while(File::where('uuid', $uuid)->first());

        //Store File
        $post_file->storeAs("/", "$directory/$uuid.$extension", 'uploads');

        //Create Record
        $file = new File;
        $file->uuid = $uuid;
        $file->directory = $directory;
        $file->extension = $extension;
        $file->mimetype = $mimetype;
        $file->size = $size;
        $file->upload_time = $upload_time;
        $file->save();

        //Return Data
        return response()->json($file);

    }

    /**
     * GET /file/{uuid}
     */
    public function getFile(Request $request, $uuid){

        $uuid = explode('.', $uuid)[0];
        $file = File::where('uuid', $uuid)->first();

        //If file (in database) not found -> 404
        if (!$file){
            return response()->json(['error' => 'File Not Found'], 404);
        }

        //Fetch file
        $directory = $file->directory;
        $extension = $file->extension;
        $mimetype = $file->mimetype;

        //If file (in storage) not found -> 404
        $uploads = Storage::disk('uploads');
        if (!$uploads->exists("/$directory/$uuid.$extension")){
            return response()->json(['error' => 'File Not Found'], 404);
        }

        //Get File
        $path = storage_path("app/uploads/$directory/$uuid.$extension");
        $headers = ['Content-Type' => $mimetype]; 
        return new BinaryFileResponse($path, 200 , $headers);

    }

    /**
     * DELETE /file/{uuid}
     */
    public function deleteFile(Request $request, $uuid){

        $uuid = explode('.', $uuid)[0];
        $file = File::where('uuid', $uuid)->first();

        //If file (in database) not found -> 404
        if (!$file){
            return response()->json(['error' => 'File Not Found'], 404);
        }

        //Fetch file
        $directory = $file->directory;
        $extension = $file->extension;

        //Remove File
        Storage::disk('uploads')->delete("$directory/$uuid.$extension");

        //Remove Record
        $file->delete();

        //Return Success
        return response()->json((object)[]);

    }

}
