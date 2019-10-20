<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\User;
use App\Role;
use App\Rating;

class RatingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $ratings = Rating::all();
        return response()->json(['ratings' => $ratings], 200);
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
            'media_id' => ['required', 'exists:media,id', 'integer'],
            'user_id' => ['required', 'exists:users,id', 'integer'],
            'rating' => ['required', 'integer', 'between:1,5']
        ]);
        
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $rating = new Rating;
        $rating->media_id = $request->media_id;
        $rating->user_id = $request->user_id;
        $rating->rating = $request->rating;
        $rating->save();
        return response()->json(['message' => 'Rating has been successfuly created', 'rating' => $rating], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if(Rating::find($id) === null)
        return response()->json(['message' => 'Rating with specified id does not exist'], 404);
        $rating = Rating::find($id);
        return response()->json(['rating' => $rating], 200);
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
        if(Comment::find($id) === null)
            return response()->json(['message' => 'Comment with specified id does not exist'], 404);
        $comment = Comment::find($id);
        $validator = Validator::make($request->all(), [
            'media_id' => ['exists:media,id', 'numeric'],
            'user_id' => ['exists:users,id', 'numeric'],
            'text' => []
        ]);
        
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        if($request->media_id != null)
            $comment->media_id = $request->media_id;
        if($request->user_id != null)
            $comment->user_id = $request->user_id;
        if($request->text != null)
            $comment->text = $request->text;
        $comment->save();
        return response()->json(['message' => 'Comment has been successfuly updated', 'comment' => $comment], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if(Comment::find($id) === null)
            return response()->json(['message' => 'Comment with specified id does not exist'], 404);
        $comment = Comment::find($id);
        $comment->delete();
        return response()->json(['message' => 'Comment has been successfuly deleted'], 200);
    }
}