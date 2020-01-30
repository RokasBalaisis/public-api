<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\User;
use App\Role;
use App\Category;
use App\MediaType;

class MediaTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $media_types = MediaType::with('categories')->get();
        if($media_types->count() > 0)
        {
            $media_types->transform(function ($entry) {
                if($entry->categories->count() > 0)
                {
                    $entry->categories->transform(function ($item) {
                        unset($item->media_type_id);      
                        return $item;
                    });
                }  
                return $entry;
            });
        }
        return response()->json(['media_types' => $media_types], 200);
    }

    /**
     * Display a listing of the resource with its media.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function indexMedia()
    {
        $media_types = MediaType::with('media.ratings', 'media.cover')->get();
        $media_types->transform(function ($entry) {
            $entry->media->transform(function ($item)
            {
                $item->ratingAverage = number_format((float)$item->ratings->avg('rating'), 2, '.', '') ?: 0;
                $item->ratingCount = $item->ratings->count();
                unset($item->ratings);
                unset($item->id);
                unset($item->category_id);
                unset($item->description);
                unset($item->trailer_url);
                return $item;
            });
            return $entry;
        });
        return response()->json(['media_types' => $media_types], 200);
    }

    /**
     * Display a listing of the latest 12 resources with its media.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function indexMediaLatest()
    {
        $media_types = array();
        $allMediaTypes = MediaType::all();
        foreach($allMediaTypes as $mediaType)
        {
            $mediaArray = MediaType::with('media.ratings', 'media.cover')->where('name', $mediaType->name)->get();
            $mediaArray[0]->media->sortByDesc('created_at');
            dd($mediaArray[0]->media);
            $mediaArray->values()->take(12);

            foreach($mediaArray as $entry)
            {
                array_push($media_types, $entry);
            }
            $mediaArray = null;
        }
        $media_types = collect($media_types);
        $media_types->transform(function ($entry) {
            $entry->media->transform(function ($item)
            {
                $item->ratingAverage = number_format((round((float)$item->ratings->avg('rating') * 2) / 2), 2, '.', '') ?: 0;
                $item->ratingCount = $item->ratings->count();
                unset($item->ratings);
                unset($item->id);
                unset($item->category_id);
                unset($item->description);
                unset($item->trailer_url);
                return $item;
            });
            return $entry;
        });
        return response()->json(['media_types' => $media_types], 200);
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
            'name' => ['required', 'min:3', 'regex:/^[A-Za-z]+$/', 'unique:media_types,name']
        ]);
        
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $media_type = new MediaType();
        $media_type->name = $request->name;
        $media_type->save();
        return response()->json(['message' => 'Media type has been successfuly created', 'media_type' => $media_type], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        if(MediaType::find($id) === null)
        return response()->json(['message' => 'Media type with specified id does not exist'], 404);
        $media_type = MediaType::with('categories')->find($id);
        return response()->json(['media_type' => $media_type], 200);
    }

    /**
     * Display the media of specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function showMedia($id)
    {
        if(MediaType::find($id) === null)
        return response()->json(['message' => 'Media type with specified id does not exist'], 404);
        $media_type = MediaType::with('media.files', 'media.actors', 'media.ratings')->find($id);
        return response()->json(['media_type' => $media_type], 200);
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
        if(MediaType::find($id) === null)
            return response()->json(['message' => 'Media type with specified id does not exist'], 404);
        $media_type = MediaType::find($id);
        $validator = Validator::make($request->all(), [
            'name' => ['min:3', 'regex:/^[A-Za-z]+$/', 'unique:media_types,name']
        ]);
        
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $media_type->name = $request->name;
        $media_type->save();
        return response()->json(['message' => 'Media type has been successfuly updated', 'media_type' => $media_type], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        if(MediaType::find($id) === null)
            return response()->json(['message' => 'Media type with specified id does not exist'], 404);
        if(DB::table('categories')->where('media_type_id', $id)->count() > 0)
            return response()->json(['message' => 'Cannot delete media type with existing categories owning it'], 422);
        $media_type = MediaType::find($id);
        $media_type->delete();
        return response()->json(['message' => 'Media type has been successfuly deleted'], 200);
    }
}