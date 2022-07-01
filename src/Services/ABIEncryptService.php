<?php

namespace Igor360\UniswapV2Connector\Services;

use Igor360\UniswapV2Connector\Exceptions\ContractABIException;
use Igor360\UniswapV2Connector\Services\DataTypes\ASCII;
use Igor360\UniswapV2Connector\Services\DataTypes\Event;
use Igor360\UniswapV2Connector\Services\DataTypes\Integers;
use Igor360\UniswapV2Connector\Services\DataTypes\Keccak;
use Igor360\UniswapV2Connector\Services\DataTypes\Method;
use Igor360\UniswapV2Connector\Services\DataTypes\MethodParam;
use InvalidArgumentException;

abstract class ABIEncryptService
{

    /** @var array */
    protected array $functions;

    protected bool $strictMode;

    /** @var array */
    protected array $events;

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

    public function decodeFunctionArgs(string $name, string $encoded): array
    {
        $method = $this->functions[$name] ?? null;
        if (!$method instanceof Method) {
            throw new ContractABIException(sprintf('Call method "%s" is undefined in ABI', $name));
        }

        $encoded = substr($encoded, 10); // delete function id from str

        // Output params
        $methodResponseParams = $method->inputs ?? [];
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
        $result = [];
        for ($i = 0; $i < $methodResponseParamsCount; $i++) {
            /** @var MethodParam $param */
            $param = $methodResponseParams[$i];
            $chunk = $chunks[$i];
            $type = $param->type;
            if (str_contains($type, "[]")) {
                $type = str_replace("[]", "", $type);
                $offset = (int)(hexdec($chunk) / 64);
                $arrayChunks = array_slice($chunks, $i + $offset + 1);
                $size = hexdec($arrayChunks[0]);
                array_shift($arrayChunks);
                $arrayDecoded = [];
                for ($j = 0; $j < $size; $j++) {
                    $arrayDecoded[] = $this->decodeArg($type, $arrayChunks[$j]);
                }
                $decoded = $arrayDecoded;
            } else {
                $decoded = $this->decodeArg($type, $chunk);
            }
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

    /**
     * @param string $name
     * @return string
     */
    public function generateMethodSelector(string $name): array
    {
        $method = $this->functions[$name] ?? null;
        $methodParams = $method->inputs;
        $methodParamsCount = is_array($methodParams) ? count($methodParams) : 0;
        $methodParamsTypes = [];
        for ($i = 0; $i < $methodParamsCount; $i++) {
            $param = $methodParams[$i];
            $methodParamsTypes[] = $param->type;
        }
        $function = sprintf('%s(%s)', $method->name, implode(",", $methodParamsTypes));
        $encodedMethodCall = Keccak::hash($function, 256);
        $hash = '0x' . substr($encodedMethodCall, 0, 8);
        return compact('function', 'hash');
    }

    public function generateMethodSelectors(): array
    {
        $functionsSelectors = [];
        foreach (array_keys($this->functions) as $functionName) {
            ['hash' => $hash] = $this->generateMethodSelector($functionName);
            $functionsSelectors[$hash] = $functionName;
        }
        return $functionsSelectors;
    }

    public function decodeEventParams(string $name, array $encodedEventLog)
    {
        $res = [];
        $event = $this->events[$name] ?? null;
        if (!$event instanceof Event) {
            return; // skip if event not exist
//            throw new ContractABIException(sprintf('Call event "%s" is undefined in ABI', $name));
        }
        $res["Event"] = $event->getName();
        $topics = $encodedEventLog['topics'] ?? false;
        if (!is_array($topics) && !is_null($topics)) {
            throw new ContractABIException('Unexpected value for param "topics"');
        }
        $topicsParams = $event->getTopicInputs() ?? [];
        $topics = array_splice($topics, 1); //count topics without event name
        $topicsCount = count($topics);
        for ($i = 0; $i < $topicsCount; $i++) {
            $res[$topicsParams[$i]["name"]] = $this->decodeArg($topicsParams[$i]["type"], $topics[$i]);
        }

        $dataEncoded = $encodedEventLog['data'] ?? false;

        // params
        $dataResponseParams = $event->getDataInputs() ?? [];
        $dataResponseParamsCount = count($dataResponseParams);

        if ($dataResponseParamsCount === 1) {
            // Put all in a single chunk
            $chunks = [$dataEncoded];
        } else {
            // Split in chunks of 64 bytes
            $chunks = str_split($dataEncoded, 64);
        }

        for ($i = 0; $i < $dataResponseParamsCount; $i++) {
            $param = $dataResponseParams[$i];
            $chunk = $chunks[$i];
            $decoded = $this->decodeArg($param['type'] ?? null, $chunk);

            if ($param['name'] ?? false) {
                $res[$param['name']] = $decoded;
            } else {
                $res[] = $decoded;
            }
        }

        return $res;
    }


    public function nestedTypes($name)
    {
        if (!is_string($name)) {
            throw new InvalidArgumentException('nestedTypes name must string.');
        }
        $matches = [];

        if (preg_match_all('/([0-9]*)/', $name, $matches, PREG_PATTERN_ORDER) >= 1) {
            return $matches[0];
        }
        return false;
    }

    public function staticPartLength($name)
    {
        $nestedTypes = $this->nestedTypes($name);

        if ($nestedTypes === false) {
            $nestedTypes = ['[1]'];
        }
        $count = 32;

        foreach ($nestedTypes as $type) {
            $num = mb_substr($type, 1, 1);

            if (!is_numeric($num)) {
                $num = 1;
            } else {
                $num = intval($num);
            }
            $count *= $num;
        }

        return $count;
    }

    public function isDynamicType(string $type): bool
    {
        return preg_match('/^(bytes(\[([0-9]*)\])*?|string(\[([0-9]*)\])*?)$/', $type);
    }
}
