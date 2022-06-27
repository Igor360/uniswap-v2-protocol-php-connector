<?php declare(strict_types=1);

namespace Igor360\UniswapV2Connector\Services;

use Igor360\UniswapV2Connector\Exceptions\ContractABIException;
use Igor360\UniswapV2Connector\Services\DataTypes\ASCII;
use Igor360\UniswapV2Connector\Services\DataTypes\Integers;
use Igor360\UniswapV2Connector\Services\DataTypes\Keccak;
use Igor360\UniswapV2Connector\Services\DataTypes\Method;
use Igor360\UniswapV2Connector\Services\DataTypes\MethodParam;

// TODO: REFACTOR THIS BAD CODE
/**
 * @source https://github.com/digitaldonkey/ethereum-php
 */
class ABIService
{
    /** @var Method|null */
    private ?Method $constructor = null;
    /** @var Method|null */
    private ?Method $fallback = null;
    /** @var Method|null */
    private ?Method $receive = null;
    /** @var array */
    private array $functions;
    /** @var array */
    private array $events;
    /** @var bool */
    private bool $strictMode;

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
                        // Todo: parse events
                        break;
                    default:
                        throw new ContractABIException(
                            sprintf('Bad/Unexpected value for ABI block param "type" at index %d', $index)
                        );
                }
            } catch (ContractABIException $e) {
//                dd($index, $block);
                // Trigger an error instead of throwing exception if a block within ABI fails,
                // to make sure rest of ABI blocks will work
                trigger_error(sprintf('[%s] %s', get_class($e), $e->getMessage()));
            }

            $index++;
        }
    }

    /**
     * @param string $name
     * @param array|null $args
     * @return string
     * @throws ContractABIException
     * @throws \Exception
     */
    public function encodeCall(string $name, ?array $args): string
    {
        $method = $this->functions[$name] ?? null;
        if (!$method instanceof Method) {
            throw new ContractABIException(sprintf('Call method "%s" is undefined in ABI', $name));
        }

        $givenArgs = $args;
        $givenArgsCount = is_array($givenArgs) ? count($givenArgs) : 0;
        $methodParams = $method->inputs;
        $methodParamsCount = is_array($methodParams) ? count($methodParams) : 0;

        // Strict mode
        if ($this->strictMode) {
            // Params/args count must match
            if ($methodParamsCount || $givenArgsCount) {
                if ($methodParamsCount !== $givenArgsCount) {
                    throw new ContractABIException(
                        sprintf('Method "%s" requires %d args, given %d', $name, $methodParamsCount, $givenArgsCount)
                    );
                }
            }
        }

        $encoded = "";
        $methodParamsTypes = [];
        for ($i = 0; $i < $methodParamsCount; $i++) {
            $param = $methodParams[$i];
            $arg = $givenArgs[$i];
            $encoded .= $this->encodeArg($param->type, $arg);
            $methodParamsTypes[] = $param->type;
        }


        $encodedMethodCall = Keccak::hash(sprintf('%s(%s)', $method->name, implode(",", $methodParamsTypes)), 256);
        return '0x' . substr($encodedMethodCall, 0, 8) . $encoded;
    }

    /**
     * @param string $type
     * @param $value
     * @return string
     * @throws ContractABIException
     */
    public function encodeArg(string $type, $value): string
    {
        $len = preg_replace('/[^0-9]/', '', $type);
        if (!$len) {
            $len = null;
        }

        // Handle array types

        if ($type === "address[]") {
            $hex = null;
            foreach ($value as $address) {
                $hex .= str_pad(substr($address, 2), 64, "0", STR_PAD_LEFT);
            }
            $countElements = count($value);
            $offset = 64;
            $offsetHex = substr(str_pad(Integers::Pack_UInt_BE($offset), 64, "0", STR_PAD_LEFT), 0, 64);
            $countElementsHex = substr(str_pad(Integers::Pack_UInt_BE($countElements), 64, "0", STR_PAD_LEFT), 0, 64);

            return $offsetHex . $countElementsHex . $hex;
        }
        $type = preg_replace('/[^a-z]/', '', $type);
        switch ($type) {
            case "hash":
            case "address":
                if (substr($value, 0, 2) === "0x") {
                    $value = substr($value, 2);
                }
                break;
            case "uint":
            case "int":
                $value = Integers::Pack_UInt_BE($value);
                break;
            case "bool":
                $value = $value === true ? 1 : 0;
                break;
            case "string":
                $value = ASCII::base16Encode($value);
                break;
            default:
                throw new ContractABIException(sprintf('Cannot encode value of type "%s"', $type));
        }

        return substr(str_pad((string)$value, 64, "0", STR_PAD_LEFT), 0, 64);
    }

    /**
     * @param string $name
     * @param string $encoded
     * @return array
     * @throws ContractABIException
     */
    public function decodeResponse(string $name, string $encoded): array
    {
        $method = $this->functions[$name] ?? null;
        if (!$method instanceof Method) {
            throw new ContractABIException(sprintf('Call method "%s" is undefined in ABI', $name));
        }

        // Remove suffix "0x"
        if (substr($encoded, 0, 2) === '0x') {
            $encoded = substr($encoded, 2);
        }

        // Output params
        $methodResponseParams = $method->outputs ?? [];
        $methodResponseParamsCount = count($methodResponseParams);

        // What to expect
        if ($methodResponseParamsCount <= 0) {
            return [];
        } elseif ($methodResponseParamsCount === 1) {
            // Put all in a single chunk
            $chunks = [$encoded];
        } else {
            // Split in chunks of 64 bytes
            $chunks = str_split($encoded, 64);
        }

        $result = []; // Prepare
        for ($i = 0; $i < $methodResponseParamsCount; $i++) {
            /** @var MethodParam $param */
            $param = $methodResponseParams[$i];
            $chunk = $chunks[$i];
            $decoded = $this->decodeArg($param->type, $chunk);

            if ($param->name) {
                $result[$param->name] = $decoded;
            } else {
                $result[] = $decoded;
            }
        }

        return $result;
    }

    /**
     * @param string $type
     * @param string $encoded
     * @throws ContractABIException
     */
    public function decodeArg(string $type, string $encoded, string $method = '')
    {

        if (str_contains($type, "[]")) {
            return $this->decodeArrayValue($type, $encoded);
        }

        $len = preg_replace('/[^0-9]/', '', $type);
        if (!$len) {
            $len = null;
        }
        $type = preg_replace('/[^a-z]/', '', $type);

        if ($type === "address") {
            return $this->decodeAddressResponse($encoded);
        }
        switch ($type) {
            case "hash":
            case "uint":
            case "int":
                return Integers::Unpack($encoded);
            case "bool":
                $encoded = ltrim($encoded, "0");
                return boolval($encoded);
            case "string":
                $encoded = ltrim($encoded, "0");
                return ASCII::base16Decode($encoded);
            case "tuple":
            default:
                throw new ContractABIException(sprintf('Cannot encode value of type "%s"', $type));
        }
    }


    public function decodeAddressResponse(string $hex): string
    {
        if (strpos($hex, "0x") === 0) {
            $hex = substr($hex, 2);
        }

        if (strlen($hex) !== 64) {
            return $hex;
        }
        return '0x' . substr($hex, 24); // 64 - address length
    }

    public function decodeArrayValue(string $type, string $encoded, string $method = ''): array
    {
        $decodedVars = [];
        $chunks = str_split($encoded, 64);
        $type = preg_replace('/[^a-z]/', '', $type);
        if (count($chunks) >= 2) {
            $size = hexdec($chunks[1]);
            array_splice($chunks, 0, $size);
            while ($size > 0) {
                $encoded = ltrim($chunks[$size - 1], "0");
                switch ($type) {
                    case "hash":
                    case "uint":
                    case "int":
                        $decodedVars[] = Integers::Unpack($encoded);
                        break;
                    case "bool":
                        $decodedVars[] = boolval($encoded);
                        break;
                    case "string":
                        $decodedVars[] = ASCII::base16Decode($encoded);
                        break;
                    case "tuple":
                    default:
                        throw new ContractABIException(sprintf('Cannot encode value of type "%s"', $type));
                }
                $size--;
            }
            $decodedVars = array_reverse($decodedVars);
        }
        return $decodedVars;
    }
}
