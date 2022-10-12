@extends('layouts.main')
@section('container')
<h1>Post</h1>
{{-- @dd($post) --}}

<article class="mb-5">

    <h2>{{ $post->title }}</h2>
    <h5>By <a href="#" class="text-decoration-none">{{ $post->user->name }}</a> in <a href="/categories/{{ $post->category->slug }}" class="text-decoration-none">{{ $post->category->name }}</h5></a>
    <p>{{ $post->excerpt }}</p>
    {!!$post->body!!}
</article>

<a href="/blog">Back</a>


@endsection

{{-- Post::create([
'title' => 'Judul Post Kedua',
'slug' => 'judul-post-kedua',
'excerpt' => 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Quisquam, quod.',
'body' => '<p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Quisquam, quae.</p>',
'category_id' => 1
]); --}}

{{-- Category::create([
'name' => 'Personal',
'slug' => 'personal'
]); --}}
