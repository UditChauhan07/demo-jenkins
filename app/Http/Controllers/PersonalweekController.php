<?php

namespace App\Http\Controllers;

use App\Models\Personal_parameter;
use Rap2hpoutre\FastExcel\FastExcel;
use Illuminate\Http\Request;

class PersonalweekController extends Controller
{
    public function index()
    {
        $personalweek = Personal_parameter::where('type', 3)
                ->get();
        return view('personalweek.index', compact('personalweek'));
    }

    public function export()
    {
        $module = Personal_parameter::where('type', 3)
                ->get();
        return (new FastExcel($module))->download('personalweek.csv');
    }

    public function create()
    {
        return view('personalweek.create');
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'number' => 'required',
            'description' => 'required',
        ]);
        $input = new Personal_parameter();
        $input->type = 3;
        $input->number = $request->number;
        $input->description = $request->description;
        $input->save();

        return redirect()->route('personalweek.index')
                        ->with('success','Personal week created successfully');
    }

    public function show($id)
    {
        $module = Personal_parameter::find($id);

        return view('personalweek.show',compact('module'));
    }

    public function edit($id)
    {
    $module = Personal_parameter::find($id);
    return view('personalweek.edit',compact('module'));
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'description' => 'required',

        ]);
        $input = $request->all();
        $module = Personal_parameter::find($id);
        $module->update($input);
        return redirect()->route('personalweek.index')
                        ->with('success','Personal week updated successfully');
    }

    public function destroy($id)
    {
        $data = Personal_parameter::find($id);
        $data->is_active = 0;
        $data->save();
        return redirect()->route('personalweek.index')
                        ->with('success','Personal week Blocked successfully');
    }

    public function unblock($id)
    {
        $data = Personal_parameter::find($id);
        $data->is_active = 1;
        $data->save();
        return redirect()->route('personalweek.index')
                        ->with('success','Personal week Unblock successfully');
    }
}
