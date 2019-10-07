<?php

namespace Lum\OpenSRS;

/**
 * Exception for Curl errors.
 */
class CurlException extends \Exception
{
  protected $message = 'Curl returned error';
}

