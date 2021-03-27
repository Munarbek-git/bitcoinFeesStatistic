<?php

namespace App\Services\Interfaces;

interface CurlServiceInterface
{
    public function getBlocksByDay(int $dayInTimestampFormat);
    public function getLastBlock();
    public function getBlock(int $height);
}
