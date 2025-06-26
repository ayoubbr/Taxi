<?php

namespace App\Http\Controllers\agency;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ReportsController extends Controller
{
    public function index()
    {
        return view('agency.reports.index');
    }
}
