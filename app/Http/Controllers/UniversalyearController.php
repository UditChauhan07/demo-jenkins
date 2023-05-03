<?php

namespace App\Http\Controllers;

use App\Models\Universal_perameter;
use Illuminate\Http\Request;
use Rap2hpoutre\FastExcel\FastExcel;

class UniversalyearController extends Controller
{
    public function index()
    {
        $universalyear = Universal_perameter::where('type', 1)
                ->get();
        return view('universalyear.index', compact('universalyear'));
    }

    public function export()
    {
        $module = Universal_perameter::where('type', 1)
                ->get();
        return (new FastExcel($module))->download('universalyear.csv');
    }

    public function create()
    {
        return view('universalyear.create');
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'number' => 'required',
            'description' => 'required',
        ]);
        $input = new Universal_perameter();
        $input->type = 1;
        $input->number = $request->number;
        $input->description = $request->description;
        $input->save();

        return redirect()->route('universalyear.index')
                        ->with('success','Universal Year created successfully');
    }

    public function show($id)
    {
        $module = Universal_perameter::find($id);

        return view('universalyear.show',compact('module'));
    }

    public function edit($id)
    {
    $module = Universal_perameter::find($id);
    return view('universalyear.edit',compact('module'));
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'description' => 'required',

        ]);
        $input = $request->all();
        $module = Universal_perameter::find($id);
        $module->update($input);
        return redirect()->route('universalyear.index')
                        ->with('success','Universal Year updated successfully');
    }

    public function destroy($id)
    {
        $data = Universal_perameter::find($id);
        $data->is_active = 0;
        $data->save();
        return redirect()->route('universalyear.index')
                        ->with('success','Universal Year Blocked successfully');
    }

    public function unblock($id)
    {
        $data = Universal_perameter::find($id);
        $data->is_active = 1;
        $data->save();
        return redirect()->route('universalyear.index')
                        ->with('success','Universal Year Unblock successfully');
    }
}
