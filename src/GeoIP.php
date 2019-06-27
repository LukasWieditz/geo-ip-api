<?php

namespace GeoIP;

use GeoIP\Provider\AbstractProvider;
use GeoIP\Provider\DbIpComProvider;
use GeoIP\Provider\IpApiComProvider;
use GeoIP\Provider\IpApiCoProvider;
use GeoIP\Provider\IpDataCoProvider;
use GeoIP\Provider\IpInfoIoProvider;
use GeoIP\Provider\IpStackComProvider;

class GeoIP
{
    const MINUTELY = 0x001;
    const DAILY = 0x002;
    const MONTHLY = 0x003;

    protected $config;

    protected $providerClassCache = [];

    public function __construct(array $config)
    {
        $config = array_replace_recursive([
            'services' => [
                'ip-api.com' => [
                    'priority' => 100,
                    'request_limit' => 150,
                    'request_reset' => self::MINUTELY,
                    'ipv6' => true,
                    'provider_class' => IpApiComProvider::class
                ],
                'ipapi.co' => [
                    'priority' => 200,
                    'request_limit' => 1000,
                    'request_reset' => self::DAILY,
                    'ipv6' => true,
                    'provider_class' => IpApiCoProvider::class
                ],
                'ipdata.co' => [
                    'priority' => 300,
                    'request_limit' => 1500,
                    'request_reset' => self::DAILY,
                    'ipv6' => true,
                    'provider_class' => IpDataCoProvider::class
                ],

                'ipinfo.io' => [
                    'priority' => 2000,
                    'request_limit' => 1000,
                    'request_reset' => self::DAILY,
                    'ipv6' => true,
                    'provider_class' => IpInfoIoProvider::class
                ],
                'ipstack.com' => [
                    'priority' => 3000,
                    'request_limit' => 10000,
                    'request_reset' => self::MONTHLY,
                    'ipv6' => true,
                    'provider_class' => IpStackComProvider::class
                ],
                'db-ip.com' => [
                    'priority' => 4000,
                    'request_limit' => 1000,
                    'request_reset' => self::DAILY,
                    'ipv6' => true,
                    'provider_class' => DbIpComProvider::class,
                    'plan' => 'free'
                ],
            ],
            'settings' => [
                'ip_result_class' => IPResult::class
            ]
        ], $config);

        $config['services'] = array_filter($config['services'], function ($service) {
            return isset($service['api_key']);
        });

        usort($config['services'], function ($a, $b) {
            return $a['priority'] <= $b['priority'];
        });

        $this->config = $config;
    }

    /**
     * @param $providerId
     * @param $requestLimit
     * @param $requestReset
     * @return int
     */
    protected function getRemainingQuota($providerId, $requestLimit, $requestReset)
    {
        // TODO
        return 1000;
    }

    /**
     * @param bool $v6
     * @return AbstractProvider
     * @throws \Exception
     */
    protected function getProvider($v6 = false)
    {
        $providerList = $this->config['services'];

        if ($v6) {
            $providerList = array_filter($providerList, function ($provider) {
                return $provider['ipv6'];
            });
        }

        foreach ($providerList as $providerId => $provider) {
            if ($this->getRemainingQuota($providerId, $provider['request_limit'], $provider['request_reset']) >= 25) {
                if (!isset($this->providerClassCache[$providerId])) {
                    $class = $provider['provider_class'];
                    $this->providerClassCache[$providerId] = new $class(array_merge($provider, $this->config['settings']));
                }

                return $this->providerClassCache[$providerId];
            }
        }

        throw new \Exception('No quota remaining on available providers');
    }

    /**
     * @param $ip
     * @return IPResult
     * @throws \Exception
     */
    public function getIpInfo($ip)
    {
        if (!filter_var($ip, FILTER_VALIDATE_IP)) {
            throw new \Exception('Provided IP is no valid IPv4 or IPv6');
        }

        $v6 = false;
        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
            $v6 = true;
        }

        $provider = $this->getProvider($v6);

        return $provider->getInformationForIp($ip, $v6);
    }
}