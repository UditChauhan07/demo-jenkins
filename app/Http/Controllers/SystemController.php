<?php

namespace App\Http\Controllers;

use App\Models\Alphasystem_type;
use App\Models\Module_type;
use Illuminate\Http\Request;

class SystemController extends Controller
{

    public function index()
    {
        $palpha = Alphasystem_type::where('systemtype_id', 1)->get();
        $calpha = Alphasystem_type::where('systemtype_id', 2)->get();
        return view('systems.index', compact('palpha', 'calpha'));
    }

}
