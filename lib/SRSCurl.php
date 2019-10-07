<?php

namespace Lum\OpenSRS;

/**
 * OpenSRS Curl extension class.
 */
class SRSCurl extends \Lum\Curl
{
  /**
   * Set the X-Signature header.
   *
   * @param str $apikey  The reseller API key.
   * @param str $xml     The XML text of the request.
   */
  public function setSignature ($apikey, $xml)
  {
    $sig = md5(md5($xml.$apikey).$apikey);
    $this->headers['X-Signature'] = $sig;
  }
}

