<?php declare(strict_types=1);

namespace Igor360\UniswapV2Connector\Connections;

use Igor360\UniswapV2Connector\Interfaces\ConnectionInterface;

class ConfigCredentials implements ConnectionInterface
{
    public function host(): string
    {
        return config("blockchain.eth.host");
    }

    public function port(): ?int
    {
        return config("blockchain.eth.port");
    }

    /**
     * Set ssl connection
     * @return bool
     */
    public function ssl(): bool
    {
        return (bool)config('blockchain.eth.ssl');
    }
}
