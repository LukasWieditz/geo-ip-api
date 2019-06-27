<?php

namespace GeoIP\Provider;

class IpInfoIoProvider extends AbstractProvider
{
    /**
     * @param $ip
     * @param bool $v6
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    protected function getInformationFromProvider($ip, $v6 = false)
    {
        $client = $this->getHttpClient();
        $result = $client->request('GET',
            'https://ipinfo.io/' . $ip, ['query' => ['token' => $this->config['api_key']]]);
        $result = array_replace_recursive([
            'city' => null,
            'region' => null,
            'country' => null,
            'postal' => null,
            'loc' => ',',
            'asn' => ['asn' => null, 'name' => null]
        ], @json_decode($result->getBody()->getContents(), true));

        list($lat, $lon) = explode(',', $result['loc']);
        list($cc, $cn) = ProviderPolyfill::countryToContinentAndContinentCode($result['country']);

        $timezone = ProviderPolyfill::countryCodeToTimezone($result['country']);

        return [
            'ip' => $ip,
            'city' => $result['city'],
            'region' => $result['region'],
            'region_code' => ProviderPolyfill::regionAndCountryCodeToRegionCode($result['region'], $result['country']),
            'country' => ProviderPolyfill::countryCodeToCountry($result['country']),
            'country_code' => $result['country'],
            'continent' => $cn,
            'continent_code' => $cc,
            'zip' => $result['postal'],
            'latitude' => $lat,
            'longitude' => $lon,
            'hostname' => $result['hostname'],
            'asn' => isset($result['asn']) ? $result['asn']['asn'] : null,
            'organization' => isset($result['asn']) ? $result['asn']['name'] : null,
            'timezone' => $timezone,
            'utc_offset' => ProviderPolyfill::timezoneToTimezoneOffset($timezone)
        ];
    }
}