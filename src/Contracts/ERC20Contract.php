<?php declare(strict_types=1);

namespace Igor360\UniswapV2Connector\Contracts;

use Igor360\UniswapV2Connector\Configs\ConfigFacade as Config;
use Igor360\UniswapV2Connector\Exceptions\GethException;
use Igor360\UniswapV2Connector\Services\ContractService;
use Illuminate\Support\Arr;

class ERC20Contract extends ContractService
{
    function abi(): array
    {
        return json_decode(Config::get("uniswap-v2-connector.erc20ABI"), true, 512, JSON_THROW_ON_ERROR);
    }

    public function balanceOf(string $contractAddress, string $address)/*: string*/
    {
        $this->validateAddress($contractAddress, $address);
        $res = $this->callContractFunction($contractAddress, "balanceOf", [$address]);
        return sprintf("%0.0f", hexdec(Arr::first($res)));
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
        return Arr::first($res);
    }

    public function totalSupply(string $contractAddress): string
    {
        $this->validateAddress($contractAddress);
        $res = $this->callContractFunction($contractAddress, "totalSupply");
        return Arr::first($res);
    }

    public function decimals(string $contractAddress): string
    {
        $this->validateAddress($contractAddress);
        $res = $this->callContractFunction($contractAddress, "decimals");
        return sprintf("%0.0f", Arr::first($res));
    }

    public function name(string $contractAddress): string
    {
        $this->validateAddress($contractAddress);
        return Arr::first($this->callContractFunction($contractAddress, "name"));
    }

    public function owner(string $contractAddress): ?string
    {
        $this->validateAddress($contractAddress);
        try {
            return Arr::first($this->callContractFunction($contractAddress, "owner"));
        } catch (GethException $exception) {
            return null;
        }
    }

    public function symbol(string $contractAddress): string
    {
        $this->validateAddress($contractAddress);
        return Arr::first($this->callContractFunction($contractAddress, "symbol"));
    }

    public function allowance(string $contractAddress, string $owner, string $spender): ?string
    {
        $this->validateAddress($contractAddress, $owner, $spender);
        return Arr::first($this->callContractFunction($contractAddress, "allowance", [$owner, $spender]));
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
