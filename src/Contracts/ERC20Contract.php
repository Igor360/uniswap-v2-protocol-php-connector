<?php declare(strict_types=1);

namespace Igor360\UniswapV2Connector\Contracts;

use Igor360\UniswapV2Connector\Services\ContractService;
use Illuminate\Support\Arr;

class ERC20Contract extends ContractService
{
    function abi(): array
    {
        return json_decode(config("uniswap-v2-connector.erc20ABI"), true, 512, JSON_THROW_ON_ERROR);
    }

    public function balanceOf(string $contractAddress, string $address)/*: string*/
    {
        $this->validateAddress($contractAddress, $address);
        $res = $this->callContractFunction($contractAddress, "balanceOf", [$address]);
        return sprintf("%0.0f", hexdec($res));
    }

    public function getBalance(string $address): string
    {
        $this->validateAddress($address);
        $balance = $this->jsonRPC('eth_getBalance', null, [$address, "latest"]);
        return (string)hexdec(Arr::get($balance, 'result'));
    }

    public function balanceOfString(string $contractAddress, string $address): string
    {
        $this->validateAddress($contractAddress, $address);
        $res = $this->callContractFunction($contractAddress, "balanceOf", [$address]);
        return sprintf("%0.0f", hexdec($res));
    }

    public function totalSupply(string $contractAddress): string
    {
        $this->validateAddress($contractAddress);
        $res = $this->callContractFunction($contractAddress, "totalSupply");
        return sprintf("%0.0f", hexdec($res));
    }

    public function decimals(string $contractAddress): string
    {
        $this->validateAddress($contractAddress);
        $res = $this->callContractFunction($contractAddress, "decimals");
        return sprintf("%0.0f", hexdec($res));
    }

    public function name(string $contractAddress): string
    {
        $this->validateAddress($contractAddress);
        $res = $this->callContractFunction($contractAddress, "name");
        return $this->ABIService->decodeArg("string", $res);
    }

    public function symbol(string $contractAddress): string
    {
        $this->validateAddress($contractAddress);
        $res = $this->callContractFunction($contractAddress, "symbol");
        return $this->ABIService->decodeArg("string", $res);
    }

    public function encodeTransfer(string $to, int $amount): string
    {
        return $this->ABIService->encodeCall('transfer', [$to, $amount]);
    }

    public function encodeTransferFrom(string $from, string $to, int $amount): string
    {
        return $this->ABIService->encodeCall('transferFrom', [$from, $to, $amount]);
    }
}
