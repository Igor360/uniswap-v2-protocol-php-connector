<?php declare(strict_types=1);

namespace Igor360\UniswapV2Connector\Services;

use Igor360\UniswapV2Connector\Exceptions\ContractException;
use Igor360\UniswapV2Connector\Exceptions\TransactionException;
use Igor360\UniswapV2Connector\Interfaces\ConnectionInterface;
use Igor360\UniswapV2Connector\Interfaces\TransactionDecodeInterface;
use Igor360\UniswapV2Connector\Interfaces\TransactionTokenInterface;
use Igor360\UniswapV2Connector\Models\ContractCallInfo;
use Igor360\UniswapV2Connector\Utils\ClassUtils;
use Illuminate\Support\Arr;

class TransactionTokenService extends TransactionService implements TransactionDecodeInterface
{
    use ClassUtils;

    private TokenService $tokenService;

    private bool $validated = false;

    /**
     * @param TokenService $tokenService
     * @throws TransactionException
     */
    public function __construct(?string $transactionHash, ConnectionInterface $credentials)
    {
        parent::__construct($transactionHash, $credentials);
        $this->isContract();
        $this->tokenService = new TokenService($this->transactionInfo->to ?? null, $credentials);
        $this->validate();
        $this->decode();
    }

    public function updateServices(): self
    {
        $this->tokenService->setContractAddress($this->transactionInfo->to);
        return $this;
    }

    public function getTokenService(): TokenService
    {
        return $this->tokenService;
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
        return TransactionTokenInterface::class;
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
            throw new ContractException("It's not token transaction");
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
        return $this->tokenService->getEventsTopics();
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
            $decodedLogs[] = $this->tokenService->getContract()->decodeContractTransactionLogs($topicId, $log);
        }
        $this->transactionInfo->callInfo->decodedLogs = $decodedLogs;
    }

    public function decodeTransactionArgs(): void
    {
        $methodId = $this->getContractFunctionId();
        $methods = $this->tokenService->getContract()->getMethodSelectors();
        if (!Arr::has($methods, $methodId)) {
            throw new TransactionException("Invalid method, method with selector ${methodId} not located in abi");
        }
        $functionName = $methods[$methodId] ?? null;
        $this->transactionInfo->callInfo->function = $functionName;
        $this->transactionInfo->callInfo->decodedArgs = $this->tokenService->getContract()->decodeContractTransactionArgs($functionName, $this->transactionInfo->data);
    }
}
