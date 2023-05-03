<?php

namespace App\Http\Controllers;

use App\Models\Possesion;
use Illuminate\Http\Request;

class PossesionController extends Controller
{
    function __construct()
    {
         $this->middleware('permission:possesion-list|possesion-create|possesion-edit|possesion-delete', ['only' => ['index','store']]);
         $this->middleware('permission:possesion-create', ['only' => ['create','store']]);
         $this->middleware('permission:possesion-edit', ['only' => ['edit','update']]);
         $this->middleware('permission:possesion-delete', ['only' => ['destroy']]);
    }
    
    public function index()
    {
        $possesion = Possesion::all();
        return view('possesions.index', compact('possesion'));
    }

    public function create()
    {
        return view('possesions.create');
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'description' => 'required',
        ]);

        $possesion = Possesion::latest()->first();
        if($possesion!= null){
        $type = $possesion->type + 1;
        }else{
            $type = 1;
        }
        $name = $request->name;
        $description = $request->description;

        $user = Possesion::create(['name'=> $name, 'type'=>$type, 'description'=>$description]);

        return redirect()->route('possesions.index')
                        ->with('success','Possesion created successfully');
    }

    public function show($id)
    {
        $possesion = Possesion::find($id);
        return view('possesions.show',compact('possesion'));
    }

    public function edit($id)
    {
        $possesion = Possesion::find($id);
        return view('possesions.edit',compact('possesion'));
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'name' => 'required',
            'description' => 'required',
        ]);
        $input = $request->all();
        $possesion = Possesion::find($id);
        $possesion->update($input);
        return redirect()->route('possesions.index')
                        ->with('success','Possesion updated successfully');
    }
}
