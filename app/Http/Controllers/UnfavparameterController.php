<?php

namespace App\Http\Controllers;

use App\Models\Fav_unfav_parameter;
use Illuminate\Http\Request;

class UnfavparameterController extends Controller
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
            return view('unfav_parameters.index', compact('unfavjan','unfavfeb','unfavmar','unfavapr',
                        'unfavmay','unfavjun','unfavjul','unfavaug','unfavsep','unfavoct','unfavnev','unfavdec'));
        }
    
        public function show($id)
        {
            $parameter = Fav_unfav_parameter::find($id);
            return view('unfav_parameters.show',compact('parameter'));
        }
    
        public function edit($id)
        {
            $parameter = Fav_unfav_parameter::find($id);
            return view('unfav_parameters.edit',compact('parameter'));
        }
    
        public function update(Request $request, $id)
        {
            $this->validate($request, [
                'numbers' => 'required',
                'days' => 'required',
                'months' => 'required',
            ]);
            $input = $request->all();
            $partner_relationship = Fav_unfav_parameter::find($id);
            $partner_relationship->update($input);
            return redirect()->route('unfav_parameters.index')
                            ->with('success','Unfav Parameters updated successfully');
        }
    
        public function destroy($id)
        {
            $data = Fav_unfav_parameter::find($id);
            $data->is_active = 0;
            $data->save();
            return redirect()->route('unfav_parameters.index')
                            ->with('success','Unfav Parameters Blocked successfully');
        }
    
        public function unblock($id)
        {
            $data = Fav_unfav_parameter::find($id);
            $data->is_active = 1;
            $data->save();
            return redirect()->route('unfav_parameters.index')
                            ->with('success','Unfav Parameters Unblock successfully');
        }
}
