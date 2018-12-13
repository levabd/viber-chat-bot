<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use DB;

class StatisticController extends Controller
{

    public function index(Request $request)
    {
        return view('statistic', [
            'month_statistic' => monthStatistic(Carbon::now()),
            'next_month_statistic' => monthStatistic(Carbon::now()->addMonth()),
            'total_drug_statistic' => totalDrugStatistic()
        ]);
    }
    
}
