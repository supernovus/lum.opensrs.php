# lum.opensrs.php

## Summary

OpenSRS API client libraries.

This is extremely limited at the moment. It currently only supports looking
up domain availability, getting the DNS Zones, and setting the DNS Zones
after making modifications. No other API calls are currently implemented.

## Classes

| Class                   | Description                                       |
| ----------------------- | ------------------------------------------------- |
| Lum\OpenSRS\Client      | The base client class.                            |
| Lum\OpenSRS\SRSCurl     | An extension of `Lum\Curl` for OpenSRS usage.     |
| Lum\OpenSRS\RequestXML  | Represents OpenSRS Request XML documents.         |
| Lum\OpenSRS\ResponseXML | Represents OpenSRS Response XML documents.        |
| Lum\OpenSRS\ZoneRecords | Represents a set of zone records.                 |

There's a few more classes, such as exceptions, and some private classes
within ZoneRecords. Look at the source if you want a full list.

The only library that is used directly is the Client.

## Official URLs

This library can be found in two places:

 * [Github](https://github.com/supernovus/lum.opensrs.php)
 * [Packageist](https://packagist.org/packages/lum/lum-opensrs)

## Author

Timothy Totten

## License

[MIT](https://spdx.org/licenses/MIT.html)
