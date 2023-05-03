<?php

namespace App\Http\Controllers;
use App\Models\Module_description;
use Rap2hpoutre\FastExcel\FastExcel;
use Illuminate\Http\Request;

class BasicparentingController extends Controller
{
    function __construct()
    {
         $this->middleware('permission:bparenting-list|bparenting-create|bparenting-edit|bparenting-delete', ['only' => ['index','store']]);
         $this->middleware('permission:bparenting-create', ['only' => ['create','store']]);
         $this->middleware('permission:bparenting-edit', ['only' => ['edit','update']]);
         $this->middleware('permission:bparenting-delete', ['only' => ['destroy']]);
    }
    
    public function index()
    {
        $phythabasic = Module_description::where('systemtype_id', 1)
                ->where('moduletype_id', 12)
                ->get();
        $chaldbasic = Module_description::where('systemtype_id',2)
        ->where('moduletype_id', 12)
        ->get();
        return view('basicparenting.index', compact('phythabasic', 'chaldbasic'));
    }

    public function export()
    {
        $module = Module_description::where('systemtype_id',1)
                ->where('moduletype_id', 12)
                ->where('is_active', 1)
                ->get();
        return (new FastExcel($module))->download('basicparenting.csv');
    }

    public function create()
    {
        return view('basicparenting.create');
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'number' => 'required',
            'description' => 'required',
        ]);
        $input = new Module_description();
        $input->systemtype_id = $request->systemtype_id;
        $input->moduletype_id = 12;
        $input->number = $request->number;
        $input->description = $request->description;
        $input->save();

        return redirect()->route('basicparenting.index')
                        ->with('success','Basic Parenting created successfully');
    }

    public function show($id)
    {
        $module = Module_description::find($id);

        return view('basicparenting.show',compact('module'));
    }

    public function edit($id)
    {
    $module = Module_description::find($id);
    return view('basicparenting.edit',compact('module'));
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'description' => 'required',
        ]);
        $input = $request->all();
        $module = Module_description::find($id);
        $module->update($input);
        return redirect()->route('basicparenting.index')
                        ->with('success','Basic Parenting updated successfully');
    }

    public function destroy($id)
    {
        $data = Module_description::find($id);
        $data->is_active = 0;
        $data->save();
        return redirect()->route('basicparenting.index')
                        ->with('success','Basic Parenting blocked successfully');
    }

    public function unblock($id)
    {
        $data = Module_description::find($id);
        $data->is_active = 1;
        $data->save();
        return redirect()->route('basicparenting.index')
                        ->with('success','Basic Parenting Unblock successfully');
    }
}
