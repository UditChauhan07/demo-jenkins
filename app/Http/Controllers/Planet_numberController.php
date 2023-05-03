<?php

namespace App\Http\Controllers;

use App\Models\Planet_number;
use Illuminate\Http\Request;

class Planet_numberController extends Controller
{
    function __construct()
    {
        $this->middleware('permission:planet_number-list|planet_number-create|planet_number-edit|planet_number-delete', ['only' => ['index','show']]);
        $this->middleware('permission:planet_number-create', ['only' => ['create','store']]);
        $this->middleware('permission:planet_number-edit', ['only' => ['edit','update']]);
        $this->middleware('permission:planet_number-delete', ['only' => ['destroy']]);
    }

    public function index()
    {
        $planet_number = Planet_number::all();
        return view('planet_numbers.index', compact('planet_number'));
    }

    public function show($id)
    {
        $planet_number = Planet_number::find($id);
        return view('planet_numbers.show',compact('planet_number'));
    }

    public function edit($id)
    {
        $planet_number = Planet_number::find($id);
        return view('planet_numbers.edit',compact('planet_number'));
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'description' => 'required',
        ]);
        $input = $request->all();
        $planet_number = Planet_number::find($id);
        $planet_number->update($input);
        return redirect()->route('planet_numbers.index')
                        ->with('success','Planet Number updated successfully');
    }

    public function destroy($id)
    {
        $data = Planet_number::find($id);
        $data->is_active = 0;
        $data->save();
        return redirect()->route('planet_numbers.index')
                        ->with('success','Planet Number Blocked successfully');
    }

    public function unblock($id)
    {
        $data = Planet_number::find($id);
        $data->is_active = 1;
        $data->save();
        return redirect()->route('planet_numbers.index')
                        ->with('success','Planet Number Unblock successfully');
    }
}
