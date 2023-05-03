<?php

namespace App\Http\Controllers;
use App\Models\Personal_parameter;
use Rap2hpoutre\FastExcel\FastExcel;
use Illuminate\Http\Request;

class PersonalyearController extends Controller
{
    function __construct()
    {
         $this->middleware('permission:persyear-list|persyear-create|persyear-edit|persyear-delete', ['only' => ['index','store']]);
         $this->middleware('permission:persyear-create', ['only' => ['create','store']]);
         $this->middleware('permission:persyear-edit', ['only' => ['edit','update']]);
         $this->middleware('permission:persyear-delete', ['only' => ['destroy']]);
    }

    public function index()
    {
        $personalyear = Personal_parameter::where('type', 1)
                    ->get();
        return view('personalyear.index', compact('personalyear'));
    }

    public function export()
    {
        $module = Personal_parameter::where('type',1)
                ->get();
        return (new FastExcel($module))->download('personalyear.csv');
    }

    public function create()
    {
        return view('personalyear.create');
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'number' => 'required',
            'description' => 'required',
        ]);
        $input = new Personal_parameter();
        $input->type = $request->type;
        $input->number = $request->number;
        $input->description = $request->description;
        $input->save();

        return redirect()->route('personalyear.index')
                        ->with('success','Personal Year created successfully');
    }

    public function show($id)
    {
        $module = Personal_parameter::find($id);

        return view('personalyear.show',compact('module'));
    }

    public function edit($id)
    {
    $module = Personal_parameter::find($id);
    return view('personalyear.edit',compact('module'));
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'description' => 'required',
            'love_relationship' => 'required',
            'health' => 'required',
            'career' => 'required',
            'travel' => 'required',
        ]);
        $input = $request->all();
        $module = Personal_parameter::find($id);
        $module->update($input);
        return redirect()->route('personalyear.index')
                        ->with('success','Personal Year updated successfully');
    }

    public function destroy($id)
    {
        $data = Personal_parameter::find($id);
        $data->is_active = 0;
        $data->save();
        return redirect()->route('personalyear.index')
                        ->with('success','Personal Year Blocked successfully');
    }

    public function unblock($id)
    {
        $data = Personal_parameter::find($id);
        $data->is_active = 1;
        $data->save();
        return redirect()->route('personalyear.index')
                        ->with('success','Personal Year Unblock successfully');
    }
}
