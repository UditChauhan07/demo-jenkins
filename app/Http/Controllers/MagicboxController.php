<?php

namespace App\Http\Controllers;

use App\Models\Module_description;
use Rap2hpoutre\FastExcel\FastExcel;
use Illuminate\Http\Request;

class MagicboxController extends Controller
{
    public function index()
    {
        $phythamagic = Module_description::where('systemtype_id', 1)
            ->where('moduletype_id', 3)
            ->get();
        $chaldmagic = Module_description::where('systemtype_id', 2)
            ->where('moduletype_id', 3)
            ->get();
        return view('magicbox.index', compact('phythamagic', 'chaldmagic'));
    }

    public function export()
    {
        $module = Module_description::where('systemtype_id', 1)
            ->where('moduletype_id', 3)
            ->where('is_active', 1)
            ->get();
        return (new FastExcel($module))->download('magicbox.csv');
    }

    public function create()
    {
        return view('magicbox.create');
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'description' => 'required',
        ]);
        $input = new Module_description();
        $input->systemtype_id = $request->systemtype_id;
        $input->moduletype_id = 3;
        $input->number = $request->number;
        $input->description = $request->description;
        $input->save();

        return redirect()->route('magicbox.index')
            ->with('success', 'Data created successfully');
    }

    public function show($id)
    {
        $module = Module_description::find($id);

        return view('magicbox.show', compact('module'));
    }

    public function edit($id)
    {
        $module = Module_description::find($id);
        return view('magicbox.edit', compact('module'));
    }

    public function update(Request $request, $id)
    {
        // $this->validate($request, [
        //     'number' => 'required',
        //     'description' => 'required',
        // ]);
        // $input = $request->all();
        // $module = Module_description::find($id);
        // $module->update($input);
        // return redirect()->route('magicbox.index')
        //                 ->with('success','Magic Box updated successfully');


        $this->validate($request, [
            'box_des' => 'required',
        ]);
        $module = Module_description::find($id);
        $box = $request['box_des'];
        $manys = $request['manys_des'];
        $few_no = $request['few_no_des'];
        $description = $box . '||' . $manys . '||' . $few_no;
        $module->description = $description;
        $module->save();
        return redirect()->route('magicbox.index')
            ->with('success', 'Magic Box updated successfully');
    }

    public function destroy($id)
    {
        $data = Module_description::find($id);
        $data->is_active = 0;
        $data->save();
        return redirect()->route('magicbox.index')
            ->with('success', 'Magic Box Blocked successfully');
    }

    public function unblock($id)
    {
        $data = Module_description::find($id);
        $data->is_active = 1;
        $data->save();
        return redirect()->route('magicbox.index')
            ->with('success', 'Magic Box Unblock successfully');
    }
}
