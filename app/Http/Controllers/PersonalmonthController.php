<?php

namespace App\Http\Controllers;
use App\Models\Personal_parameter;
use Rap2hpoutre\FastExcel\FastExcel;
use Illuminate\Http\Request;

class PersonalmonthController extends Controller
{
    function __construct()
    {
         $this->middleware('permission:persmonth-list|persmonth-create|persmonth-edit|persmonth-delete', ['only' => ['index','store']]);
         $this->middleware('permission:persmonth-create', ['only' => ['create','store']]);
         $this->middleware('permission:persmonth-edit', ['only' => ['edit','update']]);
         $this->middleware('permission:persmonth-delete', ['only' => ['destroy']]);
    }

    public function index()
    {
        $personalmonth = Personal_parameter::where('type', 2)
                ->get();
        return view('personalmonth.index', compact('personalmonth'));
    }

    public function export()
    {
        $module = Personal_parameter::where('type', 2)
                ->get();
        return (new FastExcel($module))->download('personalmonth.csv');
    }

    public function create()
    {
        return view('personalmonth.create');
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'number' => 'required',
            'description' => 'required',
        ]);
        $input = new Personal_parameter();
        $input->type = 2;
        $input->number = $request->number;
        $input->description = $request->description;
        $input->save();

        return redirect()->route('personalmonth.index')
                        ->with('success','Personal Month created successfully');
    }

    public function show($id)
    {
        $module = Personal_parameter::find($id);

        return view('personalmonth.show',compact('module'));
    }

    public function edit($id)
    {
    $module = Personal_parameter::find($id);
    return view('personalmonth.edit',compact('module'));
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'description' => 'required',
            'love_relationship' => 'required',
            'career' => 'required',

        ]);
        $input = $request->all();
        $module = Personal_parameter::find($id);
        $module->update($input);
        return redirect()->route('personalmonth.index')
                        ->with('success','Personal Month updated successfully');
    }

    public function destroy($id)
    {
        $data = Personal_parameter::find($id);
        $data->is_active = 0;
        $data->save();
        return redirect()->route('personalmonth.index')
                        ->with('success','Personal Month Blocked successfully');
    }

    public function unblock($id)
    {
        $data = Personal_parameter::find($id);
        $data->is_active = 1;
        $data->save();
        return redirect()->route('personalmonth.index')
                        ->with('success','Personal Month Unblock successfully');
    }
}
