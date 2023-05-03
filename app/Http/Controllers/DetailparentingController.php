<?php

namespace App\Http\Controllers;
use App\Models\Module_description;
use Rap2hpoutre\FastExcel\FastExcel;
use Illuminate\Http\Request;

class DetailparentingController extends Controller
{
    function __construct()
    {
         $this->middleware('permission:dparenting-list|dparenting-create|dparenting-edit|dparenting-delete', ['only' => ['index','store']]);
         $this->middleware('permission:dparenting-create', ['only' => ['create','store']]);
         $this->middleware('permission:dparenting-edit', ['only' => ['edit','update']]);
         $this->middleware('permission:dparenting-delete', ['only' => ['destroy']]);
    }

    public function index()
    {
        $phythadetail = Module_description::where('systemtype_id', 1)
                ->where('moduletype_id', 13)
                ->get();
        $chalddetail = Module_description::where('systemtype_id',2)
        ->where('moduletype_id', 13)
        ->get();
        return view('detailparenting.index', compact('phythadetail', 'chalddetail'));
    }

    public function export()
    {
        $module = Module_description::where('systemtype_id',1)
                ->where('moduletype_id', 13)
                ->where('is_active', 1)
                ->get();
        return (new FastExcel($module))->download('detailparenting.csv');
    }

    public function create()
    {
        return view('detailparenting.create');
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'number' => 'required',
            'description' => 'required',
        ]);
        $input = new Module_description();
        $input->systemtype_id = $request->systemtype_id;
        $input->moduletype_id = 13;
        $input->number = $request->number;
        $input->description = $request->description;
        $input->save();

        return redirect()->route('detailparenting.index')
                        ->with('success','Detailed Parenting created successfully');
    }

    public function show($id)
    {
        $module = Module_description::find($id);

        return view('detailparenting.show',compact('module'));
    }

    public function edit($id)
    {
    $module = Module_description::find($id);
    return view('detailparenting.edit',compact('module'));
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'description' => 'required',
        ]);
        $input = $request->all();
        $module = Module_description::find($id);
        $module->update($input);
        return redirect()->route('detailparenting.index')
                        ->with('success','Detailed Parenting updated successfully');
    }

    public function destroy($id)
    {
        $data = Module_description::find($id);
        $data->is_active = 0;
        $data->save();
        return redirect()->route('detailparenting.index')
                        ->with('success','Detailed Parenting Blocked successfully');
    }

    public function unblock($id)
    {
        $data = Module_description::find($id);
        $data->is_active = 1;
        $data->save();
        return redirect()->route('detailparenting.index')
                        ->with('success','Detailed Parenting Unblock successfully');
    }
}
