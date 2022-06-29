<?php declare(strict_types=1);

namespace Igor360\UniswapV2Connector\Interfaces;

/**
 * @description Contains method ids for token transfer actions
 */
interface TransactionTokenInterface
{
    //approve(address,uint256)
    public const APPROVE_TOKEN_METHOD_ID = "0x095ea7b3";

    //transfer(address,uint256)
    public const TRANSFER_TOKEN_METHOD_ID = "0xa9059cbb";

    //transferFrom(address,address,uint256)
    public const TRANSFER_FROM_TOKEN_METHOD_ID = "0x23b872dd";
}
