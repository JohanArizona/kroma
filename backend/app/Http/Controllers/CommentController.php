<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Comment;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{

public function store(
Request $request,
$chapter_id
){

try{

$request->validate([
'content'=>'required|string'
]);

$user=Auth::user();

if(!$user){

return response()->json([
'success'=>false,
'message'=>'Login diperlukan'
],401);

}

$comment=
Comment::create([

'chapter_id'=>$chapter_id,

'user_id'=>$user->id,

'content'=>$request->content

]);

return response()->json([

'success'=>true,

'message'=>'Komentar berhasil ditambahkan',

'data'=>$comment

],201);

}
catch(\Exception $e){

return response()->json([

'success'=>false,

'error'=>$e->getMessage()

],500);

}

}

public function index(
$chapter_id
){

$comments=
Comment::with('user')
->where(
'chapter_id',
$chapter_id
)
->latest()
->get();

return response()->json([

'success'=>true,

'data'=>$comments

]);

}

public function update(
Request $request,
$id
){

$user=
Auth::user();

$comment=
Comment::where(
'id',
$id
)
->where(
'user_id',
$user->id
)
->firstOrFail();

$comment->update([

'content'=>$request->content

]);

return response()->json([

'success'=>true,

'data'=>$comment

]);

}

public function destroy(
$id
){

$user=
Auth::user();

$comment=
Comment::where(
'id',
$id
)
->where(
'user_id',
$user->id
)
->firstOrFail();

$comment->delete();

return response()->json([

'success'=>true

]);

}

}