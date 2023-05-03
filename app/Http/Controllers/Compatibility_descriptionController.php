<?php

namespace App\Http\Controllers;

use App\Models\Compatibility_description;
use Illuminate\Http\Request;

class Compatibility_descriptionController extends Controller
{
    function __construct()
    {
         $this->middleware('permission:compatibility_desc-list|compatibility_desc-create|compatibility_desc-edit|compatibility_desc-delete', ['only' => ['index','store']]);
         $this->middleware('permission:compatibility_desc-create', ['only' => ['create','store']]);
         $this->middleware('permission:compatibility_desc-edit', ['only' => ['edit','update']]);
         $this->middleware('permission:compatibility_desc-delete', ['only' => ['destroy']]);
    }

    public function index()
    {
        $compatibility_description = Compatibility_description::where('is_active', 1)->paginate(10);
        return view('compatibility_description.index', compact('compatibility_description'));
    }

    public function typedescription(Request $request)
    {
        $type_val = $request->type;
        $descriptions = Compatibility_description::where('type', $type_val)->get();
        return $descriptions;
    }

    public function create()
    {
        return view('compatibility_description.create');
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'type' => 'required',
            'number'=> 'required',
            'description' => 'required',
        ]);

        $type = $request->type;
        $number = $request->number;
        $description = $request->description;

        $user = Compatibility_description::create(['type'=>$type, 'number'=>$number, 'description'=>$description]);

        return redirect()->route('compatibility_description.index')
                        ->with('success','Compatibility description created successfully');
    }

    public function show($id)
    {
        $compatibility_description = Compatibility_description::find($id);
        $compatibility_type_desc = $compatibility_description->type;
        if($compatibility_type_desc == 1)
        {
            $type = 'Car/Vehicle';
        }elseif($compatibility_type_desc == 2)
        {
            $type = 'Business';
        }elseif($compatibility_type_desc == 3)
        {
            $type = 'Property';
        }elseif($compatibility_type_desc == 4)
        {
            $type = 'Other Person';
        }elseif($compatibility_type_desc == 5)
        {
            $type = 'Spouse/Partner';
        }elseif($compatibility_type_desc == 6)
        {
            $type = 'Name Reading';
        }
        
        return view('compatibility_description.show',compact('compatibility_description', 'type'));
    }

    public function edit($id)
    {
        $compatibility_description = Compatibility_description::find($id);
        $compatibility_type_desc = $compatibility_description->type;
        if($compatibility_type_desc == 1)
        {
            $type = 'Car/Vehicle';
        }elseif($compatibility_type_desc == 2)
        {
            $type = 'Business';
        }elseif($compatibility_type_desc == 3)
        {
            $type = 'Property';
        }elseif($compatibility_type_desc == 4)
        {
            $type = 'Other Person';
        }elseif($compatibility_type_desc == 5)
        {
            $type = 'Spouse/Partner';
        }elseif($compatibility_type_desc == 6)
        {
            $type = 'Name Reading';
        }

        return view('compatibility_description.edit',compact('compatibility_description', 'type'));
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'description' => 'required',
        ]);
        $input = $request->all();
        $compatibility_description = Compatibility_description::find($id);
        $compatibility_description->update($input);

        return redirect()->route('compatibility_description.index')
                        ->with('success','Compatibility description updated successfully');
    }
}
