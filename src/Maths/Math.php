<?php

namespace Igor360\UniswapV2Connector\Maths;

use Igor360\UniswapV2Connector\Interfaces\IMath;

class Math implements IMath
{
    public function scale(): int
    {
        return 18;
    }
}
