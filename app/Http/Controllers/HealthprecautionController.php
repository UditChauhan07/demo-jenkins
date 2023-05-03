<?php

namespace App\Http\Controllers;
use App\Models\Module_description;
use Rap2hpoutre\FastExcel\FastExcel;
use Illuminate\Http\Request;

class HealthprecautionController extends Controller
{
    function __construct()
    {
         $this->middleware('permission:hprecaution-list|hprecaution-create|hprecaution-edit|hprecaution-delete', ['only' => ['index','store']]);
         $this->middleware('permission:hprecaution-create', ['only' => ['create','store']]);
         $this->middleware('permission:hprecaution-edit', ['only' => ['edit','update']]);
         $this->middleware('permission:hprecaution-delete', ['only' => ['destroy']]);
    }

    public function index()
    {
        $phythahealthper = Module_description::where('systemtype_id', 1)
                ->where('moduletype_id', 6)
                ->get();
        $chaldhealthpre = Module_description::where('systemtype_id',2)
        ->where('moduletype_id', 6)
        ->get();
        return view('healthprecaution.index', compact('phythahealthper', 'chaldhealthpre'));
    }

    public function export()
    {
        $module = Module_description::where('systemtype_id',1)
                ->where('moduletype_id', 6)
                ->where('is_active', 1)
                ->get();
        return (new FastExcel($module))->download('healthprecaution.csv');
    }

    public function create()
    {
        return view('healthprecaution.create');
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'number' => 'required',
            'description' => 'required',
        ]);
        $input = new Module_description();
        $input->systemtype_id = $request->systemtype_id;
        $input->moduletype_id = 6;
        $input->number = $request->number;
        $input->description = $request->description;
        $input->save();

        return redirect()->route('healthprecaution.index')
                        ->with('success','Health Precaution created successfully');
    }

    public function show($id)
    {
        $module = Module_description::find($id);

        return view('healthprecaution.show',compact('module'));
    }

    public function edit($id)
    {
    $module = Module_description::find($id);
    return view('healthprecaution.edit',compact('module'));
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'description' => 'required',
        ]);
        $input = $request->all();
        $module = Module_description::find($id);
        $module->update($input);
        return redirect()->route('healthprecaution.index')
                        ->with('success','Health Precaution updated successfully');
    }

    public function destroy($id)
    {
        $data = Module_description::find($id);
        $data->is_active = 0;
        $data->save();
        return redirect()->route('healthprecaution.index')
                        ->with('success','Health Precaution Blocked successfully');
    }

    public function unblock($id)
    {
        $data = Module_description::find($id);
        $data->is_active = 1;
        $data->save();
        return redirect()->route('healthprecaution.index')
                        ->with('success','Health Precaution Unblock successfully');
    }
}
