<?php

namespace App\Http\Controllers;

use App\Models\Partner_relationship;
use Illuminate\Http\Request;

class Partner_relationshipController extends Controller
{
    function __construct()
    {
        $this->middleware('permission:partner_relation-list|partner_relation-create|partner_relation-edit|partner_relation-delete', ['only' => ['index','show']]);
        $this->middleware('permission:partner_relation-create', ['only' => ['create','store']]);
        $this->middleware('permission:partner_relation-edit', ['only' => ['edit','update']]);
        $this->middleware('permission:partner_relation-delete', ['only' => ['destroy']]);
    }

    public function index()
    {
        $partner_relationship = Partner_relationship::where('is_active',1)->paginate(9);
        return view('partner_relationships.index', compact('partner_relationship'));
    }

    public function show($id)
    {
        $partner_relationship = Partner_relationship::find($id);
        return view('partner_relationships.show',compact('partner_relationship'));
    }

    public function edit($id)
    {
        $partner_relationship = Partner_relationship::find($id);
        return view('partner_relationships.edit',compact('partner_relationship'));
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'description' => 'required',
        ]);
        $input = $request->all();
        $partner_relationship = Partner_relationship::find($id);
        $partner_relationship->update($input);
        return redirect()->route('partner_relationships.index')
                        ->with('success','Partner Relationship updated successfully');
    }

    public function destroy($id)
    {
        $data = Partner_relationship::find($id);
        $data->is_active = 0;
        $data->save();
        return redirect()->route('partner_relationships.index')
                        ->with('success','Partner Relationship Blocked successfully');
    }

    public function unblock($id)
    {
        $data = Partner_relationship::find($id);
        $data->is_active = 1;
        $data->save();
        return redirect()->route('partner_relationships.index')
                        ->with('success','Partner Relationship Unblock successfully');
    }
}
