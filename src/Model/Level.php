<?php

namespace ShopfrontSDK\Model;

class Level
{
    const LEVEL_1 = '1';
    const LEVEL_2 = '2';
    const LEVEL_3 = '3';
    const LEVEL_4 = '4';
    const LEVEL_5 = '5';

    public string $level;

    public function __construct(string $level)
    {
        $this->level = $level;
    }
}
