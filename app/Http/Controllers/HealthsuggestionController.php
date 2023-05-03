<?php

namespace App\Http\Controllers;
use App\Models\Module_description;
use Rap2hpoutre\FastExcel\FastExcel;
use Illuminate\Http\Request;

class HealthsuggestionController extends Controller
{
    function __construct()
    {
         $this->middleware('permission:suggmonth-list|suggmonth-create|suggmonth-edit|suggmonth-delete', ['only' => ['index','store']]);
         $this->middleware('permission:suggmonth-create', ['only' => ['create','store']]);
         $this->middleware('permission:suggmonth-edit', ['only' => ['edit','update']]);
         $this->middleware('permission:suggmonth-delete', ['only' => ['destroy']]);
    }

    public function index()
    {
        $phythahealthsugg = Module_description::where('systemtype_id', 1)
                ->where('moduletype_id', 7)
                ->get();
        $chaldhealthsugg = Module_description::where('systemtype_id',2)
        ->where('moduletype_id', 7)
        ->get();
        return view('healthsuggestion.index', compact('phythahealthsugg', 'chaldhealthsugg'));
    }

    public function export()
    {
        $module = Module_description::where('systemtype_id',1)
                ->where('moduletype_id', 7)
                ->where('is_active', 1)
                ->get();
        return (new FastExcel($module))->download('healthsuggestion.csv');
    }

    public function create()
    {
        return view('healthsuggestion.create');
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'number' => 'required',
            'description' => 'required',
        ]);
        $input = new Module_description();
        $input->systemtype_id = $request->systemtype_id;
        $input->moduletype_id = 7;
        $input->number = $request->number;
        $input->description = $request->description;
        $input->save();

        return redirect()->route('healthsuggestion.index')
                        ->with('success','Module created successfully');
    }

    public function show($id)
    {
        $module = Module_description::find($id);

        return view('healthsuggestion.show',compact('module'));
    }

    public function edit($id)
    {
    $module = Module_description::find($id);
    return view('healthsuggestion.edit',compact('module'));
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'description' => 'required',
        ]);
        $input = $request->all();
        $module = Module_description::find($id);
        $module->update($input);
        return redirect()->route('healthsuggestion.index')
                        ->with('success','Health Suggestion updated successfully');
    }

    public function destroy($id)
    {
        $data = Module_description::find($id);
        $data->is_active = 0;
        $data->save();
        return redirect()->route('healthsuggestion.index')
                        ->with('success','Health Suggestion Blocked successfully');
    }

    public function unblock($id)
    {
        $data = Module_description::find($id);
        $data->is_active = 1;
        $data->save();
        return redirect()->route('healthsuggestion.index')
                        ->with('success','Health Suggestion Unblock successfully');
    }
}
