<?php

namespace App\Http\Controllers;

use App\Models\Compatible_partner;
use Illuminate\Http\Request;

class Compatible_partnerController extends Controller
{
    function __construct()
    {
        $this->middleware('permission:compatible_partner-list|compatible_partner-create|compatible_partner-edit|compatible_partner-delete', ['only' => ['index','show']]);
        $this->middleware('permission:compatible_partner-create', ['only' => ['create','store']]);
        $this->middleware('permission:compatible_partner-edit', ['only' => ['edit','update']]);
        $this->middleware('permission:compatible_partner-delete', ['only' => ['destroy']]);
    }

    public function index()
    {
        $compatible = Compatible_partner::all();
        return view('compatible_partners.index', compact('compatible'));
    }

    public function show($id)
    {
        $compatible = Compatible_partner::find($id);
        return view('compatible_partners.show',compact('compatible'));
    }

    public function edit($id)
    {
        $compatible = Compatible_partner::find($id);
        return view('compatible_partners.edit',compact('compatible'));
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'description' => 'required',
            'more_compatible_months' => 'required',
            'more_compatible_dates' => 'required',
            'less_compatible_months' => 'required',
            'less_compatible_dates' => 'required',
        ]);
        $input = $request->all();
        $compatible = Compatible_partner::find($id);
        $compatible->update($input);
        return redirect()->route('compatible_partners.index')
                        ->with('success','Compatible Partner updated successfully');
    }
    public function destroy($id)
    {
        $data = Compatible_partner::find($id);
        $data->is_active = 0;
        $data->save();
        return redirect()->route('compatible_partners.index')
                        ->with('success','Compatible Partner Blocked successfully');
    }

    public function unblock($id)
    {
        $data = Compatible_partner::find($id);
        $data->is_active = 1;
        $data->save();
        return redirect()->route('compatible_partners.index')
                        ->with('success','Compatible Partner Unblock successfully');
    }
}
