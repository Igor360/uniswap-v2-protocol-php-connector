<?php

namespace Igor360\UniswapV2Connector\Models;

class ContractCallInfo
{
    public ?string $type;

    public ?string $function;

    public ?string $functionWithParams;

    public ?array $functionDetails;

    public array $decodedArgs;

    public array $decodedLogs;
}
