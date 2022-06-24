<?php declare(strict_types=1);

namespace Igor360\UniswapV2Connector\Interfaces;

/**
 * Class RPCInterface
 * @package App\Services\Debug\Net
 */
interface RPCInterface
{
    public function baseHeaders(): array;

    public function apiVersion(): ?string;

    public function sendNodeRequest($method = null, $data = null, $need_encode = false, $is_put = false): array;
}
