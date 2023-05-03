<?php

namespace App\Http\Controllers;

use App\Models\Lifecoach_description;
use Illuminate\Http\Request;

class LifecoachController extends Controller
{
    function __construct()
    {
         $this->middleware('permission:lifecoach-list|lifecoach-create|lifecoach-edit|lifecoach-delete', ['only' => ['index','store']]);
         $this->middleware('permission:lifecoach-create', ['only' => ['create','store']]);
         $this->middleware('permission:lifecoach-edit', ['only' => ['edit','update']]);
         $this->middleware('permission:lifecoach-delete', ['only' => ['destroy']]);
    }

    public function index()
    {
        $lifecoach_description_day = Lifecoach_description::where('type',1)->where('is_active',1)->paginate(7, ['*'], 'day');
        $lifecoach_description_week = Lifecoach_description::where('type',2)->where('is_active',1)->paginate(7, ['*'], 'week');
        return view('lifecoach_descriptions.index', compact('lifecoach_description_day', 'lifecoach_description_week'));
    }

    public function create()
    {
        return view('lifecoach_descriptions.create');
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'type' => 'required',
        ]);
        $type = $request->type;
        if($type == 2){
            $this->validate($request, [
                'type' => 'required',
                'star_type' => 'required',
                'number' => 'required',
                'description' => 'required',
            ]);
            $star_number = 4;
        }else{
            $this->validate($request, [
                'type' => 'required',
                'star_type' => 'required',
                'star_number' => 'required',
                'number' => 'required',
                'description' => 'required',
            ]);
            $star_number = $request->star_number;
        }

        $star_type = $request->star_type;
        $number = $request->number;
        $description = $request->description;

        $lifecoach = Lifecoach_description::create([
            'type' => $type,
            'star_type'=> $star_type,
            'star_number'=>$star_number,
            'number'=>$number,
            'description'=>$description
        ]);
        if($lifecoach)
        {
            return redirect()->route('lifecoach_descriptions.index')
                            ->with('success','Video Added successfully');
        }else{
            return redirect()->route('lifecoach_descriptions.index')
            ->with('success','Some thing went wrong. Please try again.');
        }

    }

    public function show($id)
    {
        $lifecoach = Lifecoach_description::find($id);
        return view('lifecoach_descriptions.show',compact('lifecoach'));
    }

    public function edit($id)
    {
        $lifecoach = Lifecoach_description::find($id);
        return view('lifecoach_descriptions.edit',compact('lifecoach'));
    }

    public function update(Request $request, $id)
    {
            $this->validate($request, [
                'description' => 'required',
            ]);
        $description = $request->description;

        $lifecoach = Lifecoach_description::find($id);
        $lifecoach->description = $description;
        $lifecoach->save();

        if($lifecoach)
        {
            return redirect()->route('lifecoach_descriptions.index')
                            ->with('success','Lifecoach updated successfully');
        }else{
            return redirect()->route('lifecoach_descriptions.index')
            ->with('success','Some thing went wrong. Please try again.');
        }
    }

}
