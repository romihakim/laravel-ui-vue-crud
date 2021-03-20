<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $posts = Post::latest()->paginate(5);

        return response()->json([
            'success' => true,
            'message' => '',
            'data' => $posts
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

        return view('post', compact('token'));
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
            'title' => 'required',
            'image' => 'mimes:png,jpg,jpeg'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Please fill required fields',
                'data' => $validator->errors()
            ], 400);
        } else {
            if ($request->hasFile('image') && $request->file('image')->isValid()) {
                $image = $request->file('image');
                $image->storeAs('public/posts', $image->hashName());
            }

            $req = $request->all();
            $req['slug'] = Post::unique_slug(Str::of($req['slug'] ?? $req['title'])->slug('-'));
            $req['excerpt'] = $req['excerpt'] ?? '';
            $req['content'] = $req['content'] ?? '';

            if (isset($image)) {
                $req['image'] = $image->hashName();
            }

            if (!isset($req['published'])) {
                $req['published'] = 0;
            }

            unset($req['_method']);

            $post = Post::create($req);

            return response()->json([
                'success' => true,
                'message' => 'Post created',
                'data' => $post
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
        $post = Post::whereId($id)->first();

        if ($post) {
            unset($post->created_at);
            unset($post->updated_at);

            return response()->json([
                'success' => true,
                'message' => '',
                'data' => $post
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
            'title' => 'required',
            'image' => 'mimes:png,jpg,jpeg'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Please fill required fields',
                'data' => $validator->errors()
            ], 400);
        } else {
            if ($request->hasFile('image') && $request->file('image')->isValid()) {
                $image = $request->file('image');
                $image->storeAs('public/posts', $image->hashName());
            }

            $req = $request->all();
            $req['slug'] = Post::unique_slug(Str::of($req['slug'] ?? $req['title'])->slug('-'), $id);
            $req['excerpt'] = $req['excerpt'] ?? '';
            $req['content'] = $req['content'] ?? '';

            if (isset($image)) {
                $req['image'] = $image->hashName();
            }

            if (!isset($req['published'])) {
                $req['published'] = 0;
            }

            unset($req['_method']);

            $post = Post::whereId($id)->update($req);

            return response()->json([
                'success' => true,
                'message' => 'Post updated',
                'data' => $post
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
        $post = Post::findOrFail($id);
        $post->delete();

        if ($post) {
            return response()->json([
                'success' => true,
                'message' => 'Post deleted'
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Delete failed'
            ], 400);
        }
    }
}
