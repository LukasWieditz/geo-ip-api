<?php

require __DIR__ . '/../vendor/autoload.php';

$geoIp = new \GeoIP\GeoIP([
    'services' => ['ipapi.co' => ['api_key' => true]]
]);

echo '<pre>';

var_dump($geoIp->getIpInfo('8.8.8.8'));

echo '</pre>';