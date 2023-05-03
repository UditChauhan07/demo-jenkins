<?php

namespace App\Http\Controllers;

use App\Models\Life_change;
use Illuminate\Http\Request;

class Life_changeController extends Controller
{
    function __construct()
    {
        $this->middleware('permission:life_changes-list|life_changes-create|life_changes-edit|life_changes-delete', ['only' => ['index','show']]);
        $this->middleware('permission:life_changes-create', ['only' => ['create','store']]);
        $this->middleware('permission:life_changes-edit', ['only' => ['edit','update']]);
        $this->middleware('permission:life_changes-delete', ['only' => ['destroy']]);
    }
    
    public function index()
    {
        $life_change = Life_change::all();
        return view('life_changes.index', compact('life_change'));
    }

    public function show($id)
    {
        $life_change = Life_change::find($id);
        return view('life_changes.show',compact('life_change'));
    }

    public function edit($id)
    {
        $life_change = Life_change::find($id);
        return view('life_changes.edit',compact('life_change'));
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'ages' => 'required',
            'years' => 'required',
        ]);
        $input = $request->all();
        $life_change = Life_change::find($id);
        $life_change->update($input);
        return redirect()->route('life_changes.index')
                        ->with('success','Life Changes updated successfully');
    }

    public function destroy($id)
    {
        $data = Life_change::find($id);
        $data->is_active = 0;
        $data->save();
        return redirect()->route('life_changes.index')
                        ->with('success','Life Changes Blocked successfully');
    }

    public function unblock($id)
    {
        $data = Life_change::find($id);
        $data->is_active = 1;
        $data->save();
        return redirect()->route('life_changes.index')
                        ->with('success','Life Changes Unblock successfully');
    }
}
