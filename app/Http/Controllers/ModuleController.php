<?php

namespace App\Http\Controllers;

use App\Models\Module_type;
use Illuminate\Http\Request;

class ModuleController extends Controller
{
    function __construct()
    {
        $this->middleware('permission:module-list|module-create|module-edit|module-delete', ['only' => ['index','show']]);
        $this->middleware('permission:module-create', ['only' => ['create','store']]);
        $this->middleware('permission:module-edit', ['only' => ['edit','update']]);
        $this->middleware('permission:module-delete', ['only' => ['destroy']]);
    }
    public function index()
    {
        $module = Module_type::all();
        return view('modules.index', compact('module'));
    }

    public function create()
    {
        $parent = Module_type::where('parent', 0)->get();
        return view('modules.create', compact('parent'));
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'description' => 'required',
        ]);

        $input = $request->all();
        $user = Module_type::create($input);

        return redirect()->route('modules.index')
                        ->with('success','Module created successfully');
    }

    public function show($id)
    {
        $module = Module_type::find($id);

        return view('modules.show',compact('module'));
    }

    public function edit($id)
    {
    $module = Module_type::find($id);

    return view('modules.edit',compact('module'));
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'name' => 'required',
            'description' => 'required',
        ]);
        $input = $request->all();
        $module = Module_type::find($id);
        $module->update($input);
        return redirect()->route('modules.index')
                        ->with('success','Module updated successfully');
    }

    public function destroy($id)
    {
        $data = Module_type::find($id);
        $data->is_active = 0;
        $data->save();
        return redirect()->route('modules.index')
                        ->with('success','Module Blocked successfully');
    }

    public function unblock($id)
    {
        $data = Module_type::find($id);
        $data->is_active = 1;
        $data->save();
        return redirect()->route('modules.index')
                        ->with('success','Module Unblock successfully');
    }
}
