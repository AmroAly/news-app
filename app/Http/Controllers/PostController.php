<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Post;
use App\User;
use DB;
use File;
use Auth;

class PostController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api')
            ->except('index', 'show', 'generatePdf');
    }

    public function index(Request $request)
    {
        $posts = DB::table('posts')
                ->join('users', 'users.id', 'posts.user_id')
                ->select('posts.title', 'posts.body', 'posts.image', 'posts.id', 'posts.created_at', 'users.name as user' )
                ->orderBy('posts.created_at', 'desc');

        if (!Auth::guard('api')->user()) {

            $posts->limit(10);
        }

        return $posts->get();
    }

    public function show($id)
    {
        $newsPost = DB::table('posts')
            ->join('users', 'users.id', 'posts.user_id')
            ->select('posts.*', 'users.name as user')
            ->where('posts.id', $id)
            ->first();

        if (!$newsPost) {
            abort(404);
        }

        return response()
            ->json([
                'newsPost' => $newsPost
                ]);
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'title' => 'required|max:255',
            'body' => 'required|max:1000',
            'image' => 'required|image'
        ]);

        $filename = $this->getFileName($request->image);
        $request->image->move(base_path('public/images'), $filename);

        $newsPost = new Post($request->all());
        $newsPost->image = $filename;
        $request->user()->posts()->save($newsPost);

        return response()
            ->json([
                'published' => true,
                'id' => $newsPost->id,
                'message' => 'You have successfully Published news!'
            ]);
    }

    public function postsByUser($user_id, Request $request)
    {
        if ($request->user()->id != $user_id) {
            return response()
                ->json([
                    'unauthorized' => true
                ], 401);
        }

        $newsPosts = $request->user()->posts()
                ->orderBy('created_at', 'desc')
                ->get();
        $newsPosts = DB::table('users')
                    ->join('posts', 'users.id', 'posts.user_id')
                    ->select('users.name as user', 'posts.*' )
                    ->where('users.id', $request->user()->id)
                    ->orderBy('posts.created_at', 'desc')
                    ->get();

        return $newsPosts;
    }

    public function destroy($id, Request $request)
    {
        $newsPost = $request->user()
                    ->posts()->findOrFail($id);

        File::delete(base_path('public/images/' . $newsPost->image));
        $newsPost->delete();

        return response()
            ->json([
                'deleted' => true,
                'user_id' => $request->user()->id
            ]);
    }

    public function generatePdf($id)
    {
        $post = Post::with('user')->findOrFail($id);

         $pdf = \PDF::setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true]);
        $pdf = $pdf->loadView('newsToPdf', ['post' => $post]);

        return $pdf->download('news.pdf');
    }

    protected function getFileName($file)
    {
        return str_random(32) . '.' . $file->extension();
    }
}
