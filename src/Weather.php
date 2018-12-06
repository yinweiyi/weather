<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/12/6 0006
 * Time: 16:49
 */

namespace Wayee\Weather;

use GuzzleHttp\Client;
use Wayee\Weather\Exceptions\HttpException;
use Wayee\Weather\Exceptions\InvalidArgumentException;

class Weather
{

    protected $key;

    protected $guzzleOptions = [];

    public function __construct($key)
    {
        $this->key = $key;
    }

    /**
     * 获取httpClient
     *
     * @return Client
     */
    public function getHttpClient()
    {
        return new Client($this->guzzleOptions);
    }

    /**
     * 设置guzzle 实例的参数
     *
     * @param array $options
     */
    public function setGuzzleOptions(array $options)
    {
        $this->guzzleOptions = $options;
    }

    /**
     * @param string $city             城市名/高德地址位置 adcode，比如：“深圳” 或者（adcode：440300）
     * @param string $type      返回内容类型：base: 返回实况天气 / all:返回预报天气
     * @param string $format    输出的数据格式，默认为 json 格式
     * @return mixed|string
     * @throws HttpException
     * @throws InvalidArgumentException
     */
    public function getWeather($city, string $type = 'base', string $format = 'json')
    {
        $url = 'https://restapi.amap.com/v3/weather/weatherInfo';

        $format = strtolower($format);
        $type = strtolower($type);
        if (!in_array($format, ['json', 'xml'])) {
            throw new InvalidArgumentException('Invalid response format: '.$format);
        }

        if (!in_array($type, ['base', 'all'])) {
            throw new InvalidArgumentException('Invalid response format: '.$format);
        }

        $query = array_filter([
            'key' => $this->key,
            'city' => $city,
            'output' => $format,
            'extensions' => $type,
        ]);
        try{
            $response = $this->getHttpClient()->get($url, ['query' => $query])->getBody()->getContents();
            return 'json' === $format ? \json_decode($response, true) : $response;
        } catch (\Exception $e){
            throw new HttpException($e->getMessage(), $e->getCode(), $e);
        }
    }
}