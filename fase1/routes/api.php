<?php

use App\Http\Requests\PostRequest;
use App\Http\Resources\PostResource;
use Illuminate\Support\Facades\Route;

$posts = [
    ['id' => 1, 'title' => 'First Post', 'content' => 'This is the first post content.'],
    ['id' => 2, 'title' => 'Second Post', 'content' => 'This is the second post content.'],
    ['id' => 3, 'title' => 'Third Post', 'content' => 'This is the third post content.'],
];

Route::get('/post', function () use ($posts) {
    return PostResource::collection(collect($posts));
});

Route::get('/post/{id}', function ($id) use ($posts) {
    $post = collect($posts)->firstWhere('id', (int) $id);
    if ($post) {
        return new PostResource(collect($post));
    } else {
        return response()->json(['message' => 'Post not found'], 404);
    }
});

Route::post('/post', function (PostRequest $request) use (&$posts) {
    $newPost = $request->validated();
    $newPost['id'] = count($posts) + 1;
    $posts[] = $newPost;
    return new PostResource(collect($newPost))->response()->setStatusCode(201);
});

Route::put('/post/{id}', function (PostRequest $request, $id) use (&$posts) {
    $index = collect($posts)->search(fn($post) => $post['id'] === (int) $id);
    if ($index !== false) {
        $updatedPost = $request->validated();
        $updatedPost['id'] = (int) $id;
        $posts[$index] = $updatedPost;
        return new PostResource(collect($updatedPost))->response()->setStatusCode(200);
    } else {
        return response()->json(['message' => 'Post not found'], 404);
    }
});

Route::delete('/post/{id}', function ($id) use (&$posts) {
    $index = collect($posts)->search(fn($post) => $post['id'] === (int) $id);
    if ($index !== false) {
        array_splice($posts, $index, 1);
        return new PostResource(collect(['id' => (int) $id]))->response()->setStatusCode(200);
    } else {
        return response()->json(['message' => 'Post not found'], 404);
    }
});
