<?php

namespace Lum\OpenSRS;

/**
 * OpenSRS API client.
 */
class Client
{
  /**
   * Contant for OpenSRS URLs
   */
  const OPENSRS_URLS =
  [
    'live' => 'https://rr-n1-tor.opensrs.net:55443',
    'test' => 'https://horizon.opensrs.net:55443',
  ];

  protected $username;
  protected $apikey;
  public $url;

  public $debug = false;

  /**
   * Build a new OpenSRS client.
   *
   * @param str $username  The reseller username.
   * @param str $apikey    The reseller API key.
   */
  public function __construct ($username, $apikey)
  {
    $this->username = $username;
    $this->apikey   = $apikey;
  }

  /**
   * Use the testing service.
   */
  public function use_test ()
  {
    $this->url = self::OPENSRS_URLS['test'];
    return $this;
  }

  /**
   * Use the live service.
   */
  public function use_live ()
  {
    $this->url = self::OPENSRS_URLS['live'];
    return $this;
  }

  /**
   * Get an RequestXML object ready to be populated.
   */
  public function newRequest ()
  {
    $xmlTemplate = <<<EOD
<?xml version="1.0" encoding="UTF-8" standalone="no" ?>
<!DOCTYPE OPS_envelope SYSTEM 'ops.dtd'>
<OPS_envelope>
  <header>
    <version>0.9</version>
  </header>
  <body></body>
</OPS_envelope>
EOD;

    return new RequestXML($xmlTemplate);
  }

  /**
   * Return a SRSCurl instance ready to be used.
   *
   * @param str $xml    [Optional] XML text for signature.
   * @param array $opts [Optional] Options for Curl constructor.
   */
  public function newCurl ($xml=null, $opts=[])
  {
    $curl = new SRSCurl($opts);
    $curl->content_type('text/xml');
    $curl->headers['X-Username'] = $this->username;
    if (isset($xml))
    {
      $curl->setSignature($this->apikey, $xml);
    }
    return $curl;
  }

  /**
   * Given a data structure, build a Request and Curl object,
   * and send the request to the service.
   *
   * @param array $data  A structure representing the request body.
   * @return ResponseXML  The response object.
   */
  public function simpleRequest ($data)
  {
    if (!isset($this->url))
    {
      throw new NoUrlException();
    }
    $request = $this->newRequest();
    $request->body->addDataBlock($data);
    $xml = $request->asXML();
    if ($this->debug)
    {
      error_log("[DEBUG.Request]: $xml");
    }
    $curl = $this->newCurl($xml);
    $response = $curl->post($this->url, $xml, true);
    if (is_array($response))
    {
      error_log("Curl error: ".json_encode($response));
      throw new CurlException();
    }
    if (substr($response, 0, 5) !== '<?xml')
      throw new InvalidResponseException();
    return new ResponseXML($response);
  }

  /**
   * Look up domain availability.
   *
   * @param str $domain  The domain to look up.
   * @return ResponseXML  The response object.
   */
  public function lookupDomain ($domain)
  {
    $data = 
    [
      'protocol'   => 'XCP',
      'action'     => 'lookup',
      'object'     => 'domain',
      'attributes' =>
      [
        'domain' => $domain,
      ],
    ];
    return $this->simpleRequest($data);
  }

  /**
   * Get the DNS zones for a given domain.
   *
   * @param str $domain   The domain we are querying.
   * @return ResponseXML  The response object.
   */
  public function getDNSZone ($domain)
  {
    $data =
    [
      'protocol' => 'XCP',
      'action' => 'get_dns_zone',
      'object' => 'domain',
      'attributes' =>
      [
        'domain' => $domain,
      ],
    ];
    return $this->simpleRequest($data);
  }

  /**
   * Set the DNS zones for a given domain.
   *
   * @param str $domain  The domain we are updating.
   * @param ZoneRecords $records  The updated records object.
   * @return ResponseXML  The response object.
   */
  public function setDNSZone ($domain, $records)
  {
    $data =
    [
      'protocol' => 'XCP',
      'action'   => 'set_dns_zone',
      'object'   => 'domain',
      'attributes' =>
      [
        'domain'  => $domain,
        'records' => $records,
      ],
    ];
    return $this->simpleRequest($data);
  }

}

