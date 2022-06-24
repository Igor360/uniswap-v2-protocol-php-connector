<?php declare(strict_types=1);

namespace Igor360\UniswapV2Connector\Services;

use Igor360\UniswapV2Connector\Interfaces\ConnectionInterface;

/**
 * @class ContractService
 * @description Base contract service
 */
abstract class ContractService extends EthereumService
{

    protected ABIService $ABIService;

    abstract function abi(): array;

    public function __construct(ConnectionInterface $credentials)
    {
        parent::__construct($credentials);
        $this->ABIService = new ABIService($this->abi());
    }

    public function callContractFunction(
        string $contractAddress,
        string $functionName,
        array  $params = [],
               $block = "latest"
    ): string
    {
        $tx = new \stdClass();
        $tx->to = $contractAddress;
        $tx->data = $this->ABIService->encodeCall($functionName, $params);

        return $this->ethCall($tx, is_int($block) ? '0x' . dechex($block) : $block, $functionName);
    }
}
