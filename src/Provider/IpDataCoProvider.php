<?php

namespace GeoIP\Provider;

class IpDataCoProvider extends AbstractProvider
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
        $result = $client->request('GET', 'https://api.ipdata.co/' . $ip, [
            'query' => ['api-key' => $this->config['api_key']]
        ]);

        $result = array_replace_recursive([
            'city' => null,
            'region' => null,
            'region_code' => null,
            'country_name' => null,
            'country_code' => null,
            'continent_name' => null,
            'continent_code' => null,
            'postal' => null,
            'latitude' => null,
            'longitude' => null,
            'asn' => null,
            'organisation' => null,
            'time_zone' => ['name' => null]
        ], @json_decode($result->getBody()->getContents(), true));

        return [
            'ip' => $ip,
            'city' => $result['city'],
            'region' => $result['region'],
            'region_code' => $result['region_code'],
            'country' => $result['country_name'],
            'country_code' => $result['country_code'],
            'continent' => $result['continent_name'],
            'continent_code' => $result['continent_code'],
            'zip' => $result['postal'],
            'latitude' => $result['latitude'],
            'longitude' => $result['longitude'],
            'hostname' => gethostbyaddr($ip),
            'asn' => $result['asn'],
            'organization' => $result['organisation'],
            'timezone' => $result['time_zone']['name'],
            'utc_offset' => ProviderPolyfill::timezoneToTimezoneOffset($result['time_zone']['name'])
        ];
    }
}