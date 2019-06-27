<?php

namespace GeoIP;

/**
 * Class IPResult
 * @package GeoIP
 */
class IPResult implements \ArrayAccess
{
    /**
     * @var string
     */
    protected $ip;

    /**
     * @var string
     */
    protected $city;

    /**
     * @var string
     */
    protected $region;

    /**
     * @var string
     */
    protected $region_code;

    /**
     * @var string
     */
    protected $country;

    /**
     * @var string
     */
    protected $country_code;

    /**
     * @var string
     */
    protected $continent;

    /**
     * @var string
     */
    protected $continent_code;

    /**
     * @var string
     */
    protected $zip;

    /**
     * @var float
     */
    protected $latitude;

    /**
     * @var float
     */
    protected $longitude;

    /**
     * @var string
     */
    protected $hostname;

    /**
     * @var string
     */
    protected $asn;

    /**
     * @var string
     */
    protected $organization;

    /**
     * @var string
     */
    protected $timezone;

    /**
     * @var string
     */
    protected $utc_offset;

    /**
     * IPResult constructor.
     * @param array $data
     */
    public function __construct(array $data = [])
    {
        foreach ($data as $key => $value) {
            if ($this->offsetExists($key)) {
                $this->$key = $value;
            }
        }
    }

    /**
     * @param string $offset
     * @return bool
     */
    public function offsetExists($offset)
    {
        switch ($offset) {
            case 'ip':
            case 'city':
            case 'region':
            case 'region_code':
            case 'country':
            case 'country_code':
            case 'zip':
            case 'latitude':
            case 'longitude':
            case 'hostname':
            case 'asn':
            case 'organization':
            case 'timezone':
            case 'utc_offset':
            case 'continent':
            case 'continent_code':
                return true;

            default:
                return false;
        }
    }

    /**
     * @param string $offset
     * @return string|float|null
     */
    public function offsetGet($offset)
    {
        if ($this->offsetExists($offset)) {
            return $this->$offset;
        }

        return null;
    }

    /**
     * @param string $offset
     * @param string|float $value
     * @throws \Exception
     */
    public function offsetSet($offset, $value)
    {
        throw new \Exception('Setting offsets is not supported.');
    }

    /**
     * @param string $offset
     * @throws \Exception
     */
    public function offsetUnset($offset)
    {
        throw new \Exception('Unsetting offsets is not supported.');
    }

    /**
     * @return string
     */
    public function getIp()
    {
        return $this->ip;
    }

    /**
     * @return string
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * @return string
     */
    public function getRegion()
    {
        return $this->region;
    }

    /**
     * @return string
     */
    public function getRegionCode()
    {
        return $this->region_code;
    }

    /**
     * @return string
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * @return string
     */
    public function getCountryCode()
    {
        return $this->country_code;
    }

    /**
     * @return string
     */
    public function getZip()
    {
        return $this->zip;
    }

    /**
     * @return float
     */
    public function getLatitude()
    {
        return $this->latitude;
    }

    /**
     * @return float
     */
    public function getLongitude()
    {
        return $this->longitude;
    }

    /**
     * @return string
     */
    public function getHostname()
    {
        return $this->hostname;
    }

    /**
     * @return string
     */
    public function getAsn()
    {
        return $this->asn;
    }

    /**
     * @return string
     */
    public function getOrganization()
    {
        return $this->organization;
    }

    /**
     * @return string
     */
    public function getTimezone()
    {
        return $this->timezone;
    }

    /**
     * @return string
     */
    public function getUtcOffset()
    {
        return $this->utc_offset;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            'ip' => $this->ip,
            'city' => $this->city,
            'region' => $this->region,
            'region_code' => $this->region_code,
            'country' => $this->country,
            'country_code' => $this->country_code,
            'continent' => $this->continent,
            'continent_code' => $this->continent_code,
            'zip' => $this->zip,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'hostname' => $this->hostname,
            'asn' => $this->asn,
            'organization' => $this->organization,
            'timezone' => $this->timezone,
            'utc_offset' => $this->utc_offset
        ];
    }
}