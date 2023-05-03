<?php

namespace App\Http\Controllers;

use App\Models\Compatibility_percentage;
use Illuminate\Http\Request;

class Compatibility_percentageController extends Controller
{
    function __construct()
    {
         $this->middleware('permission:compatibility_percentage-list|compatibility_percentage-create|compatibility_percentage-edit|compatibility_percentage-delete', ['only' => ['index','store']]);
         $this->middleware('permission:compatibility_percentage-create', ['only' => ['create','store']]);
         $this->middleware('permission:compatibility_percentage-edit', ['only' => ['edit','update']]);
         $this->middleware('permission:compatibility_percentage-delete', ['only' => ['destroy']]);
    }

    public function index()
    {
        $compatiblepercentage = Compatibility_percentage::where('is_active',1)->paginate(9);
        return view('compatibility_percentage.index', compact('compatiblepercentage'));
    }

    public function show($id)
    {
        $compatiblepercentage = Compatibility_percentage::find($id);
        return view('compatibility_percentage.show',compact('compatiblepercentage'));
    }

    public function edit($id)
    {
        $compatiblepercentage = Compatibility_percentage::find($id);
        return view('compatibility_percentage.edit',compact('compatiblepercentage'));
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'compatibility_number' => 'required',
            'compatibility_percentage' => 'required',
            'strength' => 'required',
        ]);
        $input = $request->all();
        $compatiblepercentage = Compatibility_percentage::find($id);
        $compatiblepercentage->update($input);
        return redirect()->route('compatibility_percentage.index')
                        ->with('success','Compatible Scale updated successfully');
    }
}
