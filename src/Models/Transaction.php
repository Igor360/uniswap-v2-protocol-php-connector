<?php

namespace Igor360\UniswapV2Connector\Models;

class Transaction
{
    public string $hash;

    public ?bool $status;

    public string $from;

    public string $to;

    public string $value;

    public string $block;

    public string $blockHash;

    public string $nonce;

    public string $data;

    public string $gas;

    public string $gasPrice;

    public ?string $gasUsed;

    public ?string $cumulativeGasUsed;

    public ?string $contractAddress;

    public ?string $type;

    public ?string $transactionIndex;

    public string $r;

    public string $s;

    public string $v;

    public ?string $logsBloom;

    public ?array $logs;

    public ?ContractCallInfo $callInfo;

    public ?string $rejectReason;

    public ?array $trace;

    public ?string $coin;

    public ?string $token;
}

