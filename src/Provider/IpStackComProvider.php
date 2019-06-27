<?php

namespace GeoIP\Provider;

class IpStackComProvider extends AbstractProvider
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
            'http://api.ipstack.com/' . $ip, [
                'query' => [
                    'access_key' => $this->config['api_key'],
                    'output' => 'json',
                    'fields' => 'ip,city,region_name,region_code,country_name,country_code,continent_name,continent_code,zip,latitude,longitude,hostname,connection.asn,time_zone.id',
                    'hostname' => 1
                ]
            ]);

        $result = array_replace_recursive([
            'city' => null,
            'location' => ['capital' => null],
            'region_name' => null,
            'region_code' => null,
            'country_name' => null,
            'country_code' => null,
            'continent_name' => null,
            'continent_code' => null,
            'latitude' => null,
            'longitude' => null,
            'zip' => null,
            'hostname' => null,
            'connection' => ['asn' => null],
            'time_zone' => ['id' => null]
        ], @json_decode($result->getBody()->getContents(), true));

        $timezone = isset($result['time_zone']) ? $result['time_zone']['id'] : ProviderPolyfill::countryCodeToTimezone($result['country_code']);

        return [
            'ip' => $ip,
            'city' => $result['city'] ?: (isset($result['location']) ? $result['location']['capital'] : null),
            'region' => $result['region_name'],
            'region_code' => $result['region_code'],
            'country' => $result['country_name'],
            'country_code' => $result['country_code'],
            'continent' => $result['continent_name'],
            'continent_code' => $result['continent_code'],
            'zip' => $result['zip'],
            'latitude' => $result['latitude'],
            'longitude' => $result['longitude'],
            'hostname' => $result['hostname'] ?: gethostbyaddr($ip),
            'asn' => isset($result['connection']) ? $result['connection']['asn'] : null,
            'organization' => null,
            'timezone' => $timezone,
            'utc_offset' => ProviderPolyfill::timezoneToTimezoneOffset($timezone)
        ];
    }
}