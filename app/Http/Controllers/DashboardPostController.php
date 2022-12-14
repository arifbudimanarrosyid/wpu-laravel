<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Category;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DashboardPostController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // auth user is admin show all posts
        if (Auth::user()->is_admin == true) {
            // show all posts with paginate

            $posts = Post::latest()-> paginate(10);
        } else {
            $posts = Post::where('user_id', auth()->user()->id)->latest()->paginate(10);
        }

        return view('dashboard.posts.index', ['posts' => $posts]);

        // return view('dashboard.posts.index', [
        //     'posts' => Post::where('user_id', auth()->user()->id)->latest()->get()
        // ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('dashboard.posts.create', ['categories' => Category::all()]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // ddd($request);
        // return $request -> file('image')->store('post-images');
        $validatedData = $request->validate([
            'title' => 'required|max:255',
            'slug' => 'required|unique:posts',
            'category_id' => 'required',
            'body' => 'required',
            'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $validatedData['image'] = $request->file('image')->store('post-images');
        }

        $validatedData['user_id'] = auth()->user()->id;
        $validatedData['excerpt'] = Str::limit(strip_tags($request->body), 50);

        Post::create($validatedData);

        return redirect('/dashboard/posts')->with('success', 'Post Created Successfully');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function show(Post $post)
    {
        return view('dashboard.posts.show', [
            'post' => $post
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function edit(Post $post)
    {

        // auth agar tidak bisa edit post yang post user id nya tidak sama dengan user id yang login
        if ($post->user_id != auth()->user()->id) {
            // abort(403);
            return back()->with('error', 'You are not authorized to edit this post');
        }
        // if (auth()->user()->is_admin === true) {
        //     return view('dashboard.posts.edit', [
        //         'post' => $post,
        //         'categories' => Category::all()
        //     ]);
        // }





        return view('dashboard.posts.edit', [
            'post' => $post,
            'categories' => Category::all()
        ]);

        // bisa juga ditulis dengan cara ini
        // if ($post->user_id != auth()->user()->id ) {
        //     abort(403);
        // }{
        //     return view('dashboard.posts.edit', [
        //         'post' => $post,
        //         'categories' => Category::all()
        //     ]);
        // }




    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Post $post)
    {
        $rules = [
            'title' => 'required|max:255',
            // 'slug' => 'required|unique:posts',
            'category_id' => 'required',
            'body' => 'required',
        ];



        // if ($request->user_id != $post->user_id) {
        //     abort(403);
        // }

        if ($request->slug != $post->slug) {
            // $validatedData['slug'] = $request->slug;
            $rules['slug'] = 'required|unique:posts';
        }

        $validatedData = $request->validate($rules);
        $validatedData['user_id'] = auth()->user()->id;
        $validatedData['excerpt'] = Str::limit(strip_tags($request->body), 50);

        Post::where('id', $post->id)->update($validatedData);

        return redirect('/dashboard/posts')->with('success', 'Post has beed Updated Successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function destroy(Post $post)
    {
        if ($post->user_id != auth()->user()->id) {
            // abort(403);
            return back()->with('error', 'You are not authorized to delete this post');
        }

        if ($post->image) {
            Storage::delete($post->image);
        }
        Post::destroy($post->id);

        return redirect('/dashboard/posts')->with('success', 'Post has been deleted');
    }
}
