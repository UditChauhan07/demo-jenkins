<?php

namespace App\Http\Controllers;
use Rap2hpoutre\FastExcel\FastExcel;
use App\Models\Module_description;
use Illuminate\Http\Request;

class HealthcycleController extends Controller
{
    public function index()
    {
        $phythahealthcyc = Module_description::where('systemtype_id', 1)
                ->where('moduletype_id', 17)
                ->get();
        $chaldhealthcyc = Module_description::where('systemtype_id',2)
        ->where('moduletype_id', 17)
        ->get();
        return view('healthcycle.index', compact('phythahealthcyc', 'chaldhealthcyc'));
    }

    public function export()
    {
        $module = Module_description::where('systemtype_id',1)
                ->where('moduletype_id', 17)
                ->where('is_active', 1)
                ->get();
        return (new FastExcel($module))->download('healthcycle.csv');
    }

    public function create()
    {
        return view('healthcycle.create');
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'number' => 'required',
            'description' => 'required',
        ]);
        $input = new Module_description();
        $input->systemtype_id = $request->systemtype_id;
        $input->moduletype_id = 17;
        $input->number = $request->number;
        $input->description = $request->description;
        $input->save();

        return redirect()->route('healthcycle.index')
                        ->with('success','Health Cycle created successfully');
    }

    public function show($id)
    {
        $module = Module_description::find($id);

        return view('healthcycle.show',compact('module'));
    }

    public function edit($id)
    {
    $module = Module_description::find($id);
    return view('healthcycle.edit',compact('module'));
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'description' => 'required',
        ]);
        $input = $request->all();
        $module = Module_description::find($id);
        $module->update($input);
        return redirect()->route('healthcycle.index')
                        ->with('success','Health Cycle updated successfully');
    }

    public function destroy($id)
    {
        $data = Module_description::find($id);
        $data->is_active = 0;
        $data->save();
        return redirect()->route('healthcycle.index')
                        ->with('success','Health Cycle Blocked successfully');
    }

    public function unblock($id)
    {
        $data = Module_description::find($id);
        $data->is_active = 1;
        $data->save();
        return redirect()->route('healthcycle.index')
                        ->with('success','Health Cycle Unblock successfully');
    }
}
