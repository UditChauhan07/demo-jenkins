<?php

namespace App\Http\Controllers;
use App\Models\Module_description;
use Rap2hpoutre\FastExcel\FastExcel;
use Illuminate\Http\Request;

class HealthreadingController extends Controller
{
    function __construct()
    {
         $this->middleware('permission:healthreading-list|healthreading-create|healthreading-edit|healthreading-delete', ['only' => ['index','store']]);
         $this->middleware('permission:healthreading-create', ['only' => ['create','store']]);
         $this->middleware('permission:healthreading-edit', ['only' => ['edit','update']]);
         $this->middleware('permission:healthreading-delete', ['only' => ['destroy']]);
    }

    public function index()
    {
        $phythahealth = Module_description::where('systemtype_id', 1)
                ->where('moduletype_id', 5)
                ->get();
        $chaldhealth = Module_description::where('systemtype_id',2)
        ->where('moduletype_id', 5)
        ->get();
        return view('healthreading.index', compact('phythahealth', 'chaldhealth'));
    }

    public function export()
    {
        $module = Module_description::where('systemtype_id',1)
                ->where('moduletype_id', 5)
                ->where('is_active', 1)
                ->get();
        return (new FastExcel($module))->download('healthreading.csv');
    }

    public function create()
    {
        return view('healthreading.create');
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'number' => 'required',
            'description' => 'required',
        ]);
        $input = new Module_description();
        $input->systemtype_id = $request->systemtype_id;
        $input->moduletype_id = 5;
        $input->number = $request->number;
        $input->description = $request->description;
        $input->save();

        return redirect()->route('healthreading.index')
                        ->with('success','Module created successfully');
    }

    public function show($id)
    {
        $module = Module_description::find($id);

        return view('healthreading.show',compact('module'));
    }

    public function edit($id)
    {
    $module = Module_description::find($id);
    return view('healthreading.edit',compact('module'));
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'description' => 'required',
        ]);
        $input = $request->all();
        $module = Module_description::find($id);
        $module->update($input);
        return redirect()->route('healthreading.index')
                        ->with('success','Health Reading Number updated successfully');
    }

    public function destroy($id)
    {
        $data = Module_description::find($id);
        $data->is_active = 0;
        $data->save();
        return redirect()->route('healthreading.index')
                        ->with('success','Health Reading Number Blocked successfully');
    }

    public function unblock($id)
    {
        $data = Module_description::find($id);
        $data->is_active = 1;
        $data->save();
        return redirect()->route('healthreading.index')
                        ->with('success','Health Reading Number Unblock successfully');
    }
}
