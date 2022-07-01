<?php declare(strict_types=1);

namespace Igor360\UniswapV2Connector\Services;

use Igor360\UniswapV2Connector\Exceptions\ContractException;
use Igor360\UniswapV2Connector\Exceptions\TransactionException;
use Igor360\UniswapV2Connector\Interfaces\ConnectionInterface;
use Igor360\UniswapV2Connector\Interfaces\TransactionDecodeInterface;
use Igor360\UniswapV2Connector\Interfaces\TransactionSwapInterface;
use Igor360\UniswapV2Connector\Models\ContractCallInfo;
use Igor360\UniswapV2Connector\Utils\ClassUtils;
use Illuminate\Support\Arr;

class TransactionSwapService extends TransactionService implements TransactionDecodeInterface
{
    use ClassUtils;

    private ?string $transactionHash;

    private UniswapRouteService $uniswapRouteService;

    private UniswapPairService $pairService;

    private UniswapFactoryService $factoryService;

    private bool $validated = false;

    /**
     * @param string|null $transactionHash
     */
    public function __construct(?string $transactionHash, ConnectionInterface $credentials)
    {
        parent::__construct($transactionHash, $credentials);
        $this->isContract();
        $this->uniswapRouteService = new UniswapRouteService($this->transactionInfo->to ?? null, $credentials);
        $this->pairService = new UniswapPairService(null, $credentials);
        $this->factoryService = new UniswapFactoryService(null, $credentials);
        $this->validate();
        $this->decode();
    }

    public function updateServices(): self
    {
        $this->uniswapRouteService->setContractAddress($this->transactionInfo->to);
        return $this;
    }

    /**
     * @return UniswapRouteService
     */
    public function getUniswapRouteService(): UniswapRouteService
    {
        return $this->uniswapRouteService;
    }

    /**
     * @return string|null
     */
    public function getTransactionHash(): ?string
    {
        return $this->transactionHash;
    }

    /**
     * @param string|null $transactionHash
     * @return TransactionTokenService
     */
    public function setTransactionHash(?string $transactionHash): self
    {
        $this->transactionAddress = $transactionHash;
        $this->loadTransactionInstance();
        $this->updateServices();
        return $this;
    }

    public function constantStorageClass(): string
    {
        return TransactionSwapInterface::class;
    }

    public function getContractFunctionId(): string
    {
        return substr($this->transactionInfo->data, 0, 10);
    }

    public function validate(): void
    {
        $this->checkSettingTxHash();
        $this->isContract();
        $this->validated = true;
        $txId = $this->getContractFunctionId();
        $txIds = array_values($this->getConstants());
        if (!in_array($txId, $txIds, true)) {
            throw new ContractException("It's not swap transaction");
        }
    }

    public function isValidated(): bool
    {
        return $this->validated;
    }

    public function getMethodTypes(): array
    {
        return array_keys($this->getConstants());
    }

    public function getMethodType(): ?string
    {
        return Arr::first(array_keys(Arr::where($this->getConstants(), fn($value, $key) => $value === $this->getContractFunctionId())));
    }

    public function decode(): void
    {
        if (!$this->isValidated()) {
            throw new TransactionException("Contract transaction is not validated");
        }
        $this->transactionInfo->callInfo = new ContractCallInfo();
        $type = $this->getMethodType();
        $this->transactionInfo->callInfo->type = is_null($type) ? $type : str_replace("_METHOD_ID", "", $type);
        $this->decodeTransactionArgs();
        $this->decodeTransactionLogs();
    }

    public function getEventsTopics(): array
    {
        return $this->uniswapRouteService->getEventsTopics();
    }

    public function decodeTransactionLogs(): void
    {
        $decodedLogs = [];
        $logs = $this->transactionInfo->logs ?? [];
        foreach ($logs as $log) {
            $topicId = $log["topics"][0] ?? null;
            if (is_null($topicId)) {
                continue;
            }
            $decoded = $this->uniswapRouteService->getContract()->decodeContractTransactionLogs($topicId, $log);
            $decodedForPairs = $this->pairService->getContract()->decodeContractTransactionLogs($topicId, $log);
            $decodedForFactory = $this->factoryService->getContract()->decodeContractTransactionLogs($topicId, $log);
            if (!is_null($decoded)) {
                $decodedLogs [] = $decoded;
                continue;
            }
            if (!is_null($decodedForPairs)) {
                $decodedLogs [] = $decodedForPairs;
                continue;
            }
            if (!is_null($decodedForFactory)) {
                $decodedLogs [] = $decodedForFactory;
            }
        }
        $this->transactionInfo->callInfo->decodedLogs = $decodedLogs;
    }

    public function decodeTransactionArgs(): void
    {
        $methodId = $this->getContractFunctionId();
        $methods = $this->uniswapRouteService->getContract()->getMethodSelectors();
        if (!Arr::has($methods, $methodId)) {
            throw new TransactionException("Invalid method, method with selector ${methodId} not located in abi");
        }
        $functionName = $methods[$methodId] ?? null;
        $this->transactionInfo->callInfo->function = $functionName;
        $this->transactionInfo->callInfo->decodedArgs = $this->uniswapRouteService->getContract()->decodeContractTransactionArgs($functionName, $this->transactionInfo->data);
    }

}
