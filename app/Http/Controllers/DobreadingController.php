<?php

namespace App\Http\Controllers;
use App\Models\Module_description;
use Rap2hpoutre\FastExcel\FastExcel;
use Illuminate\Http\Request;

class DobreadingController extends Controller
{
    public function index()
    {
        $phythadob = Module_description::where('systemtype_id', 1)
                ->where('moduletype_id', 2)
                ->get();
        $chalddob = Module_description::where('systemtype_id',2)
        ->where('moduletype_id', 2)
        ->get();
        return view('dobreading.index', compact('phythadob', 'chalddob'));
    }

    public function export()
    {
        $module = Module_description::where('systemtype_id',1)
                ->where('moduletype_id', 2)
                ->where('is_active', 1)
                ->get();
        return (new FastExcel($module))->download('dobreading.csv');
    }

    public function create()
    {
        return view('dobreading.create');
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'number' => 'required',
            'description' => 'required',
        ]);
        $input = new Module_description();
        $input->systemtype_id = $request->systemtype_id;
        $input->moduletype_id = 2;
        $input->number = $request->number;
        $input->description = $request->description;
        $input->save();

        return redirect()->route('dobreading.index')
                        ->with('success','DOB Reading Number created successfully');
    }

    public function show($id)
    {
        $module = Module_description::find($id);

        return view('dobreading.show',compact('module'));
    }

    public function edit($id)
    {
    $module = Module_description::find($id);
    return view('dobreading.edit',compact('module'));
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'description' => 'required',
        ]);
        $input = $request->all();
        $module = Module_description::find($id);
        $module->update($input);
        return redirect()->route('dobreading.index')
                        ->with('success','DOB Reading Number updated successfully');
    }

    public function destroy($id)
    {
        $data = Module_description::find($id);
        $data->is_active = 0;
        $data->save();
        return redirect()->route('dobreading.index')
                        ->with('success','DOB Reading Number Blocked successfully');
    }

    public function unblock($id)
    {
        $data = Module_description::find($id);
        $data->is_active = 1;
        $data->save();
        return redirect()->route('dobreading.index')
                        ->with('success','DOB Reading Number Unblock successfully');
    }
}
