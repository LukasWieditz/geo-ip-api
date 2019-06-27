<?php

namespace GeoIP\Provider;

class IpApiCoProvider extends AbstractProvider
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
        $result = $client->request('GET', 'http://ipapi.co/' . $ip . '/json');
        $result = array_replace_recursive([
            'city' => null,
            'region' => null,
            'region_code' => null,
            'country_name' => null,
            'country' => null,
            'continent_code' => null,
            'postal' => null,
            'latitude' => null,
            'longitude' => null,
            'asn' => null,
            'org' => null,
            'timezone' => null,
            'utc_offset' => null
        ], @json_decode($result->getBody()->getContents(), true));

        if (isset($result['error']) && $result['error']) {
            throw new \Exception($result['reason']);
        }

        return [
            'ip' => $ip,
            'city' => $result['city'],
            'region' => $result['region'],
            'region_code' => $result['region_code'],
            'country' => $result['country_name'],
            'country_code' => $result['country'],
            'continent' => ProviderPolyfill::continentCodeToContinent($result['continent_code']),
            'continent_code' => $result['continent_code'],
            'zip' => $result['postal'],
            'latitude' => $result['latitude'],
            'longitude' => $result['longitude'],
            'hostname' => gethostbyaddr($ip),
            'asn' => $result['asn'],
            'organization' => $result['org'],
            'timezone' => $result['timezone'],
            'utc_offset' => $result['utc_offset']
        ];
    }
}