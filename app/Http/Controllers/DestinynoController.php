<?php

namespace App\Http\Controllers;
use Rap2hpoutre\FastExcel\FastExcel;
use App\Models\Module_description;
use Illuminate\Http\Request;

class DestinynoController extends Controller
{
    function __construct()
    {
         $this->middleware('permission:destinyno-list|destinyno-create|destinyno-edit|destinyno-delete', ['only' => ['index','store']]);
         $this->middleware('permission:destinyno-create', ['only' => ['create','store']]);
         $this->middleware('permission:destinyno-edit', ['only' => ['edit','update']]);
         $this->middleware('permission:destinyno-delete', ['only' => ['destroy']]);
    }

    public function index()
    {
        $phythadestiny = Module_description::where('systemtype_id',1)
                ->where('moduletype_id', 16)
                ->get();
        $chalddestiny = Module_description::where('systemtype_id',2)
        ->where('moduletype_id', 16)
        ->get();
        return view('destinyno.index', compact('phythadestiny', 'chalddestiny'));
    }

    public function export()
    {
        $module = Module_description::where('systemtype_id',1)
                ->where('moduletype_id', 16)
                ->where('is_active', 1)
                ->get();
        return (new FastExcel($module))->download('destinyno.csv');
    }

    public function create()
    {
        return view('destinyno.create');
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'number' => 'required',
            'description' => 'required',
        ]);
        $input = new Module_description();
        $input->systemtype_id = $request->systemtype_id;
        $input->moduletype_id = 16;
        $input->number = $request->number;
        $input->description = $request->description;
        $input->save();

        return redirect()->route('destinyno.index')
                        ->with('success','Data entered successfully');
    }

    public function show($id)
    {
        $module = Module_description::find($id);

        return view('destinyno.show',compact('module'));
    }

    public function edit($id)
    {
    $module = Module_description::find($id);
    return view('destinyno.edit',compact('module'));
    }

    public function update(Request $request, $id)
    {
        // $this->validate($request, [
        //     'description' => 'required',
        // ]);
        // $input = $request->all();
        // $module = Module_description::find($id);
        // $module->update($input);
        // return redirect()->route('destinyno.index')
        //                 ->with('success','Destiny Number updated successfully');


        $this->validate($request, [
            'learn_to_be' => 'required',
        ]);
        $module = Module_description::find($id);
        $positive = $request['learn_to_be'];
        $negative = $request['learn_not_to_be'];
        $description = $positive . '||' . $negative;
        $module->description = $description;
        $module->save();
        return redirect()->route('destinyno.index')
                        ->with('success','Destiny Number updated successfully');
    }

    public function destroy($id)
    {
        $data = Module_description::find($id);
        $data->is_active = 0;
        $data->save();
        return redirect()->route('destinyno.index')
                        ->with('success','Destiny Number Blocked successfully');
    }

    public function unblock($id)
    {
        $data = Module_description::find($id);
        $data->is_active = 1;
        $data->save();
        return redirect()->route('destinyno.index')
                        ->with('success','Destiny Number Unblock successfully');
    }
}
