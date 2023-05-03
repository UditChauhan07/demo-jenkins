<?php

namespace App\Http\Controllers;

use App\Models\Fav_unfav_parameter;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Gem;
use App\Models\Life_cycle;
use App\Models\Module_description;
use App\Models\Partner_relationship;
use App\Models\Personal_parameter;
use Rap2hpoutre\FastExcel\FastExcel;
use Illuminate\Http\Request;

class ImportController extends Controller
{
    public function index()
    {
        $tables = "Userlist Gem";
        $tables = explode(" ", $tables);
        return view('import', compact('tables'));
    }
    // import file
    public function fileImport(Request $request)
    {
        $files = $request->file;
        (new FastExcel)->import($files, function ($line) {
            Personal_parameter::insert($line);
        });
         return redirect('import')->with('success', 'Done');

    }
}
