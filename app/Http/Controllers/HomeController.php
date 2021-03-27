<?php

namespace App\Http\Controllers;

use App\Services\Interfaces\CurlServiceInterface;
use App\Services\StatisticService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * @var StatisticService
     */
    private $statisticService;

    public function __construct(StatisticService $statisticService)
    {
        $this->statisticService = $statisticService;
    }

    public function index()
    {
        $last_block = $this->statisticService->getStatistics(1);
        $cart_data = $this->statisticService->getChartData();

        return view('home.index', ['last_block' => $last_block[0], 'chart_data' => json_encode($cart_data)]);
    }

    public function update()
    {
        $this->statisticService->checking();
        return redirect()->route('home.index');
    }
}
