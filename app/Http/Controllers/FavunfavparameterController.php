<?php

namespace App\Http\Controllers;

use App\Models\Fav_unfav_parameter;
use App\Models\Month;
use Illuminate\Http\Request;

class FavunfavparameterController extends Controller
{
    function __construct()
    {
        $this->middleware('permission:fav_unfav-list|fav_unfav-create|fav_unfav-edit|fav_unfav-delete', ['only' => ['index','show']]);
        $this->middleware('permission:fav_unfav-create', ['only' => ['create','store']]);
        $this->middleware('permission:fav_unfav-edit', ['only' => ['edit','update']]);
        $this->middleware('permission:fav_unfav-delete', ['only' => ['destroy']]);
    }

    public function index()
    {
        $favjan = Fav_unfav_parameter::where('type', 1)->where('month_id', 1)->get();
        $favfeb = Fav_unfav_parameter::where('type', 1)->where('month_id', 2)->get();
        $favmar = Fav_unfav_parameter::where('type', 1)->where('month_id', 3)->get();
        $favapr = Fav_unfav_parameter::where('type', 1)->where('month_id', 4)->get();
        $favmay = Fav_unfav_parameter::where('type', 1)->where('month_id', 5)->get();
        $favjun = Fav_unfav_parameter::where('type', 1)->where('month_id', 6)->get();
        $favjul = Fav_unfav_parameter::where('type', 1)->where('month_id', 7)->get();
        $favaug = Fav_unfav_parameter::where('type', 1)->where('month_id', 8)->get();
        $favsep = Fav_unfav_parameter::where('type', 1)->where('month_id', 9)->get();
        $favoct = Fav_unfav_parameter::where('type', 1)->where('month_id', 10)->get();
        $favnev = Fav_unfav_parameter::where('type', 1)->where('month_id', 11)->get();
        $favdec = Fav_unfav_parameter::where('type', 1)->where('month_id', 12)->get();
        $unfavjan = Fav_unfav_parameter::where('type', 2)->where('month_id', 1)->get();
        $unfavfeb = Fav_unfav_parameter::where('type', 2)->where('month_id', 2)->get();
        $unfavmar = Fav_unfav_parameter::where('type', 2)->where('month_id', 3)->get();
        $unfavapr = Fav_unfav_parameter::where('type', 2)->where('month_id', 4)->get();
        $unfavmay = Fav_unfav_parameter::where('type', 2)->where('month_id', 5)->get();
        $unfavjun = Fav_unfav_parameter::where('type', 2)->where('month_id', 6)->get();
        $unfavjul = Fav_unfav_parameter::where('type', 2)->where('month_id', 7)->get();
        $unfavaug = Fav_unfav_parameter::where('type', 2)->where('month_id', 8)->get();
        $unfavsep = Fav_unfav_parameter::where('type', 2)->where('month_id', 9)->get();
        $unfavoct = Fav_unfav_parameter::where('type', 2)->where('month_id', 10)->get();
        $unfavnev = Fav_unfav_parameter::where('type', 2)->where('month_id', 11)->get();
        $unfavdec = Fav_unfav_parameter::where('type', 2)->where('month_id', 12)->get();
        return view('fav_unfav_parameters.index', compact('favjan','favfeb','favmar','favapr','favmay','favjun',
                    'favjul','favaug','favsep','favoct','favnev','favdec','unfavjan','unfavfeb','unfavmar','unfavapr',
                    'unfavmay','unfavjun','unfavjul','unfavaug','unfavsep','unfavoct','unfavnev','unfavdec'));
    }

    public function show($id)
    {
        $parameter = Fav_unfav_parameter::find($id);
        return view('fav_unfav_parameters.show',compact('parameter'));
    }

    public function edit($id)
    {
        $parameter = Fav_unfav_parameter::find($id);
        return view('fav_unfav_parameters.edit',compact('parameter'));
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [ 
            'date' => 'required',
            'number' => 'required',
            'days' => 'required',
            'months' => 'required',
        ]);
        $input = $request->all();
        $partner_relationship = Fav_unfav_parameter::find($id);
        $partner_relationship->update($input);
        return redirect()->route('fav_unfav_parameters.index')
                        ->with('success','Master Number updated successfully');
    }

    public function destroy($id)
    {
        $data = Fav_unfav_parameter::find($id);
        $data->is_active = 0;
        $data->save();
        return redirect()->route('fav_unfav_parameters.index')
                        ->with('success','Master Number Blocked successfully');
    }

    public function unblock($id)
    {
        $data = Fav_unfav_parameter::find($id);
        $data->is_active = 1;
        $data->save();
        return redirect()->route('fav_unfav_parameters.index')
                        ->with('success','Master Number Unblock successfully');
    }
}
