<?php

namespace App\Services;

use App\Jobs\AddBlockToDB;
use App\Jobs\UpdateBlocks;
use App\Models\Statistic;
use App\Services\Interfaces\CurlServiceInterface;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class StatisticService
{
    private $count;
    /**
     * @var CurlServiceInterface
     */
    private $curlService;
    /**
     * @var Statistic
     */
    private $statistic;

    public function __construct(CurlServiceInterface $curlService, Statistic $statistic)
    {
        $this->curlService = $curlService;
        $this->statistic = $statistic;
        $this->count = config('blockchain.limit_count');
    }

    public function getStatistics(int $limit)
    {
        return DB::select(
            "SELECT main_tb.height,
                (SELECT AVG(avg_tb.fee) from statistics as avg_tb where avg_tb.height <= main_tb.height order by avg_tb.height limit :lim1) as avg,
                (SELECT MIN(min_tb.fee) from statistics as min_tb where min_tb.height <= main_tb.height order by min_tb.height limit :lim2) as min,
                (SELECT MAX(max_tb.fee) from statistics as max_tb where max_tb.height <= main_tb.height order by max_tb.height limit :lim3) as max
                from statistics as main_tb
                order by main_tb.height desc
                limit :limit",
            ['lim1' => $this->count, 'lim2' => $this->count, 'lim3' => $this->count, 'limit' => $limit]
        );
    }

    public function getChartData()
    {
        $results = $this->getStatistics(config('blockchain.limit_count'));
        $chart_data[] = ['Height', 'AVG', 'MIN', 'MAX'];
        for ($i = count($results) - 1; $i >= 0; $i--) {
            $chart_data[] = [$results[$i]->height, (int)ceil($results[$i]->avg), (int)$results[$i]->min, (int)$results[$i]->max];
        }

        return $chart_data;
    }

    /**
     *
     */
    public function checking()
    {
        if ($this->shouldUpdate()) {
            dispatch(new UpdateBlocks());
        }
    }

    /**
     * @return bool
     */
    public function shouldUpdate()
    {
        $statistic = $this->statistic->orderby('height', 'desc')->first();
        if (!$statistic) {
            return true;
        }

        return !$this->isLastBlock($statistic->height);
    }

    public function updateData()
    {
        $lastBlockFromTable = $this->statistic->orderBy('height', 'desc')->take($this->count)->get();
        $lastBlockFromTable = $lastBlockFromTable->map(function ($block) {
            return $block->height;
        });

        $day = Carbon::now();
        $flag = true;
        $j = 0;
        while($flag && $j < $this->count)
        {
            $timestamp = $day->timestamp.$day->milli;
            $blocks = $this->curlService->getBlocksByDay($timestamp);
            for ($i = count($blocks) - 1; $i >= 0; $i--) {
                if ($lastBlockFromTable->contains($blocks[$i]->height)) {
                    $flag = false;
                    break;
                }

                dispatch(new AddBlockToDB($blocks[$i]->height));
                $j++;
                if ($j >= $this->count) {
                    break;
                }
            }
            $day->subDay();
        }
    }

    /**
     * @param int $height
     * @return mixed
     */
    public function addBlock(int $height)
    {
        $block = $this->curlService->getBlock($height);
        return $this->create([
            "height" => $height,
            "fee" => $block->fee
        ]);
    }

    /**
     * @param array $param
     * @return mixed
     */
    public function create(array $param)
    {
        return $this->statistic->create($param);
    }

    /**
     * @param int $height
     * @return bool
     */
    private function isLastBlock(int $height)
    {
        $lastBlock = $this->curlService->getLastBlock();

        return $lastBlock->height === $height;
    }
}
