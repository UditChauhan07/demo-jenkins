<?php

namespace App\Http\Controllers;
use App\Models\Module_description;
use Rap2hpoutre\FastExcel\FastExcel;
use Illuminate\Http\Request;

class DetailedmoneyController extends Controller
{
    function __construct()
    {
         $this->middleware('permission:detailmoney-list|detailmoney-create|detailmoney-edit|detailmoney-delete', ['only' => ['index','store']]);
         $this->middleware('permission:detailmoney-create', ['only' => ['create','store']]);
         $this->middleware('permission:detailmoney-edit', ['only' => ['edit','update']]);
         $this->middleware('permission:detailmoney-delete', ['only' => ['destroy']]);
    }

    public function index()
    {
        $phythadetailed = Module_description::where('systemtype_id', 1)
                ->where('moduletype_id', 15)
                ->get();
        $chalddetailed = Module_description::where('systemtype_id',2)
        ->where('moduletype_id', 15)
        ->get();
        return view('detailedmoney.index', compact('phythadetailed', 'chalddetailed'));
    }

    public function export()
    {
        $module = Module_description::where('systemtype_id',1)
                ->where('moduletype_id', 15)
                ->where('is_active', 1)
                ->get();
        return (new FastExcel($module))->download('detailedmoney.csv');
    }

    public function create()
    {
        return view('detailedmoney.create');
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'number' => 'required',
            'description' => 'required',
        ]);
        $input = new Module_description();
        $input->systemtype_id = $request->systemtype_id;
        $input->moduletype_id = 15;
        $input->number = $request->number;
        $input->description = $request->description;
        $input->save();

        return redirect()->route('detailedmoney.index')
                        ->with('success','Module created successfully');
    }

    public function show($id)
    {
        $module = Module_description::find($id);

        return view('detailedmoney.show',compact('module'));
    }

    public function edit($id)
    {
    $module = Module_description::find($id);
    return view('detailedmoney.edit',compact('module'));
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'description' => 'required',
        ]);
        $input = $request->all();
        $module = Module_description::find($id);
        $module->update($input);
        return redirect()->route('detailedmoney.index')
                        ->with('success','Detailed Money Matters updated successfully');
    }

    public function destroy($id)
    {
        $data = Module_description::find($id);
        $data->is_active = 0;
        $data->save();
        return redirect()->route('detailedmoney.index')
                        ->with('success','Detailed Money Matters Blocked successfully');
    }

    public function unblock($id)
    {
        $data = Module_description::find($id);
        $data->is_active = 1;
        $data->save();
        return redirect()->route('detailedmoney.index')
                        ->with('success','Detailed Money Matters Unblock successfully');
    }
}
