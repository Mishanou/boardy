<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;

class CommentController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'post_id' => 'required|exists:posts,id',
            'body'    => 'required|string|max:1000',
        ]);

        // Создаем комментарий в БД
        $comment = $request->user()->comments()->create($data);

        // Публикуем в Redis для FastAPI
        Redis::publish('new_comment', json_encode([
            'post_id' => (int)$comment->post_id,
            'body'    => $comment->body,
            'author'  => $request->user()->name,
        ]));

        return back()->with('success', 'Комментарий добавлен');
    }
}