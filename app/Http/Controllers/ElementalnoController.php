<?php

namespace App\Http\Controllers;
use App\Models\Module_description;
use Rap2hpoutre\FastExcel\FastExcel;
use Illuminate\Http\Request;

class ElementalnoController extends Controller
{
    public function index()
    {
        $phythaelemental = Module_description::where('systemtype_id', 1)
                ->where('moduletype_id', 4)
                ->get();
        $chaldelemental = Module_description::where('systemtype_id',2)
        ->where('moduletype_id', 4)
        ->get();
        return view('elementalno.index', compact('phythaelemental', 'chaldelemental'));
    }

    public function export()
    {
        $module = Module_description::where('systemtype_id',1)
                ->where('moduletype_id', 4)
                ->where('is_active', 1)
                ->get();
        return (new FastExcel($module))->download('elementalno.csv');
    }

    public function create()
    {
        return view('elementalno.create');
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'number' => 'required',
            'description' => 'required',
        ]);
        $input = new Module_description();
        $input->systemtype_id = $request->systemtype_id;
        $input->moduletype_id = 4;
        $input->number = $request->number;
        $input->description = $request->description;
        $input->save();

        return redirect()->route('elementalno.index')
                        ->with('success','Elemental Number created successfully');
    }

    public function show($id)
    {
        $module = Module_description::find($id);

        return view('elementalno.show',compact('module'));
    }

    public function edit($id)
    {
    $module = Module_description::find($id);
    return view('elementalno.edit',compact('module'));
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'description' => 'required',
        ]);
        $input = $request->all();
        $module = Module_description::find($id);
        $module->update($input);
        return redirect()->route('elementalno.index')
                        ->with('success','Elemental Number updated successfully');
    }

    public function destroy($id)
    {
        $data = Module_description::find($id);
        $data->is_active = 0;
        $data->save();
        return redirect()->route('elementalno.index')
                        ->with('success','Elemental Number Blocked successfully');
    }

    public function unblock($id)
    {
        $data = Module_description::find($id);
        $data->is_active = 1;
        $data->save();
        return redirect()->route('elementalno.index')
                        ->with('success','Elemental Number Unblock successfully');
    }
}
