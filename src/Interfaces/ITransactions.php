<?php declare(strict_types=1);

namespace Igor360\UniswapV2Connector\Interfaces;

interface ITransactions
{
    // TOKEN

    //approve(address,uint256)
    public const APPROVE_TOKEN_METHOD_ID = "0x095ea7b3";

    //transfer(address,uint256)
    public const TRANSFER_TOKEN_METHOD_ID = "0xa9059cbb";

    //transferFrom(address,address,uint256)
    public const TRANSFER_FROM_TOKEN_METHOD_ID = "0x23b872dd";


    // SWAP Method ids - I knew it not correct way, but it's more simply to verify than debug crashed lib

    // createPair(address tokenA, address tokenB)
    public const CREATE_PAIR_METHOD_IO = "0xc9c65396";

    //swapExactTokensForETH(uint256 amountIn, uint256 amountOutMin, address[] path, address to, uint256 deadline)
    public const SWAP_EXACT_TOKENS_FOR_ETH_METHOD_ID = "0x18cbafe5";

    //swapExactETHForTokens(uint256 amountOutMin, address[] path, address to, uint256 deadline)
    public const SWAP_EXACT_ETH_FOR_TOKENS_METHOD_ID = "0x7ff36ab5";

    //removeLiquidityWithPermit(address tokenA, address tokenB, uint256 liquidity, uint256 amountAMin, uint256 amountBMin, address to, uint256 deadline, bool approveMax, uint8 v, bytes32 r, bytes32 s)
    public const REMOVE_LIQUIDITY_WITH_PERMIT_METHOD_ID = "0x2195995c";

    //swapExactTokensForTokensSupportingFeeOnTransferTokens(uint256 amountIn, uint256 amountOutMin, address[] path, address to, uint256 deadline)
    public const SWAP_EXACT_TOKENS_FOR_TOKENS_SUPPORTING_FEE_ON_TRANSFER_TOKENS_METHOD_ID = "0x5c11d795";

    //swapTokensForExactETH(uint256 amountOut, uint256 amountInMax, address[] path, address to, uint256 deadline)
    public const SWAP_TOKENS_FOR_EXACT_ETH_METHOD_ID = "0x4a25d94a";

    //swapTokensForExactTokens(uint256 amountOut, uint256 amountInMax, address[] path, address to, uint256 deadline)
    public const SWAP_TOKENS_FOR_EXACT_TOKENS_METHOD_ID = "0x8803dbee";

    //swapExactTokensForETHSupportingFeeOnTransferTokens(uint256 amountIn, uint256 amountOutMin, address[] path, address to, uint256 deadline)
    public const SWAP_EXACT_TOKENS_FOR_ETH_SUPPORTING_FEE_ON_TRANSFER_TOKENS_METHOD_ID = "0x791ac947";

    //swapExactTokensForTokens(uint256 amountIn, uint256 amountOutMin, address[] path, address to, uint256 deadline)
    public const SWAP_EXACT_TOKENS_FOR_TOKENS_METHOD_ID = "0x38ed1739";

    //addLiquidityETH(address token, uint256 amountTokenDesired, uint256 amountTokenMin, uint256 amountETHMin, address to, uint256 deadline)
    public const ADD_LIQUIDITY_ETH_METHOD_ID = "0xf305d719";

    //removeLiquidityETHWithPermitSupportingFeeOnTransferTokens(address token, uint256 liquidity, uint256 amountTokenMin, uint256 amountETHMin, address to, uint256 deadline, bool approveMax, uint8 v, bytes32 r, bytes32 s)
    public const REMOVE_LIQUIDITY_ETH_WITH_PERMIT_SUPPORTING_FEE_ON_TRANSFER_TOKENS_METHOD_ID = "0x5b0d5984";

    //addLiquidity(address tokenA, address tokenB, uint256 amountADesired, uint256 amountBDesired, uint256 amountAMin, uint256 amountBMin, address to, uint256 deadline)
    public const ADD_LIQUIDITY_METHOD_ID = "0xe8e33700";

    //swapETHForExactTokens(uint256 amountOut, address[] path, address to, uint256 deadline)
    public const SWAP_ETH_FOR_EXACT_TOKENS_METHOD_ID = "0xfb3bdb41";


}
