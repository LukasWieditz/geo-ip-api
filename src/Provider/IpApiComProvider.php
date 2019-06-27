<?php

namespace GeoIP\Provider;

class IpApiComProvider extends AbstractProvider
{
    /**
     * @param $ip
     * @param bool $v6
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Exception
     */
    protected function getInformationFromProvider($ip, $v6 = false)
    {
        $client = $this->getHttpClient();
        $result = $client->request('GET',
            'http://ip-api.com/json/' . $ip, [
                'query' => ['fields' => 'status,message,continent,continentCode,country,countryCode,region,regionName,city,zip,lat,lon,timezone,org,as']
            ]);
        $result = array_replace_recursive([
            'city' => null,
            'regionName' => null,
            'region' => null,
            'country' => null,
            'countryCode' => null,
            'continent' => null,
            'continentCode' => null,
            'zip' => null,
            'lat' => null,
            'lon' => null,
            'org' => null,
            'timezone' => null
        ], @json_decode($result->getBody()->getContents(), true));

        if (!$result['status']) {
            throw new \Exception($result['message']);
        }

        $as = explode(' ', $result['as']);

        $timezone = $result['timezone'] ?: ProviderPolyfill::countryCodeToTimezone($result['countryCode']);

        return [
            'ip' => $ip,
            'city' => $result['city'],
            'region' => $result['regionName'],
            'region_code' => $result['region'],
            'country' => $result['country'],
            'country_code' => $result['countryCode'],
            'continent' => $result['continent'],
            'continent_code' => $result['continentCode'],
            'zip' => $result['zip'],
            'latitude' => $result['lat'],
            'longitude' => $result['lon'],
            'hostname' => gethostbyaddr($ip),
            'asn' => array_shift($as),
            'organization' => $result['org'],
            'timezone' => $timezone,
            'utc_offset' => ProviderPolyfill::timezoneToTimezoneOffset($timezone)
        ];
    }
}