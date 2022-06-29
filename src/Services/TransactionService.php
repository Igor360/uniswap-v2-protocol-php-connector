<?php declare(strict_types=1);

namespace Igor360\UniswapV2Connector\Services;

use Igor360\UniswapV2Connector\Exceptions\TransactionException;
use Igor360\UniswapV2Connector\Interfaces\ConnectionInterface;
use Igor360\UniswapV2Connector\Interfaces\ITransactions;
use Igor360\UniswapV2Connector\Models\Transaction;
use Illuminate\Support\Arr;

class TransactionService implements ITransactions
{
    private string $transactionAddress;

    private EthereumService $rpc;

    protected Transaction $transactionInfo;

    /**
     * @param string $transactionAddress
     * @param ConnectionInterface $credentials
     */
    public function __construct(string $transactionAddress, ConnectionInterface $credentials)
    {
        $this->transactionAddress = $transactionAddress;
        $this->rpc = new EthereumService($credentials);
        $this->transactionInfo = new Transaction();
        $this->loadTransaction();
    }

    public function loadTransaction(): self
    {
        $transaction = $this->rpc->getTransactionByHash($this->transactionAddress);
        $this->transactionInfo->hash = Arr::get($transaction, 'hash');
        $this->transactionInfo->from = Arr::get($transaction, 'from');
        $this->transactionInfo->to = Arr::get($transaction, 'to');
        $this->transactionInfo->value = (string)hexdec(Arr::get($transaction, 'value'));
        $this->transactionInfo->data = Arr::get($transaction, 'input');
        $this->transactionInfo->gas = (string)hexdec(Arr::get($transaction, 'gas'));
        $this->transactionInfo->gasPrice = (string)hexdec(Arr::get($transaction, 'gasPrice'));
        $this->transactionInfo->block = (string)hexdec(Arr::get($transaction, 'blockNumber'));
        $this->transactionInfo->blockHash = (string)Arr::get($transaction, 'blockHash');
        $this->transactionInfo->nonce = (string)hexdec(Arr::get($transaction, 'nonce'));
        $this->transactionInfo->r = Arr::get($transaction, 'r');
        $this->transactionInfo->s = Arr::get($transaction, 's');
        $this->transactionInfo->v = Arr::get($transaction, 'v');
        return $this;
    }

    public function loadLogs(): self
    {
        $transaction = $this->rpc->getTransactionReceipt($this->transactionAddress);
        $this->transactionInfo->status = Arr::get($transaction, "status") === "0x01";
        $this->transactionInfo->logsBloom = Arr::get($transaction, "logsBloom");
        $this->transactionInfo->cumulativeGasUsed = Arr::get($transaction, "cumulativeGasUsed");
        $this->transactionInfo->gasUsed = (string)hexdec(Arr::get($transaction, "gasUsed"));
        $this->transactionInfo->logs = Arr::get($transaction, "logs");
        $this->transactionInfo->transactionIndex = Arr::get($transaction, "transactionIndex");
        $this->transactionInfo->type = Arr::get($transaction, "type");
        $this->transactionInfo->contractAddress = Arr::get($transaction, "contractAddress");
        return $this;
    }

    /**
     * @return Transaction
     */
    public function getTransactionInfo(): Transaction
    {
        return $this->transactionInfo;
    }

    /**
     * @return string
     * @throws \JsonException
     */
    public function getTransactionInfoJson(): string
    {
        return json_encode($this->transactionInfo, JSON_THROW_ON_ERROR);
    }

    protected function isContract(): void
    {
        if (is_null($this->transactionInfo->data)) {
            throw new TransactionException("It's not contract transaction");
        }
    }

}
