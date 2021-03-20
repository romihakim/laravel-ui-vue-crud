<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $categories = Category::latest()->paginate(5);

        return response()->json([
            'success' => true,
            'message' => '',
            'data' => $categories
        ], 200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $token = Auth::user()->api_token;

        return view('category', compact('token'));
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
            'title' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Please fill required fields',
                'data' => $validator->errors()
            ], 400);
        } else {
            $req = $request->all();
            $req['slug'] = Category::unique_slug(Str::of($req['slug'] ?? $req['title'])->slug('-'));
            $req['description'] = $req['description'] ?? '';

            if (!isset($req['published'])) {
                $req['published'] = 0;
            }

            $category = Category::create($req);

            return response()->json([
                'success' => true,
                'message' => 'Category created',
                'data' => $category
            ], 200);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $category = Category::whereId($id)->first();

        if ($category) {
            unset($category->created_at);
            unset($category->updated_at);

            return response()->json([
                'success' => true,
                'message' => '',
                'data' => $category
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => '',
                'data' => null
            ], 400);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
        $validator = Validator::make($request->all(), [
            'title' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Please fill required fields',
                'data' => $validator->errors()
            ], 400);
        } else {
            $req = $request->all();
            $req['slug'] = Category::unique_slug(Str::of($req['slug'] ?? $req['title'])->slug('-'), $id);
            $req['description'] = $req['description'] ?? '';

            if (!isset($req['published'])) {
                $req['published'] = 0;
            }

            $category = Category::whereId($id)->update($req);

            return response()->json([
                'success' => true,
                'message' => 'Category updated',
                'data' => $category
            ], 200);
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
        $category = Category::findOrFail($id);
        $category->delete();

        if ($category) {
            return response()->json([
                'success' => true,
                'message' => 'Category deleted'
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Delete failed'
            ], 400);
        }
    }
}
