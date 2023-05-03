<?php

namespace App\Http\Controllers;

use App\Models\Primaryno_type;
use Illuminate\Http\Request;

class Primaryno_typeController extends Controller
{
    function __construct()
    {
        $this->middleware('permission:primary_number-list|primary_number-create|primary_number-edit|primary_number-delete', ['only' => ['index','show']]);
        $this->middleware('permission:primary_number-create', ['only' => ['create','store']]);
        $this->middleware('permission:primary_number-edit', ['only' => ['edit','update']]);
        $this->middleware('permission:primary_number-delete', ['only' => ['destroy']]);
    }
    
    public function index()
    {
        $primaryno_type = Primaryno_type::all();
        return view('primaryno_types.index', compact('primaryno_type'));
    }

    public function show($id)
    {
        $primaryno_type = Primaryno_type::find($id);
        return view('primaryno_types.show',compact('primaryno_type'));
    }

    public function edit($id)
    {
        $primaryno_type = Primaryno_type::find($id);
        return view('primaryno_types.edit',compact('primaryno_type'));
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'description' => 'required',
            'positive' => 'required',
            'negative' => 'required',
            'occupations' => 'required',
            'health' => 'required',
            'partners' => 'required',
            'times_of_the_year' => 'required',
            'countries' => 'required',
            'tibbits' => 'required',
        ]);
        $input = $request->all();
        $primaryno_type = Primaryno_type::find($id);
        $primaryno_type->update($input);
        return redirect()->route('primaryno_types.index')
                        ->with('success','Primary Number updated successfully');
    }

    public function destroy($id)
    {
        $data = Primaryno_type::find($id);
        $data->is_active = 0;
        $data->save();
        return redirect()->route('primaryno_types.index')
                        ->with('success','Primary Number Blocked successfully');
    }

    public function unblock($id)
    {
        $data = Primaryno_type::find($id);
        $data->is_active = 1;
        $data->save();
        return redirect()->route('primaryno_types.index')
                        ->with('success','Primary Number Unblock successfully');
    }
}
