<?php

namespace Igor360\UniswapV2Connector\Services\DataTypes;

class Event
{
    protected $abi;
    protected $anonymous;
    protected $inputs;
    protected $name;

    public function __construct($abiItem)
    {
        $this->abi = $abiItem;
        $this->name = $abiItem->name;
        $this->inputs = $abiItem->inputs;
        $this->anonymous = $abiItem->anonymous;
    }


}
