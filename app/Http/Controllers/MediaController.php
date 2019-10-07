<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\User;
use App\Role;
use App\Media;
use App\MediaFile;
use Carbon\Carbon;

class MediaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $media = Media::with('files')->get();
        if($media->count() > 1)
        {
            $media->transform(function ($entry) {
                $entry->files->transform(function ($item) {
                    unset($item->media_id);
            
                    return $item;
                });    
                return $entry;
            });
        }
        else if($media->count() == 1){
            $media = Media::with('files')->first();
            $media->files->transform(function ($item) {
                unset($item->media_id);
        
                return $item;
            });    
        }

        return response()->json(['media' => $media], 200);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'min:3', 'regex:/^[A-Za-z]+$/'],
            'short_description' => ['required'],
            'description' => ['required'],
            'trailer_url' => ['required', 'regex:/www.youtube(?:-nocookie)?.com\/(?:v|embed)\/([a-zA-Z0-9-_]+).*/'],
            'image' => ['required', 'array', 'min:3', 'max:3'],
            'image.*' => ['required','file','mimes:jpg,jpeg,png,bmp'],
            'imdb_rating' => ['numeric', 'between:0,10'],
            'actor_id' => ['array'],
            'actor_id.*' => ['exists:actors,id'],
        ]);
        
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $media = new Media;
        $media->name = $request->name;
        $media->short_description = $request->short_description;
        $media->description = $request->description;
        $media->trailer_url = $request->trailer_url;
        if($request->imdb_rating != null)
            $media->imdb_rating = $request->imdb_rating;
        $media->save();

        $actor_data = array();

        if($request->actor_id != null)
        {
            foreach($request->actor_id as $actor_id)
            {
                array_push($actor_data, ['media_id' => $media->id, 'actor_id' => $actor_id]);
            }
        }

        DB::table('media_actors')->insert($actor_data);

        $file_data = array();

        $counter = 0;
        foreach($request->image as $image)
        {
            $currentTimeStamp = Carbon::now()->format('Y-m-d H:i:s.u');
            array_push($file_data, ['media_id' => $media->id, 'folder' => 'images', 'name' => 'image['.$counter.'].'.$image->getClientOriginalExtension(), 'created_at' => $currentTimeStamp, 'updated_at' => $currentTimeStamp]);
            $image->storeAs('media/'.$media->id.'/images', 'image['.$counter.'].'.$image->getClientOriginalExtension());
            $counter++;
        }
        DB::table('media_files')->insert(array_reverse($file_data));
        $media = Media::with('files', 'actors')->find($media->id);
        $media->files->transform(function ($item) {
            unset($item->media_id);
            return $item;
        });    
        return response()->json(['message' => 'Media has been successfully created', 'media' => $media], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if(Media::find($id) === null)
            return response()->json(['message' => 'Media with specified id does not exist'], 404);
        $media = Media::with('files')->find($id);
        $media->files->transform(function ($item) {
            unset($item->media_id);
    
            return $item;
        });   
        return response()->json(['role' => $media], 200);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if(Media::find($id) === null)
            return response()->json(['message' => 'Media with specified id does not exist'], 404);

        $validator = Validator::make($request->all(), [
            'name' => ['min:3', 'regex:/^[A-Za-z]+$/'],
            'short_description' => [],
            'description' => [],
            'trailer_url' => ['regex:/www.youtube(?:-nocookie)?.com\/(?:v|embed)\/([a-zA-Z0-9-_]+).*/'],
            'image' => ['array', 'max:3'],
            'image.*' => ['file','mimes:jpg,jpeg,png,bmp'],
            'imdb_rating' => ['numeric', 'between:0,10'],
        ]);
        
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

       try {
            $media = Media::with('files')->find($id);
            if($request->name != null)
                $media->name = $request->name;
            if($request->short_description != null)    
                $media->short_description = $request->short_description;
            if($request->description != null)    
                $media->description = $request->description;
            if($request->trailer_url != null)
                $media->trailer_url = app('hash')->make($request->trailer_url);
            if($request->imdb_rating != null)
                $media->imdb_rating = $request->imdb_rating;
            if($request->image != null)
            {
                $file_data = array();
                $ids_to_delete = array();
                $counter = 0;
                foreach($request->image as $image)
                {
                    $currentTimeStamp = Carbon::now()->format('Y-m-d H:i:s.u');
                    array_push($ids_to_delete, DB::table('media_files')->where('media_id', $media->id)->where('folder', 'images')->where('name', 'image['.$counter.'].'.$image->getClientOriginalExtension())->pluck('id'));
                    array_push($file_data, new MediaFile(['media_id' => $media->id, 'folder' => 'images', 'name' => 'image['.$counter.'].'.$image->getClientOriginalExtension(), 'created_at' => $currentTimeStamp, 'updated_at' => $currentTimeStamp]));
                    $image->storeAs('media/'.$media->id.'/images', 'image['.$counter.'].'.$image->getClientOriginalExtension());
                    $counter++;
                }
                DB::table('media_files')->whereIn('id', $ids_to_delete)->delete();
                $media->files()->saveMany($file_data);
            }
                
            $media->save();
            $media = Media::with('files')->find($id);
            $media->files->transform(function ($item) {
                unset($item->media_id);
                return $item;
            }); 
            //return successful response
            return response()->json(['message' => 'Media information has been successfuly updated', 'media' => $media], 200);

        } catch (\Exception $e) {
            //return error message
           return response()->json(['message' => 'Media edit failed!'], 409);
       }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if(Media::find($id) === null)
            return response()->json(['message' => 'Media with specified id does not exist'], 404);
        $role = Media::find($id);
        $entries = DB::table('media_files')->where('media_id', $id)->get()->toArray();
        DB::table('media_files')->where('media_id', $id)->delete();
        $role->delete();
        foreach($entries as $entry)
        {
            Storage::delete('/media'.'/'.$entry->media_id.'/'.$entry->folder.'/'.$entry->name);
        }
        if(count(glob('/media'.'/'.$entry->media_id.'/'.$entry->folder, GLOB_NOSORT)) === 0)
            Storage::deleteDirectory('/media'.'/'.$entry->media_id.'/'.$entry->folder);
        if(count(glob('/media'.'/'.$entry->media_id, GLOB_NOSORT)) === 0)
            Storage::deleteDirectory('/media'.'/'.$entry->media_id);
        return response()->json(['message' => 'Media has been successfuly deleted'], 200);
    }
}