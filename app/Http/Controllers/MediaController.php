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
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $media = Media::with('files', 'actors', 'ratings', 'cover')->get();
        if($media->count() > 0)
        {
            $media->transform(function ($entry) {
                if($entry->files->count() > 0)
                {
                    $entry->files->transform(function ($item) {
                        unset($item->media_id);
                
                        return $item;
                    });
                }
                if($entry->ratings->count() > 0)
                {
                    $entry->ratings->transform(function ($item) {
                        unset($item->media_id);
                
                        return $item;
                    }); 
                }    
                return $entry;
            });
        }
        return response()->json(['media' => $media], 200);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'category_id' => ['exists:categories,id', 'required', 'integer'],
            'name' => ['required', 'min:3', 'regex:/^[a-z\d\-_\s]+$/i'],
            'short_description' => ['required'],
            'description' => ['required'],
            'trailer_url' => ['required', 'regex:/www.youtube(?:-nocookie)?.com\/(?:v|embed)\/([a-zA-Z0-9-_]+).*/'],
            'cover' => ['file','mimes:jpg,jpeg,png,bmp', 'required'],
            'image' => ['array', 'min:3', 'max:3'],
            'image.*' => ['file','mimes:jpg,jpeg,png,bmp'],
            'imdb_rating' => ['numeric', 'between:0,10'],
            'actor_id' => ['array'],
            'actor_id.*' => ['exists:actors,id', 'integer'],
        ]);
        
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $media = new Media;
        $media->category_id = $request->category_id;
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
        if($request->image != null)
        {
            foreach($request->image as $image)
            {
                $currentTimeStamp = Carbon::now()->format('Y-m-d H:i:s.u');
                array_push($file_data, ['media_id' => $media->id, 'folder' => 'images', 'name' => $image->getClientOriginalName(), 'created_at' => $currentTimeStamp, 'updated_at' => $currentTimeStamp]);
                $image->storeAs('public/images/', Carbon::parse($currentTimeStamp)->format('Y-m-d_H-i-s-u') . '_' . $image->getClientOriginalName());
                $counter++;
            }
        }
        

        if($request->cover != null)
        {
            $currentTimeStamp = Carbon::now()->format('Y-m-d H:i:s.u');
            array_push($file_data, ['media_id' => $media->id, 'folder' => 'covers', 'name' => $request->cover->getClientOriginalName(), 'created_at' => $currentTimeStamp, 'updated_at' => $currentTimeStamp]);
            $request->cover->storeAs('public/covers/', Carbon::parse($currentTimeStamp)->format('Y-m-d_H-i-s-u') . '_' . $request->cover->getClientOriginalName());
        }
        DB::table('media_files')->insert(array_reverse($file_data));
        
        $media = Media::with('files', 'actors')->find($media->id);
        $media->files->transform(function ($item) {
            unset($item->media_id);
            return $item;
        });    
        return response()->json(['message' => 'Media has been successfully created', 'media' => $media], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        if(Media::find($id) === null)
            return response()->json(['message' => 'Media with specified id does not exist'], 404);
        $media = Media::with('files', 'actors')->find($id);
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
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        if(Media::find($id) === null)
            return response()->json(['message' => 'Media with specified id does not exist'], 404);

        $validator = Validator::make($request->all(), [
            'category_id' => ['exists:categories,id', 'integer'],
            'name' => ['min:3', 'regex:/^[A-Za-z]+$/'],
            'short_description' => [],
            'description' => [],
            'trailer_url' => ['regex:/www.youtube(?:-nocookie)?.com\/(?:v|embed)\/([a-zA-Z0-9-_]+).*/'],
            'image' => ['array', 'max:3'],
            'image.*' => ['file','mimes:jpg,jpeg,png,bmp'],
            'imdb_rating' => ['numeric', 'between:0,10'],
            'actor_id' => ['array'],
            'actor_id.*' => ['exists:actors,id', 'integer'],
            'remove_actor_id' => ['array'],
            'remove_actor_id.*' => ['exists:actors,id', 'integer'],
        ]);
        
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

            $media = Media::with('files', 'actors')->find($id);
            if($request->category_id != null)
                $media->category_id = $request->category_id;
            if($request->name != null)
                $media->name = $request->name;
            if($request->short_description != null)    
                $media->short_description = $request->short_description;
            if($request->description != null)    
                $media->description = $request->description;
            if($request->trailer_url != null)
                $media->trailer_url = $request->trailer_url;
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
                    DB::table('media_files')->where('media_id', $media->id)->where('folder', 'images')->where('name', 'image['.$counter.'].'.$image->getClientOriginalExtension())->delete();
                    array_push($file_data, new MediaFile(['media_id' => $media->id, 'folder' => 'images', 'name' => 'image['.$counter.'].'.$image->getClientOriginalExtension(), 'created_at' => $currentTimeStamp, 'updated_at' => $currentTimeStamp]));
                    $image->storeAs('media/'.$media->id.'/images', 'image['.$counter.'].'.$image->getClientOriginalExtension());
                    $counter++;
                }
                $media->files()->saveMany($file_data);
            }

            $actor_data = array();

            if($request->actor_id != null)
            {
                foreach($request->actor_id as $actor_id)
                {
                    if(DB::table('media_actors')->where('media_id', $media->id)->where('actor_id', $actor_id)->count() == 0)
                        array_push($actor_data, ['media_id' => $media->id, 'actor_id' => $actor_id]);
                }
            }
    
            DB::table('media_actors')->insert($actor_data);

            $remove_actor_ids = array();

            if($request->remove_actor_id != null)
            {
                foreach($request->remove_actor_id as $remove_actor_id)
                {
                    if(DB::table('media_actors')->where('media_id', $media->id)->where('actor_id', $remove_actor_id)->count() > 0)
                        array_push($remove_actor_ids, DB::table('media_actors')->where('media_id', $media->id)->where('actor_id', $remove_actor_id)->pluck('id'));
                }
            }
    
            DB::table('media_actors')->whereIn('id', $remove_actor_ids)->delete();
                
            $media->save();
            $media = Media::with('files', 'actors')->find($id);
            $media->files->transform(function ($item) {
                unset($item->media_id);
                return $item;
            }); 
            //return successful response
            return response()->json(['message' => 'Media information has been successfuly updated', 'media' => $media], 200);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        if(Media::find($id) === null)
            return response()->json(['message' => 'Media with specified id does not exist'], 404);
        $media= Media::find($id);
        $entries = DB::table('media_files')->where('media_id', $id)->get()->toArray();
        if(DB::table('media_files')->where('media_id', $id)->count() > 0)
        {
            foreach($entries as $entry)
            {
                Storage::delete('/media'.'/'.$entry->media_id.'/'.$entry->folder.'/'.$entry->name);
            }
            foreach($entries as $entry)
            {
                if(count(glob('/media'.'/'.$entry->media_id.'/'.$entry->folder, GLOB_NOSORT)) === 0)
                    Storage::deleteDirectory('/media'.'/'.$entry->media_id.'/'.$entry->folder);
                if(count(glob('/media'.'/'.$entry->media_id, GLOB_NOSORT)) === 0)
                    Storage::deleteDirectory('/media'.'/'.$entry->media_id);
            }
        }
        DB::table('media_files')->where('media_id', $id)->delete();
        DB::table('media_actors')->where('media_id', $id)->delete();
        $media->delete();
        return response()->json(['message' => 'Media has been successfuly deleted'], 200);
    }

    /**
     * Download a resource from storage.
     *
     * @param  int  $id
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function downloadFile($id)
    {
        $headers = [
            'Access-Control-Allow-Origin'      => '*',
            'Access-Control-Allow-Methods'     => 'POST, GET, OPTIONS, PUT, DELETE',
            'Access-Control-Allow-Credentials' => 'true',
            'Access-Control-Max-Age'           => '86400',
            'Access-Control-Allow-Headers'     => 'Content-Type, Authorization, X-Requested-With',
            'Access-Control-Expose-Headers'    => 'Authorization'
        ];
        if(MediaFile::find($id) === null)
            return response()->json(['message' => 'Media file with specified id does not exist'], 404);

        $mediaFile = MediaFile::find($id);

        if(!is_file(storage_path('app') . '/' . 'media' . '/' . $mediaFile->media_id . '/' . $mediaFile->folder . '/' . $mediaFile->name)){
            return response()->json(['message' => 'Media file does not exist'], 404);
        }


        return response()->download(storage_path('app') . '/' . 'media' . '/' . $mediaFile->media_id . '/' . $mediaFile->folder . '/' . $mediaFile->name, null, $headers);
    }

    /**
     * Download a resource from storage.
     *
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function noFileImage()
    {
        $headers = [
            'Access-Control-Allow-Origin'      => '*',
            'Access-Control-Allow-Methods'     => 'POST, GET, OPTIONS, PUT, DELETE',
            'Access-Control-Allow-Credentials' => 'true',
            'Access-Control-Max-Age'           => '86400',
            'Access-Control-Allow-Headers'     => 'Content-Type, Authorization, X-Requested-With',
            'Access-Control-Expose-Headers'    => 'Authorization'
        ];


        return response()->download(storage_path('app')  . '/' . 'NoImage.png', null, $headers);
    }
}