<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Luckiest_parameter;
class Luckiest_parameterController extends Controller
{
    function __construct()
    {
        $this->middleware('permission:lucky_parameter-list|lucky_parameter-create|lucky_parameter-edit|lucky_parameter-delete', ['only' => ['index','show']]);
        $this->middleware('permission:lucky_parameter-create', ['only' => ['create','store']]);
        $this->middleware('permission:lucky_parameter-edit', ['only' => ['edit','update']]);
        $this->middleware('permission:lucky_parameter-delete', ['only' => ['destroy']]);
    }

    public function index()
    {
        $parameter = Luckiest_parameter::all();
        return view('luckiest_parameters.index', compact('parameter'));
    }

    public function show($id)
    {
        $parameter = Luckiest_parameter::find($id);
        return view('luckiest_parameters.show',compact('parameter'));
    }

    public function edit($id)
    {
    $parameter = Luckiest_parameter::find($id);
    return view('luckiest_parameters.edit',compact('parameter'));
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'lucky_colours' => 'required',
            'lucky_gems' => 'required',
            'lucky_metals' => 'required',
        ]);
        $input = $request->all();
        $module = Luckiest_parameter::find($id);
        $module->update($input);
        return redirect()->route('luckiest_parameters.index')
                        ->with('success','Luckiest Parameters updated successfully');
    }

    public function destroy($id)
    {
        $data = Luckiest_parameter::find($id);
        $data->is_active = 0;
        $data->save();
        return redirect()->route('luckiest_parameters.index')
                        ->with('success','Luckiest Parameters Blocked successfully');
    }

    public function unblock($id)
    {
        $data = Luckiest_parameter::find($id);
        $data->is_active = 1;
        $data->save();
        return redirect()->route('luckiest_parameters.index')
                        ->with('success','Luckiest Parameters Unblock successfully');
    }
}
