<?php

namespace GeoIP\Provider;

use GeoIP\IPResult;

use GuzzleHttp\Client;

abstract class AbstractProvider
{

    /**
     * @var array
     */
    protected $config;

    /**
     * AbstractProvider constructor.
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        $this->config = $config;
    }

    /**
     * @param $ip
     * @param bool $v6
     * @return array
     */
    abstract protected function getInformationFromProvider($ip, $v6 = false);

    /**
     * @param $ip
     * @param bool $v6
     * @return IPResult
     */
    public function getInformationForIp($ip, $v6 = false)
    {
        $information = $this->getInformationFromProvider($ip, $v6);
        $ipResultClass = $this->config['ip_result_class'];
        return new $ipResultClass($information);
    }
    protected function getHttpClient()
    {
        return new Client();
    }
}