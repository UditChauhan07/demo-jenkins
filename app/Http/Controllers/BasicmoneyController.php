<?php

namespace App\Http\Controllers;
use App\Models\Module_description;
use Rap2hpoutre\FastExcel\FastExcel;
use Illuminate\Http\Request;

class BasicmoneyController extends Controller
{
    function __construct()
    {
         $this->middleware('permission:basicmoney-list|basicmoney-create|basicmoney-edit|basicmoney-delete', ['only' => ['index','store']]);
         $this->middleware('permission:basicmoney-create', ['only' => ['create','store']]);
         $this->middleware('permission:basicmoney-edit', ['only' => ['edit','update']]);
         $this->middleware('permission:basicmoney-delete', ['only' => ['destroy']]);
    }

    public function index()
    {
        $phythabasic = Module_description::where('systemtype_id', 1)
                ->where('moduletype_id', 14)
                ->get();
        $chaldbasic = Module_description::where('systemtype_id',2)
        ->where('moduletype_id', 14)
        ->get();
        return view('basicmoney.index', compact('phythabasic', 'chaldbasic'));
    }

    public function export()
    {
        $module = Module_description::where('systemtype_id',1)
                ->where('moduletype_id', 14)
                ->get();
        return (new FastExcel($module))->download('basicmoney.csv');
    }

    public function create()
    {
        return view('basicmoney.create');
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'number' => 'required',
            'description' => 'required',
        ]);
        $input = new Module_description();
        $input->systemtype_id = $request->systemtype_id;
        $input->moduletype_id = 14;
        $input->number = $request->number;
        $input->description = $request->description;
        $input->save();

        return redirect()->route('basicmoney.index')
                        ->with('success','Module created successfully');
    }

    public function show($id)
    {
        $module = Module_description::find($id);

        return view('basicmoney.show',compact('module'));
    }

    public function edit($id)
    {
    $module = Module_description::find($id);
    return view('basicmoney.edit',compact('module'));
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'description' => 'required',
        ]);
        $input = $request->all();
        $module = Module_description::find($id);
        $module->update($input);
        return redirect()->route('basicmoney.index')
                        ->with('success','Module updated successfully');
    }

    public function destroy($id)
    {
        $data = Module_description::find($id);
        $data->is_active = 0;
        $data->save();
        return redirect()->route('basicmoney.index')
                        ->with('success','Module Blocked successfully');
    }

    public function unblock($id)
    {
        $data = Module_description::find($id);
        $data->is_active = 1;
        $data->save();
        return redirect()->route('basicmoney.index')
                        ->with('success','Module Unblock successfully');
    }
}
