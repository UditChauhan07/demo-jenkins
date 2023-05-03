<?php

namespace App\Http\Controllers;

use App\Models\Video;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class VideoController extends Controller
{
    function __construct()
    {
         $this->middleware('permission:video-list|video-create|video-edit|video-delete', ['only' => ['index','store']]);
         $this->middleware('permission:video-create', ['only' => ['create','store']]);
         $this->middleware('permission:video-edit', ['only' => ['edit','update']]);
         $this->middleware('permission:video-delete', ['only' => ['destroy']]);
    }

    public function index()
    {
        $videos = Video::all();
        return view('videos.index', compact('videos'));
    }

    public function create()
    {
        return view('videos.create');
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'video_title' => 'required',
            'video_link' => 'required',
        ]);
        $video_title = $request->video_title;
        $video_link = $request->video_link;
        $explode_videoLink = explode("/", $video_link);
        $videoLink_arraycount = count($explode_videoLink);
        $arrayKey = $videoLink_arraycount - 1;
        $video_code = $explode_videoLink[$arrayKey];
        $video_thumbnail = $request->video_thumbnail;
        if ($video_thumbnail) {
            $destinationPath = public_path() . '/thumbnail';
            $safeName = \Str::random(12) . time() . '.' . $video_thumbnail->getClientOriginalExtension();
            $video_thumbnail->move($destinationPath, $safeName);
            $new_videoThumbnail_name = $safeName;
        }
        

        $video = Video::create([
            'video_title'=> $video_title,
            'video_link'=>$video_link,
            'video_code'=>$video_code,
            'video_thumbnail'=>$new_videoThumbnail_name
        ]);

        return redirect()->route('videos.index')
                        ->with('success','Video Added successfully');
    }

    public function show($id)
    {
        $video = Video::find($id);
        return view('videos.show',compact('video'));
    }

    public function edit($id)
    {
        $video = Video::find($id);
        return view('videos.edit',compact('video'));
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'video_title' => 'required',
            'video_link' => 'required',
        ]);
        $video_title = $request->video_title;
        $video_link = $request->video_link;
        $explode_videoLink = explode("/", $video_link);
        $videoLink_arraycount = count($explode_videoLink);
        $arrayKey = $videoLink_arraycount - 1;
        $video_code = $explode_videoLink[$arrayKey];
        $video_thumbnail = $request->video_thumbnail;
        if ($video_thumbnail) {
            $destinationPath = public_path() . '/thumbnail';
            $safeName = \Str::random(12) . time() . '.' . $video_thumbnail->getClientOriginalExtension();
            $video_thumbnail->move($destinationPath, $safeName);
            $new_videoThumbnail_name = $safeName;
        }

        $video = Video::find($id);
        $video->video_title = $video_title;
        $video->video_link = $video_link;
        $video->video_code = $video_code;
        $video->video_thumbnail = $new_videoThumbnail_name;
        $video->save();
        return redirect()->route('videos.index')
                        ->with('success','Video updated successfully');
    }
}
