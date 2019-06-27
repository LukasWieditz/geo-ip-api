<?php

require __DIR__ . '/../vendor/autoload.php';

$geoIp = new \GeoIP\GeoIP([
    'services' => ['ipinfo.ip' => ['api_key' => '<api_key>']]
]);

echo '<pre>';

var_dump($geoIp->getIpInfo('8.8.8.8'));

echo '</pre>';