<?php

namespace App\Http\Controllers;

use App\Models\Universal_perameter;
use Illuminate\Http\Request;
use Rap2hpoutre\FastExcel\FastExcel;

class UniversalmonthController extends Controller
{
    public function index()
    {
        $universalmonth = Universal_perameter::where('type', 2)
                ->get();
        return view('universalmonth.index', compact('universalmonth'));
    }

    public function export()
    {
        $module = Universal_perameter::where('type', 2)
                ->get();
        return (new FastExcel($module))->download('universalmonth.csv');
    }

    public function create()
    {
        return view('universalmonth.create');
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'number' => 'required',
            'description' => 'required',
        ]);
        $input = new Universal_perameter();
        $input->type = 2;
        $input->number = $request->number;
        $input->description = $request->description;
        $input->save();

        return redirect()->route('universalmonth.index')
                        ->with('success','Universal month created successfully');
    }

    public function show($id)
    {
        $module = Universal_perameter::find($id);

        return view('universalmonth.show',compact('module'));
    }

    public function edit($id)
    {
    $module = Universal_perameter::find($id);
    return view('universalmonth.edit',compact('module'));
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'description' => 'required',

        ]);
        $input = $request->all();
        $module = Universal_perameter::find($id);
        $module->update($input);
        return redirect()->route('universalmonth.index')
                        ->with('success','Universal month updated successfully');
    }

    public function destroy($id)
    {
        $data = Universal_perameter::find($id);
        $data->is_active = 0;
        $data->save();
        return redirect()->route('universalmonth.index')
                        ->with('success','Universal month Blocked successfully');
    }

    public function unblock($id)
    {
        $data = Universal_perameter::find($id);
        $data->is_active = 1;
        $data->save();
        return redirect()->route('universalmonth.index')
                        ->with('success','Universal month Unblock successfully');
    }
}
