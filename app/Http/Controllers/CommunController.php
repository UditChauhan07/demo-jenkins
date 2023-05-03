<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Module_description;
use Illuminate\Support\Facades\DB;

class CommunController extends Controller
{
    public function index()
    {
        return view('communs.create');
    }

     public function store(Request $request)
     {
         $moduletype_id = 17;
         $number = $request->numbers;
         $description = $request->description;
         $data=array("moduletype_id"=>$moduletype_id, "number"=>$number, "description"=>$description);
         DB::table('module_descriptions')->insert($data);
         return redirect()->back()
         ->with('success','Data Inserted successfully');
     }
}
