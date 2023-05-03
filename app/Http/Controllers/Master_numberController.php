<?php

namespace App\Http\Controllers;
use App\Models\Master_number;
use Illuminate\Http\Request;

class Master_numberController extends Controller
{
    function __construct()
    {
        $this->middleware('permission:master_number-list|master_number-create|master_number-edit|master_number-delete', ['only' => ['index','show']]);
        $this->middleware('permission:master_number-create', ['only' => ['create','store']]);
        $this->middleware('permission:master_number-edit', ['only' => ['edit','update']]);
        $this->middleware('permission:master_number-delete', ['only' => ['destroy']]);
    }

    public function index()
    {
        $master_number = Master_number::all();
        return view('master_numbers.index', compact('master_number'));
    }

    public function show($id)
    {
        $master_number = Master_number::find($id);
        return view('master_numbers.show',compact('master_number'));
    }

    public function edit($id)
    {
    $master_number = Master_number::find($id);
    return view('master_numbers.edit',compact('master_number'));
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'description' => 'required',
        ]);
        $input = $request->all();
        $module = Master_number::find($id);
        $module->update($input);
        return redirect()->route('master_numbers.index')
                        ->with('success','Master Number updated successfully');
    }

    public function destroy($id)
    {
        $data = Master_number::find($id);
        $data->is_active = 0;
        $data->save();
        return redirect()->route('master_numbers.index')
                        ->with('success','Master Number Blocked successfully');
    }

    public function unblock($id)
    {
        $data = Master_number::find($id);
        $data->is_active = 1;
        $data->save();
        return redirect()->route('master_numbers.index')
                        ->with('success','Master Number Unblock successfully');
    }
}
