<?php

namespace App\Http\Controllers;

use App\Models\User;
use Barryvdh\DomPDF\Facade\PDF;
use Illuminate\Http\Request;

class PdfController extends Controller
{
    function __construct()
    {
         $this->middleware('permission:invoice-list|invoice-create|invoice-edit|invoice-delete', ['only' => ['index','store']]);
         $this->middleware('permission:invoice-create', ['only' => ['create','store']]);
         $this->middleware('permission:invoice-edit', ['only' => ['edit','update']]);
         $this->middleware('permission:invoice-delete', ['only' => ['destroy']]);
    }

    public function index()
    {
        $users = User::all();
        return view('invoice', compact('users'));
    }

    /**
     * Export content to PDF with View
     *
     * @return void
     */
    public function downloadPdf($id)
    {
        $users = User::find($id);
        // share data to view
        
        $pdf = PDF::loadView('users-pdf', ['users' => $users]);
        return $pdf->download('users.pdf');
    }
}
