<?php


namespace App\Services;


use App\Exceptions\BadRequestApiException;
use App\Services\Interfaces\CurlServiceInterface;
use Illuminate\Support\Facades\Log;
use Ixudra\Curl\CurlService;

class IxudraCurlService implements CurlServiceInterface
{
    /**
     * @var CurlService
     */
    private $curlService;

    public function __construct(CurlService $curlService)
    {
        $this->curlService = $curlService;
    }

    /**
     * @param int $dayInTimestampFormat
     * @return mixed
     * @throws BadRequestApiException
     */
    public function getBlocksByDay(int $dayInTimestampFormat)
    {
        $response = $this->curlService->to("https://blockchain.info/blocks/$dayInTimestampFormat?format=json")
            ->get();

        $response = json_decode($response);

        if (!isset($response->blocks)) {
            Log::error(json_encode($response));
            throw new BadRequestApiException("Bad request");
        }

        return $response->blocks;
    }

    /**
     * @return mixed
     * @throws BadRequestApiException
     */
    public function getLastBlock()
    {
        $response = $this->curlService->to("https://blockchain.info/latestblock")
            ->get();

        $response = json_decode($response);

        if (!isset($response->height)) {
            Log::error(json_encode($response));
            throw new BadRequestApiException("Bad request");
        }

        return $response;
    }

    public function getBlock(int $height)
    {
        $response = $this->curlService->to("https://blockchain.info/block-height/$height?format=json")
            ->get();

        $response = json_decode($response);

        if (!isset($response->blocks[0]->fee)) {
            Log::error(json_encode($response));
            throw new BadRequestApiException("Bad request");
        }

        return $response->blocks[0];
    }
}
