<?php

namespace App\Http\Controllers;

use App\Models\Module_description;
use Rap2hpoutre\FastExcel\FastExcel;
use Illuminate\Http\Request;

class ChildrenController extends Controller
{
    function __construct()
    {
        $this->middleware('permission:children-list|children-create|children-edit|children-delete', ['only' => ['index', 'store']]);
        $this->middleware('permission:children-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:children-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:children-delete', ['only' => ['destroy']]);
    }
    
    public function index()
    {
        $phythadetail = Module_description::where('systemtype_id', 1)
                ->where('moduletype_id', 20)
                ->get();
        $chalddetail = Module_description::where('systemtype_id',2)
        ->where('moduletype_id', 20)
        ->get();
        return view('childrens.index', compact('phythadetail', 'chalddetail'));
    }

    public function export()
    {
        $module = Module_description::where('systemtype_id',1)
                ->where('moduletype_id', 20)
                ->where('is_active', 1)
                ->get();
        return (new FastExcel($module))->download('childrens.csv');
    }

    public function create()
    {
        return view('childrens.create');
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'number' => 'required',
            'description' => 'required',
        ]);
        $input = new Module_description();
        $input->systemtype_id = $request->systemtype_id;
        $input->moduletype_id = 20;
        $input->number = $request->number;
        $input->description = $request->description;
        $input->save();

        return redirect()->route('childrens.index')
                        ->with('success','Children Number created successfully');
    }

    public function show($id)
    {
        $module = Module_description::find($id);

        return view('childrens.show',compact('module'));
    }

    public function edit($id)
    {
    $module = Module_description::find($id);
    return view('childrens.edit',compact('module'));
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'description' => 'required',
        ]);
        $input = $request->all();
        $module = Module_description::find($id);
        $module->update($input);
        return redirect()->route('childrens.index')
                        ->with('success','Children Number updated successfully');
    }

    public function destroy($id)
    {
        $data = Module_description::find($id);
        $data->is_active = 0;
        $data->save();
        return redirect()->route('childrens.index')
                        ->with('success','Children Number Blocked successfully');
    }

    public function unblock($id)
    {
        $data = Module_description::find($id);
        $data->is_active = 1;
        $data->save();
        return redirect()->route('childrens.index')
                        ->with('success','Children Number Unblock successfully');
    }
}
