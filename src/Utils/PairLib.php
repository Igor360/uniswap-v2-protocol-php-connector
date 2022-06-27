<?php

namespace Igor360\UniswapV2Connector\Utils;

use Igor360\UniswapV2Connector\Interfaces\IMath;

trait PairLib
{
    private string $FEE_DENOMINATOR = "9998"; // it for Uniswap Swap for, pancake swap set 9975

    private string $SLIPPAGE = "10"; // 10%

    /**
     * @return string
     */
    public function getFeeDenominator(): string
    {
        return $this->FEE_DENOMINATOR;
    }

    /**
     * @param string $value
     */
    public function setFeeDenominator(string $value): void
    {
        $this->FEE_DENOMINATOR = $value;
    }

    public function getSlippage(): string
    {
        return $this->SLIPPAGE;
    }

    public function setSlippage(string $value): void
    {
        if ((int)$value <= 100 && (int)$value >= 0) {
            $this->SLIPPAGE = $value;
        }
    }

    public function getAmountOut($amountIn, $reserveIn, $reserveOut, IMath $math)
    {
        $amountInWithFee = bcmul($amountIn, $this->FEE_DENOMINATOR, $math->scale());
        $numerator = bcmul($amountInWithFee, $reserveOut, $math->scale());
        $denominator = bcadd(bcmul($reserveIn, "10000"), $amountInWithFee, $math->scale());
        return bcdiv($numerator, $denominator, $math->scale());
    }


    public function getAmountIn($amountOut, $reserveIn, $reserveOut, IMath $math)
    {
        $numerator = bcmul(bcmul($reserveIn, $amountOut, $math->scale()), "10000", $math->scale());
        $denominator = bcmul(bcsub($reserveOut, $amountOut, $math->scale()), $this->FEE_DENOMINATOR, $math->scale());
        return bcadd(bcdiv($numerator, $denominator), "1", $math->scale());
    }

    public function quote($amountA, $reserveA, $reserveB, IMath $math)
    {
        return bcdiv(bcmul($amountA, $reserveB, $math->scale()), $reserveA, $math->scale());
    }

    public function amountWithSlippage(string $amount, IMath $math): string
    {
        //min_tokens = int(amount_out * (1 - (slippage / 100)))
        return bcmul($amount, bcsub("1", bcdiv($this->SLIPPAGE, "100", $math->scale()), $math->scale()), $math->scale());
    }
}
