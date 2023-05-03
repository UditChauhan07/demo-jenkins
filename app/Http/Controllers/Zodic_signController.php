<?php

namespace App\Http\Controllers;

use App\Models\Zodic_sign;
use Illuminate\Http\Request;

class Zodic_signController extends Controller
{
    function __construct()
    {
        $this->middleware('permission:zodic_sign-list|zodic_sign-create|zodic_sign-edit|zodic_sign-delete', ['only' => ['index','show']]);
        $this->middleware('permission:zodic_sign-create', ['only' => ['create','store']]);
        $this->middleware('permission:zodic_sign-edit', ['only' => ['edit','update']]);
        $this->middleware('permission:zodic_sign-delete', ['only' => ['destroy']]);
    }

    public function index()
    {
        $zodic_sign = Zodic_sign::all();
        return view('zodic_signs.index', compact('zodic_sign'));
    }

    public function show($id)
    {
        $zodic_sign = Zodic_sign::find($id);
        return view('zodic_signs.show',compact('zodic_sign'));
    }

    public function edit($id)
    {
        $zodic_sign = Zodic_sign::find($id);
        return view('zodic_signs.edit',compact('zodic_sign'));
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'zodic_sign' => 'required',
            'zodic_number' => 'required',
            'zodic_day' => 'required',
        ]);
        $input = $request->all();
        $zodic_sign = Zodic_sign::find($id);
        $zodic_sign->update($input);
        return redirect()->route('zodic_signs.index')
                        ->with('success','Zodiac Sign updated successfully');
    }

    public function destroy($id)
    {
        $data = Zodic_sign::find($id);
        $data->is_active = 0;
        $data->save();
        return redirect()->route('zodic_signs.index')
                        ->with('success','Zodiac Sign Blocked successfully');
    }

    public function unblock($id)
    {
        $data = Zodic_sign::find($id);
        $data->is_active = 1;
        $data->save();
        return redirect()->route('zodic_signs.index')
                        ->with('success','Zodiac Sign Unblock successfully');
    }
}
