<?php

namespace GeoIP\Provider;

class DbIpComProvider extends AbstractProvider
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
            'http://api.db-ip.com/v2/' . $this->config['plan'] . '/' . $ip);
        $result = array_replace_recursive([
            'city' => null,
            'stateProvCode' => null,
            'stateProv' => null,
            'countryCode' => null,
            'countryName' => null,
            'continentCode' => null,
            'continentName' => null,
            'zipCode' => null,
            'latitude' => null,
            'longitude' => null,
            'timeZone' => null,
            'asNumber' => null,
            'isp' => null,
        ], @json_decode($result->getBody()->getContents(), true));

        $timezone = $result['timeZone'] ?: ProviderPolyfill::countryCodeToTimezone($result['countryCode']);

        return [
            'ip' => $ip,
            'city' => $result['city'],
            'region' => $result['stateProv'],
            'region_code' => $result['stateProvCode'],
            'country' => $result['countryName'],
            'country_code' => $result['countryCode'],
            'continent' => $result['continentName'],
            'continent_code' => $result['continentCode'],
            'zip' => $result['zipCode'],
            'latitude' => $result['latitude'],
            'longitude' => $result['longitude'],
            'hostname' => gethostbyaddr($ip),
            'asn' => $result['asNumber'],
            'organization' => $result['isp'],
            'timezone' => $timezone,
            'utc_offset' => ProviderPolyfill::timezoneToTimezoneOffset($timezone)
        ];
    }
}