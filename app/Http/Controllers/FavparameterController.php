<?php

namespace App\Http\Controllers;

use App\Models\Fav_unfav_parameter;
use Illuminate\Http\Request;

class FavparameterController extends Controller
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
            return view('fav_parameters.index', compact('favjan','favfeb','favmar','favapr','favmay','favjun',
                        'favjul','favaug','favsep','favoct','favnev','favdec'));
        }
    
        public function show($id)
        {
            $parameter = Fav_unfav_parameter::find($id);
            return view('fav_parameters.show',compact('parameter'));
        }
    
        public function edit($id)
        {
            $parameter = Fav_unfav_parameter::find($id);
            return view('fav_parameters.edit',compact('parameter'));
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
            return redirect()->route('fav_parameters.index')
                            ->with('success','Fav Parameters updated successfully');
        }
    
        public function destroy($id)
        {
            $data = Fav_unfav_parameter::find($id);
            $data->is_active = 0;
            $data->save();
            return redirect()->route('fav_parameters.index')
                            ->with('success','Fav Parameters Blocked successfully');
        }
    
        public function unblock($id)
        {
            $data = Fav_unfav_parameter::find($id);
            $data->is_active = 1;
            $data->save();
            return redirect()->route('fav_parameters.index')
                            ->with('success','Fav Parameters Unblock successfully');
        }
}
