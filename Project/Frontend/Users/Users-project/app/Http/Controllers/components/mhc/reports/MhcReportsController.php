<?php

namespace App\Http\Controllers\components\mhc\reports;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class MhcReportsController extends Controller
{
    
    public function index(Request $request)
    {
        $headerData = "Health Risk Assessment Reports";
        return view('content.components.mhc.reports.health-risk-reports', ['HeaderData' => $headerData]);
    } 
    public function graphBasedonFilter(Request $request)
    {
        $headerData = "Health Risk Assessment Reports";
        return view('content.components.mhc.reports.health-risk-reports-filter', ['HeaderData' => $headerData]);
    }

    
}