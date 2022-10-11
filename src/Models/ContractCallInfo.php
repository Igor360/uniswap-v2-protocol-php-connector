<?php

namespace Igor360\UniswapV2Connector\Models;

class ContractCallInfo
{
    public ?string $type;

    public ?string $function;

    public ?array $methods;

    public array $decodedArgs;

    public array $decodedLogs;
}
