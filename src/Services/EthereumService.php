<?php declare(strict_types=1);

namespace Igor360\UniswapV2Connector\Services;

use Igor360\UniswapV2Connector\Connections\EthereumRPC;
use Igor360\UniswapV2Connector\Exceptions\InvalidAddressException;
use Illuminate\Support\Arr;

/**
 * Class EthereumService
 * @package Igor360\UniswapV2Connector\Services
 */
class EthereumService extends EthereumRPC
{

    /**
     * Base ethereum regex
     */
    public const ETHEREUM_ADDRESS = '/^0x[a-fA-F0-9]{40}$/';

    /**
     * @param mixed ...$addresses
     * @throws InvalidAddressException
     */
    public
    function validateAddress(
        ...$addresses
    ): void
    {
        foreach ($addresses as &$address) {
            if (!preg_match(self::ETHEREUM_ADDRESS, $address)) {
                throw new InvalidAddressException();
            }
        }
    }

    public function getBalance(string $address): string
    {
        $this->validateAddress($address);
        $balance = $this->jsonRPC('eth_getBalance', null, [$address, "latest"]);
        return (string)hexdec(Arr::get($balance, 'result'));
    }

    public function getTransactionByHash(string $hash): ?array
    {
        $res = $this->jsonRPC('eth_getTransactionByHash', null, [$hash]);
        return Arr::get($res, 'result');
    }

    public function getTxCountForAddress(string $address, string $quantity = "pending"): string
    {
        $this->validateAddress($address);
        $res = $this->jsonRPC('eth_getTransactionCount', null, [$address, $quantity]);
        return Arr::get($res, 'result');
    }

    public function getGethGasPrice(): string
    {
        $res = $this->jsonRPC('eth_gasPrice');
        return Arr::get($res, 'result');
    }

    public function getCurrentBlockNumber(): int
    {
        $res = $this->jsonRPC('eth_blockNumber');
        return hexdec(Arr::get($res, 'result'));
    }

    public function getBlockTransactions(int $blockNumber, bool $onlyHashes = false): array
    {
        $res = $this->jsonRPC('eth_getBlockByNumber', null, ['0x' . dechex($blockNumber), !$onlyHashes]);
        return Arr::get($res, 'result.transactions');
    }

    public function getBlockTransactionsCountByNumber(int $blockNumber): int
    {
        $res = $this->jsonRPC('eth_getBlockTransactionCountByNumber', null, ['0x' . dechex($blockNumber)]);
        return hexdec(Arr::get($res, 'result'));
    }

    public function getTransactionReceipt(string $txHash): ?array
    {
        $res = $this->jsonRPC("eth_getTransactionReceipt", null, [$txHash]);
        return $res ? Arr::get($res, 'result') : [];
    }

    public function getTransactionTrace(string $txHash): ?array
    {
        $res = $this->jsonRPC("debug_traceTransaction", null, [$txHash]);
        return $res ? Arr::get($res, 'result') : [];
    }

    public function getBlockTrace(int $number): ?array
    {
        $res = $this->jsonRPC("debug_traceBlockByNumber", null, ["0x".dechex($number)]);
        return $res ? Arr::get($res, 'result') : [];
    }
}

