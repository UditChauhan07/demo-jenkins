<?php

namespace App\Http\Controllers;
use App\Models\Life_cycle;
use Illuminate\Http\Request;

class Life_cycleController extends Controller
{
    function __construct()
    {
        $this->middleware('permission:life_cycle-list|life_cycle-create|life_cycle-edit|life_cycle-delete', ['only' => ['index','show']]);
        $this->middleware('permission:life_cycle-create', ['only' => ['create','store']]);
        $this->middleware('permission:life_cycle-edit', ['only' => ['edit','update']]);
        $this->middleware('permission:life_cycle-delete', ['only' => ['destroy']]);
    }

    public function index()
    {
        $life_cycle = Life_cycle::all();
        return view('life_cycles.index', compact('life_cycle'));
    }

    public function show($id)
    {
        $life_cycle = Life_cycle::find($id);
        return view('life_cycles.show',compact('life_cycle'));
    }

    public function edit($id)
    {
        $life_cycle = Life_cycle::find($id);
        return view('life_cycles.edit',compact('life_cycle'));
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'cycle_by_month' => 'required',
            'cycle_by_date' => 'required',
            'cycle_by_year' => 'required',
        ]);
        $input = $request->all();
        $life_cycle = Life_cycle::find($id);
        $life_cycle->update($input);
        return redirect()->route('life_cycles.index')
                        ->with('success','Life Cycles updated successfully');
    }

    public function destroy($id)
    {
        $data = Life_cycle::find($id);
        $data->is_active = 0;
        $data->save();
        return redirect()->route('life_cycles.index')
                        ->with('success','Life Cycles Blocked successfully');
    }

    public function unblock($id)
    {
        $data = Life_cycle::find($id);
        $data->is_active = 1;
        $data->save();
        return redirect()->route('life_cycles.index')
                        ->with('success','Life Cycles Unblock successfully');
    }
}
