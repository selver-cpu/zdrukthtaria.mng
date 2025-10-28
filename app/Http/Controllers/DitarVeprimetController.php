<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DitarVeprimet;

class DitarVeprimetController extends Controller
{
    public function index()
    {
        $veprimet = DitarVeprimet::with('perdorues')->latest()->paginate(20);

        return view('ditar.index', compact('veprimet'));
    }
    //
}
