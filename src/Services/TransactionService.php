<?php declare(strict_types=1);

namespace Igor360\UniswapV2Connector\Services;

use Igor360\UniswapV2Connector\Contracts\ERC20Contract;
use Igor360\UniswapV2Connector\Exceptions\TransactionException;
use Igor360\UniswapV2Connector\Interfaces\ConnectionInterface;
use Igor360\UniswapV2Connector\Models\Transaction;
use Illuminate\Support\Arr;

class TransactionService
{
    protected ?string $transactionAddress;

    private EthereumService $rpc;

    protected Transaction $transactionInfo;

    protected ConnectionInterface $credentials;

    /**
     * @param string $transactionAddress
     * @param ConnectionInterface $credentials
     */
    public function __construct(?string $transactionAddress, ConnectionInterface $credentials)
    {
        $this->credentials = $credentials;
        $this->transactionAddress = $transactionAddress;
        $this->rpc = new EthereumService($credentials);
        $this->transactionInfo = new Transaction();
        if (!is_null($transactionAddress)) {
            $this->loadTransactionInstance();
        }
    }

    public function loadTransactionInstance(): self
    {
        return $this
            ->loadTransaction()
            ->loadLogs()
            ->loadCoin();
    }

    public function loadTransaction(): self
    {
        $transaction = $this->rpc->getTransactionByHash($this->transactionAddress);
        if (is_null($transaction)) {
            return $this;
        }
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
        $this->transactionInfo->status = (bool)hexdec(Arr::get($transaction, "status", "0x0"));
        $this->transactionInfo->logsBloom = Arr::get($transaction, "logsBloom");
        $this->transactionInfo->cumulativeGasUsed = (string)hexdec(Arr::get($transaction, "cumulativeGasUsed") ?? "");
        $this->transactionInfo->gasUsed = (string)hexdec(Arr::get($transaction, "gasUsed") ?? "");
        $this->transactionInfo->logs = Arr::get($transaction, "logs");
        $this->transactionInfo->transactionIndex = (string)hexdec(Arr::get($transaction, "transactionIndex") ?? "");
        $this->transactionInfo->type = Arr::get($transaction, "type");
        $this->transactionInfo->contractAddress = Arr::get($transaction, "contractAddress");
        $this->transactionInfo->token = Arr::get($transaction, "token", "0x0000000000000000000000000000000000000000");
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
        if (!is_null($this->transactionAddress) && is_null($this->transactionInfo->data ?? null)) {
            throw new TransactionException("It's not contract transaction");
        }
    }

    protected function checkSettingTxHash(): void
    {
        if (is_null($this->transactionAddress)) {
            throw new TransactionException("Hash is not set");
        }
    }

    /**
     * @return EthereumService
     */
    public function getRpc(): EthereumService
    {
        return $this->rpc;
    }

    public function loadCoin(): self
    {
        $id = $this->rpc->getChainId();
        switch ($id) {
            case 4286:
                $coin = 'GATO';
                break;
            case 137:
                $coin = 'MATIC';
                break;
            case 97:
            case 56:
                $coin = 'BNB';
                break;
            default:
                $coin = 'ETH';
                break;
        }
        if ($this->transactionInfo->token && $this->transactionInfo->token !== "0x0000000000000000000000000000000000000000") {
            $erc20Service = new ERC20Contract($this->credentials);
            $coin = $erc20Service->symbol($this->transactionInfo->token);
        }

        $this->transactionInfo->coin = $coin;
        return $this;
    }
}
