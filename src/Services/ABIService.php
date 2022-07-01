<?php declare(strict_types=1);

namespace Igor360\UniswapV2Connector\Services;

use Igor360\UniswapV2Connector\Exceptions\ContractABIException;
use Igor360\UniswapV2Connector\Services\DataTypes\Event;
use Igor360\UniswapV2Connector\Services\DataTypes\Method;

// TODO: REFACTOR THIS BAD CODE

/**
 * @source https://github.com/digitaldonkey/ethereum-php
 */
final class ABIService extends ABIEncryptService
{
    /** @var Method|null */
    private ?Method $constructor = null;
    /** @var Method|null *
    private ?Method $fallback = null;
    /** @var Method|null */
    private ?Method $receive = null;

    /**
     * ABI constructor.
     * @param array $abi
     */
    public function __construct(array $abi)
    {
        $this->strictMode = true;
        $this->functions = [];
        $this->events = [];

        $index = 0;
        foreach ($abi as $block) {
            try {
                if (!is_array($block)) {
                    throw new ContractABIException(
                        sprintf(
                            'Unexpected data type "%s" at ABI array index %d, expecting Array',
                            gettype($block),
                            $index
                        )
                    );
                }

                $type = $block["type"] ?? null;
                switch ($type) {
                    case "constructor":
                    case "function":
                    case "receive":
                    case "fallback":
                        $method = new Method($block);
                        switch ($method->type) {
                            case "constructor":
                                $this->constructor = $method;
                                break;
                            case "function":
                                $this->functions[$method->name] = $method;
                                break;
                            case "fallback":
                                $this->fallback = $method;
                                break;
                            case "receive":
                                $this->receive = $method;
                                break;
                        }
                        break;
                    case "event":
                        $event = new Event($block);
                        $this->events[$event->getTopic()] = $event;
                        break;
                    default:
                        throw new ContractABIException(
                            sprintf('Bad/Unexpected value for ABI block param "type" at index %d', $index)
                        );
                }
            } catch (ContractABIException $e) {
                // Trigger an error instead of throwing exception if a block within ABI fails,
                // to make sure rest of ABI blocks will work
                trigger_error(sprintf('[%s] %s', get_class($e), $e->getMessage()));
            }

            $index++;
        }
    }

    public function getEventsTopics(): array
    {
        $topics = [];
        foreach ($this->events as $event) {
            $topics[$event->getSignature()] = $event->getTopic();
        }
        return $topics;
    }
}
