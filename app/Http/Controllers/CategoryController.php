<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\User;
use App\Role;
use App\Category;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $categories = Category::all();
        return response()->json(['categories' => $categories], 200);
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
            'media_type_id' => ['required', 'exists:media,id', 'integer'],
            'name' => ['required', 'regex:/(^([a-zA-Z]+)(\d+)?$)/u']
        ]);

        if(DB::table('categories')->where('media_type_id', $request->media_type_id)->where('name')->count() > 0)
        {
            return response()->json(['message' => 'Category with this name and media type has been taken'], 422);
        }
        
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $category = new Category;
        $category->media_type_id = $request->media_type_id;
        $category->name = $request->name;
        $category->save();
        return response()->json(['message' => 'Category has been successfuly created', 'category' => $category], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if(Category::find($id) === null)
        return response()->json(['message' => 'Category with specified id does not exist'], 404);
        $category = Category::find($id);
        return response()->json(['category' => $category], 200);
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
        if(Category::find($id) === null)
            return response()->json(['message' => 'Category with specified id does not exist'], 404);
        $category = Category::find($id);
        $validator = Validator::make($request->all(), [
            'media_type_id' => ['exists:media,id', 'integer'],
            'name' => ['regex:/(^([a-zA-Z]+)(\d+)?$)/u']
        ]);
        
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $category->media_type_id = $request->media_type_id;
        $category->name = $request->name;
        $category->save();
        return response()->json(['message' => 'Category has been successfuly updated', 'category' => $category], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if(Category::find($id) === null)
            return response()->json(['message' => 'Category with specified id does not exist'], 404);
        if(DB::table('user_role')->where('role_id', $id)->count() > 0)
            return response()->json(['message' => 'Cannot delete role with existing users owning it'], 422);
        $role = Role::find($id);
        $role->delete();
        return response()->json(['message' => 'Role has been successfuly deleted'], 200);
    }
}