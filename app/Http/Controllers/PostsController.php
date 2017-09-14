<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Post;
use Illuminate\Support\Facades\Storage;


class PostsController extends Controller
{


    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //prevent non signed in users from editing or deleting they can only access index and show views in posts
        $this->middleware('auth',['except'=>['index','show']]);
    }



    public function index()
    {
        //
        $posts = Post::orderBy('created_at','desc')->paginate(5);
        return view('posts.index')->with('posts',$posts);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        return view('posts.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //

        $this->validate($request,[
            'title'=>'required',
            'body'=>'required',
            'cover_image'=>'image|nullable|max:1999',
        ]);

        //handle file upload
        if($request->hasFile('cover_image')){
            //get file name with extension
            $fileNameWithExt = $request->file('cover_image')->getClientOriginalName();
            //get just file name
            $filename=pathinfo($fileNameWithExt,PATHINFO_FILENAME);

            //get just extension
            $extension =$request->file('cover_image')->getClientOriginalExtension();

            $fileNametoStore=$filename.'_'.time().'.'.$extension;
            $path = $request->file('cover_image')->storeAs('public/cover_images',$fileNametoStore);
        }else{
            $fileNametoStore = 'noimage.jpg';
        }

        //create new blog

        $post = new Post();
        $post->title =$request->input('title');
        $post->body =$request->input('body');
        $post->user_id=auth()->user()->id;
        $post->cover_image = $fileNametoStore;
        $post->save();
        return redirect('/posts')->with('success','Post created successfully');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
        $post = Post::find($id);
        return view('posts.show')->with('post',$post);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //block users who dont own the post from updating it even on the url b passing /edit

        $post = Post::find($id);
        if(auth()->user()->id !== $post->user_id){
            return redirect('/posts')->with('error','Unauthorized activity');
        }
        return view('posts.edit')->with('post',$post);
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
        //
        $this->validate($request,[
            'title'=>'required',
            'body'=>'required'
        ]);
        //handle file upload
        if($request->hasFile('cover_image')){
            //get file name with extension
            $fileNameWithExt = $request->file('cover_image')->getClientOriginalName();
            //get just file name
            $filename=pathinfo($fileNameWithExt,PATHINFO_FILENAME);

            //get just extension
            $extension =$request->file('cover_image')->getClientOriginalExtension();

            $fileNametoStore=$filename.'_'.time().'.'.$extension;
            $path = $request->file('cover_image')->storeAs('public/cover_images',$fileNametoStore);
        }
        //create new blog

        $post = Post::find($id);
        $post->title =$request->input('title');
        $post->body =$request->input('body');
        if($request->hasFile('cover_image')){
            $post->cover_image =$fileNametoStore;
        }
        $post->save();
        return redirect('/posts')->with('success','Post updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
        $post= Post::find($id);
        //check for correct user
        if(auth()->user()->id !== $post->user_id){
            return redirect('/posts')->with('error','Unauthorized activity');
        }
        //check if image exists before deleting it
        if($post->cover_image !='noimage.jpg'){
            //delete image
           Storage::delete('public/cover_images/'.$post->cover_image);
            //unlink(storage_path('app/folder/'.$file));

        }
        $post->delete();
        return redirect('/posts')->with('success','Post deleted successfully');
    }
}
