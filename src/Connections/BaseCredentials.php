<?php declare(strict_types=1);

namespace Igor360\UniswapV2Connector\Connections;

use Igor360\UniswapV2Connector\Interfaces\ConnectionInterface;

class BaseCredentials implements ConnectionInterface
{
    protected string $host;

    protected ?int $port;

    protected bool $ssl;

    protected ?string $path;


    /**
     * @param string $host
     * @param int|null $port
     * @param bool $ssl
     */
    public function __construct(string $host, ?int $port = null, bool $ssl = false, $path = "")
    {
        $this->host = $host;
        $this->port = $port;
        $this->ssl = $ssl;
        $this->path = $path;

    }

    public function host(): string
    {
        return $this->host;
    }

    public function port(): ?int
    {
        return $this->port;
    }

    /**
     * Set ssl connection
     * @return bool
     */
    public function ssl(): bool
    {
        return (bool)$this->ssl;
    }

    public function path(): string
    {
        return $this->path;
    }

    /**
     * @param string|null $params
     */
    public function setPath(?string $path): void
    {
        $this->path = $path;
    }


}
