<?php

namespace Tests\Feature;

use App\Models\Statistic;
use App\Services\Interfaces\CurlServiceInterface;
use App\Services\StatisticService;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class StatisticServiceTest extends TestCase
{
    use DatabaseMigrations;
    /**
     * @test
     */
    public function should_getTrue_ifTableIsEmpty()
    {
        $curlService = $this->getMockBuilder(CurlServiceInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $statisticService = new StatisticService($curlService, new Statistic());

        $this->assertTrue($statisticService->shouldUpdate());
    }

    /**
     * @test
     */
    public function should_getTrue_ifIsNotLastBlock()
    {
        factory(Statistic::class)->create(['height' => 222]);

        $ixudraCurlService = $this->getMockBuilder(CurlServiceInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $ixudraCurlService->expects($this->once())->method('getLastBlock')->willReturn((object)['height' => 111]);

        $statisticService = new StatisticService($ixudraCurlService, new Statistic());

        $this->assertTrue($statisticService->shouldUpdate());
    }

    /**
     * @test
     */
    public function should_getFalse_ifIsLastBlock()
    {
        factory(Statistic::class)->create(['height' => 123]);

        $ixudraCurlService = $this->getMockBuilder(CurlServiceInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $ixudraCurlService->expects($this->once())
            ->method('getLastBlock')
            ->willReturn((object)['height' => 123]);

        $statisticService = new StatisticService($ixudraCurlService, new Statistic());

        $this->assertFalse($statisticService->shouldUpdate());
    }

    /**
     * @test
     */
    public function should_createRow_whenAddBlock()
    {
        $data = [
            "height" => 123,
            "fee" => 222
        ];

        $ixudraCurlService = $this->getMockBuilder(CurlServiceInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $ixudraCurlService->expects($this->once())
            ->method('getBlock')
            ->willReturn((object)$data);

        $statisticService = new StatisticService($ixudraCurlService, new Statistic());
        $statisticService->addBlock($data['height']);

        $statistic = Statistic::first();

        $this->assertEquals($data['height'], $statistic->height);
        $this->assertEquals($data['fee'], $statistic->fee);
    }

}
