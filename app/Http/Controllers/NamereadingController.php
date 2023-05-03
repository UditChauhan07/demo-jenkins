<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Module_description;
use Rap2hpoutre\FastExcel\FastExcel;
class NamereadingController extends Controller
{

    public function index()
    {
        $phythaname = Module_description::where('systemtype_id',1)
                ->where('moduletype_id', 1)
                ->get();
        $chaldname = Module_description::where('systemtype_id',2)
        ->where('moduletype_id', 1)
        ->get();
        return view('namereading.index', compact('phythaname', 'chaldname'));
    }

    public function export()
    {
        $module = Module_description::where('systemtype_id',1)
                ->where('moduletype_id', 1)
                ->get();
        return (new FastExcel($module))->download('namereading.csv');
    }

    public function create()
    {
        return view('namereading.create');
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'number' => 'required',
            'description' => 'required',
        ]);
        $input = new Module_description();
        $input->systemtype_id = $request->systemtype_id;
        $input->moduletype_id = 1;
        $input->number = $request->number;
        $input->description = $request->description;
        $input->save();

        return redirect()->route('namereading.index')
                        ->with('success','Name Reading Number entered successfully');
    }

    public function show($id)
    {
        $module = Module_description::find($id);

        return view('namereading.show',compact('module'));
    }

    public function edit($id)
    {
    $module = Module_description::find($id);
    return view('namereading.edit',compact('module'));
    }

    public function update(Request $request, $id)
    {
        // $this->validate($request, [
        //    'description' => 'required',
        // ]);
        // $input = $request->all();
        // $module = Module_description::find($id);
        // $module->update($input);
        // return redirect()->route('namereading.index')
        //                 ->with('success','Name Reading Number updated successfully');

        $this->validate($request, [
            'positive_description' => 'required',
        ]);
        $module = Module_description::find($id);
        $positive = $request['positive_description'];
        $negative = $request['negative_description'];
        $description = $positive.'||'.$negative;
        $module->description = $description;
        $module->save();
        return redirect()->route('namereading.index')
                        ->with('success','Name Reading Number updated successfully');
    }

    public function destroy($id)
    {
        $data = Module_description::find($id);
        $data->is_active = 0;
        $data->save();
        return redirect()->route('namereading.index')
                        ->with('success','Name Reading Number Blocked successfully');
    }

    public function unblock($id)
    {
        $data = Module_description::find($id);
        $data->is_active = 1;
        $data->save();
        return redirect()->route('namereading.index')
                        ->with('success','Name Reading Number Unblock successfully');
    }
}
