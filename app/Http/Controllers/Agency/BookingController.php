<?php

namespace App\Http\Controllers\Agency;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    public function create()
    {
        return view('agency.bookings.create');
    }

    public function index(){
        return view('agency.bookings.index');
    }
}
