<?php

namespace Tests\Unit;

use App\Exceptions\BadRequestApiException;
use App\Services\IxudraCurlService;
use Carbon\Carbon;
use Ixudra\Curl\Builder;
use Ixudra\Curl\CurlService;
use Tests\TestCase;

class IxudraCurlServiceTest extends TestCase
{
    /**
     * @test
     */
    public function should_getBlocks()
    {
        $curlService = $this->getMockBuilder(CurlService::class)
            ->disableOriginalConstructor()
            ->getMock();

        $builder = $this->getMockBuilder(Builder::class)
            ->disableOriginalConstructor()
            ->getMock();

        $curlService->expects($this->once())->method('to')->willReturn($builder);
        $builder->expects($this->once())->method('get')->willReturn(json_encode(['blocks' => [1,2,3]]));

        $service = new IxudraCurlService($curlService);
        $response = $service->getBlocksByDay(Carbon::now()->timestamp);
        $this->assertEquals(3, count($response));
    }

    /**
     * @test
     */
    public function should_throwBadRequestException_forGetBlocks()
    {
        $curlService = $this->getMockBuilder(CurlService::class)
            ->disableOriginalConstructor()
            ->getMock();

        $builder = $this->getMockBuilder(Builder::class)
            ->disableOriginalConstructor()
            ->getMock();

        $curlService->expects($this->once())->method('to')->willReturn($builder);
        $builder->expects($this->once())->method('get')->willReturn(json_encode([]));
        $this->expectException(BadRequestApiException::class);
        $service = new IxudraCurlService($curlService);
        $service->getBlocksByDay(000);
    }

    /**
     * @test
     */
    public function should_workRight_forgettingLastBlocks()
    {
        $curlService = $this->getMockBuilder(CurlService::class)
            ->disableOriginalConstructor()
            ->getMock();

        $builder = $this->getMockBuilder(Builder::class)
            ->disableOriginalConstructor()
            ->getMock();

        $curlService->expects($this->once())->method('to')->willReturn($builder);
        $builder->expects($this->once())->method('get')->willReturn(json_encode(['height' => 123]));

        $service = new IxudraCurlService($curlService);
        $response = $service->getLastBlock();
        $this->assertEquals(123, $response->height);
    }

    /**
     * @test
     */
    public function should_getBlock()
    {
        $curlService = $this->getMockBuilder(CurlService::class)
            ->disableOriginalConstructor()
            ->getMock();

        $builder = $this->getMockBuilder(Builder::class)
            ->disableOriginalConstructor()
            ->getMock();

        $curlService->expects($this->once())->method('to')->willReturn($builder);
        $builder->expects($this->once())->method('get')->willReturn(json_encode([
            'blocks' => [
                0 => ["fee" => 1]
            ]
        ]));

        $service = new IxudraCurlService($curlService);
        $response = $service->getBlock(123);
        $this->assertEquals(1, $response->fee);
    }

    /**
     * @test
     */
    public function should_throwBadRequestException_forGetBlock()
    {
        $curlService = $this->getMockBuilder(CurlService::class)
            ->disableOriginalConstructor()
            ->getMock();

        $builder = $this->getMockBuilder(Builder::class)
            ->disableOriginalConstructor()
            ->getMock();

        $curlService->expects($this->once())->method('to')->willReturn($builder);
        $builder->expects($this->once())->method('get')->willReturn(json_encode(["asd"=>2]));

        $this->expectException(BadRequestApiException::class);

        $service = new IxudraCurlService($curlService);
        $service->getBlock(000);
    }
}
