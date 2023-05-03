<?php

namespace App\Http\Controllers;

use App\Models\Universal_perameter;
use Illuminate\Http\Request;
use Rap2hpoutre\FastExcel\FastExcel;

class UniversaldayController extends Controller
{
    public function index()
    {
        $universalday = Universal_perameter::where('type', 3)
                ->get();
        return view('universalday.index', compact('universalday'));
    }

    public function export()
    {
        $module = Universal_perameter::where('type', 3)
                ->get();
        return (new FastExcel($module))->download('universalday.csv');
    }

    public function create()
    {
        return view('universalday.create');
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'number' => 'required',
            'description' => 'required',
        ]);
        $input = new Universal_perameter();
        $input->type = 3;
        $input->number = $request->number;
        $input->description = $request->description;
        $input->save();

        return redirect()->route('universalday.index')
                        ->with('success','Universal day created successfully');
    }

    public function show($id)
    {
        $module = Universal_perameter::find($id);

        return view('universalday.show',compact('module'));
    }

    public function edit($id)
    {
    $module = Universal_perameter::find($id);
    return view('universalday.edit',compact('module'));
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'description' => 'required',

        ]);
        $input = $request->all();
        $module = Universal_perameter::find($id);
        $module->update($input);
        return redirect()->route('universalday.index')
                        ->with('success','Universal day updated successfully');
    }

    public function destroy($id)
    {
        $data = Universal_perameter::find($id);
        $data->is_active = 0;
        $data->save();
        return redirect()->route('universalday.index')
                        ->with('success','Universal day Blocked successfully');
    }

    public function unblock($id)
    {
        $data = Universal_perameter::find($id);
        $data->is_active = 1;
        $data->save();
        return redirect()->route('universalday.index')
                        ->with('success','Universal day Unblock successfully');
    }
}
