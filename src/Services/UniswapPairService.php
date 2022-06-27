<?php declare(strict_types=1);

namespace Igor360\UniswapV2Connector\Services;

use Igor360\UniswapV2Connector\Contracts\ContractsFactory;
use Igor360\UniswapV2Connector\Contracts\UniswapPair;
use Igor360\UniswapV2Connector\Interfaces\ConnectionInterface;
use Igor360\UniswapV2Connector\Interfaces\IMath;
use Igor360\UniswapV2Connector\Maths\MathFacade;
use Igor360\UniswapV2Connector\Models\Pair;
use Igor360\UniswapV2Connector\Models\Token;
use Igor360\UniswapV2Connector\Utils\DateTimeConvert;
use Igor360\UniswapV2Connector\Utils\PairLib;
use Illuminate\Support\Arr;

class UniswapPairService
{
    use DateTimeConvert;
    use PairLib;

    private ?string $contractAddress;

    private UniswapPair $contract;

    private Pair $pairInfo;

    private IMath $math;

    /**
     * @param string $contractAddress
     * @param UniswapPair $contract
     * @throws \Igor360\UniswapV2Connector\Exceptions\InvalidContractInstanceKeyException
     */
    public function __construct(?string $contractAddress, ConnectionInterface $credentials)
    {
        $this->math = MathFacade::getSourceInstance();
        $this->contractAddress = $contractAddress;
        $this->pairInfo = new Pair();
        $this->pairInfo->pairAddress = $contractAddress;
        $this->contract = ContractsFactory::make('pair', $credentials);

        if (!is_null($contractAddress)) {
            $this->loadPairInfo();
            if ($this->pairInfo->lpSymbol === "Cake-LP") {
                $this->setFeeDenominator("9975");
            }
        }
    }

    /**
     * @return string
     */
    public function getContractAddress(): string
    {
        return $this->contractAddress;
    }

    /**
     * @param string $contractAddress
     */
    public function setContractAddress(string $contractAddress): void
    {
        $this->contractAddress = $contractAddress;
        $this->pairInfo->pairAddress = $contractAddress;
    }

    /**
     * @return UniswapPair
     */
    public function getContract(): UniswapPair
    {
        return $this->contract;
    }

    /**
     * @return Pair
     */
    public function getPairInfo(): Pair
    {
        return $this->pairInfo;
    }

    /**
     * @throws \JsonException
     */
    public function getPairInfoJson(): string
    {
        return json_encode($this->pairInfo, JSON_THROW_ON_ERROR);
    }

    /***/


    public function loadPairInfo(): void
    {
        $this->loadLpInfo();
        $this->loadTokens();
        $this->loadReserves();
        $this->loadKLast();
        $this->loadPriceCumulative();
    }

    public function getToken0(): string
    {
        return Arr::first($this->contract->getToken0Address($this->contractAddress));
    }

    public function getToken1(): string
    {
        return Arr::first($this->contract->getToken1Address($this->contractAddress));
    }

    public function getReserves(): array
    {
        return $this->contract->getReserves($this->contractAddress);
    }

    public function loadTokens(): void
    {
        $this->pairInfo->token0 = $this->getToken0();
        $this->pairInfo->token1 = $this->getToken1();
    }

    public function loadLpInfo(): self
    {
        $this->pairInfo->lpName = $this->contract->name($this->contractAddress);
        $this->pairInfo->lpSymbol = $this->contract->symbol($this->contractAddress);
        $this->pairInfo->lpDecimals = (int)$this->contract->decimals($this->contractAddress);
        $this->pairInfo->lpSupply = $this->contract->totalSupply($this->contractAddress);
        return $this;
    }

    public function loadReserves(): self
    {
        $reserves = $this->getReserves();
        $this->pairInfo->reserve0 = Arr::get($reserves, "_reserve0", "0");
        $this->pairInfo->reserve1 = Arr::get($reserves, "_reserve1", "0");
        $this->pairInfo->blockTimestamp = (int)Arr::get($reserves, "_blockTimestampLast", 0);
        $this->pairInfo->blockTimestampDateTime = $this->toDateTimeZone($this->pairInfo->blockTimestamp);
        $this->pairInfo->hasLiquidity = (int)$this->pairInfo->reserve1 > 0 && (int)$this->pairInfo->reserve0 > 0 && (int)$this->pairInfo->lpSupply > 0;
        return $this;
    }

    public function loadKLast(): self
    {
        $this->pairInfo->kLast = $this->contract->getKLast($this->contractAddress);
        return $this;
    }

    public function loadPriceCumulative(): self
    {
        $this->pairInfo->price0CumulativeLast = $this->contract->getPrice0CumulativeLast($this->contractAddress);
        $this->pairInfo->price1CumulativeLast = $this->contract->getPrice1CumulativeLast($this->contractAddress);

        $this->pairInfo->priceToken0toToken1 = $this->getAmountOut("1", $this->pairInfo->reserve0, $this->pairInfo->reserve1, $this->math);
        $this->pairInfo->priceToken1toToken0 = $this->getAmountOut("1", $this->pairInfo->reserve1, $this->pairInfo->reserve0, $this->math);
        return $this;
    }

    public function loadToken0Info(): self
    {
        $this->pairInfo->token0Info = new Token();
        $this->pairInfo->token0Info->name = $this->contract->name($this->pairInfo->token0);
        $this->pairInfo->token0Info->symbol = $this->contract->symbol($this->pairInfo->token0);
        $this->pairInfo->token0Info->decimals = $this->contract->decimals($this->pairInfo->token0);
        $this->pairInfo->token0Info->totalSupply = $this->contract->totalSupply($this->pairInfo->token0);
        return $this;
    }

    public function loadToken1Info(): self
    {
        $this->pairInfo->token1Info = new Token();
        $this->pairInfo->token1Info->name = $this->contract->name($this->pairInfo->token1);
        $this->pairInfo->token1Info->symbol = $this->contract->symbol($this->pairInfo->token1);
        $this->pairInfo->token1Info->decimals = $this->contract->decimals($this->pairInfo->token1);
        $this->pairInfo->token1Info->totalSupply = $this->contract->totalSupply($this->pairInfo->token1);
        return $this;
    }

    public function getNonces(string $address)
    {
        $this->contract->validateAddress($address);

        return $this->contract->getNoncesByAddress($this->contractAddress, $address);
    }
}
