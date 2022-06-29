<?php declare(strict_types=1);

namespace Igor360\UniswapV2Connector\Configs;

use Igor360\UniswapV2Connector\Interfaces\ConfigInterface;
use Illuminate\Support\Arr;

abstract class Config implements ConfigInterface
{
    public static function get(string $key, $default = null)
    {
        try {
            return \Illuminate\Support\Facades\Config::get($key, $default);
        } catch (\RuntimeException $exception) {
            $BASE_CONFIGS = self::toArray();
            return Arr::get($BASE_CONFIGS, str_replace(self::BASE_KEY . ".", "", $key), $default);
        }
    }

    public static function loadRouterABI(): string
    {
        return trim(file_get_contents(__DIR__ . '/../../config/abis/uniswap_router.json'));
    }

    public static function loadPairABI(): string
    {
        return trim(file_get_contents(__DIR__ . '/../../config/abis/uniswap_pair.json'));
    }

    public static function loadFactoryABI(): string
    {
        return trim(file_get_contents(__DIR__ . '/../../config/abis/uniswap_factory.json'));
    }

    public static function loadERC20ABI(): string
    {
        return trim(file_get_contents(__DIR__ . '/../../config/abis/erc20.json'));
    }

    public static function loadERC721ABI(): string
    {
        return trim(file_get_contents(__DIR__ . '/../../config/abis/erc721.json'));
    }

    public static function loadBaseABI(): array
    {
        return [
            "routerABI" => self::loadRouterABI(),
            "factoryABI" => self::loadFactoryABI(),
            "pairABI" => self::loadPairABI(),
            "erc20ABI" => self::loadERC20ABI(),
            "erc721ABI" => self::loadERC721ABI(),
        ];
    }

    public static function toArray(): array
    {
        return array_merge(
            self::loadBaseABI(),
            [
                "eth" => [
                    "host" => "",
                    "port" => "",
                    "ssh" => false
                ]
            ]);
    }
}
