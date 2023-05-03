<?php

namespace App\Http\Controllers;

use App\Models\Personal_parameter;
use Rap2hpoutre\FastExcel\FastExcel;
use Illuminate\Http\Request;

class PersonaldayController extends Controller
{
    public function index()
    {
        $personalday = Personal_parameter::where('type', 4)
                ->get();
        return view('personalday.index', compact('personalday'));
    }

    public function export()
    {
        $module = Personal_parameter::where('type', 4)
                ->get();
        return (new FastExcel($module))->download('personalday.csv');
    }

    public function create()
    {
        return view('personalday.create');
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'number' => 'required',
            'description' => 'required',
        ]);
        $input = new Personal_parameter();
        $input->type = 4;
        $input->number = $request->number;
        $input->description = $request->description;
        $input->save();

        return redirect()->route('personalday.index')
                        ->with('success','Personal day created successfully');
    }

    public function show($id)
    {
        $module = Personal_parameter::find($id);

        return view('personalday.show',compact('module'));
    }

    public function edit($id)
    {
    $module = Personal_parameter::find($id);
    return view('personalday.edit',compact('module'));
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'description' => 'required',

        ]);
        $input = $request->all();
        $module = Personal_parameter::find($id);
        $module->update($input);
        return redirect()->route('personalday.index')
                        ->with('success','Personal day updated successfully');
    }

    public function destroy($id)
    {
        $data = Personal_parameter::find($id);
        $data->is_active = 0;
        $data->save();
        return redirect()->route('personalday.index')
                        ->with('success','Personal day Blocked successfully');
    }

    public function unblock($id)
    {
        $data = Personal_parameter::find($id);
        $data->is_active = 1;
        $data->save();
        return redirect()->route('personalday.index')
                        ->with('success','Personal day Unblock successfully');
    }
}
