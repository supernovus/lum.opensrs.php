<?php

namespace Lum\OpenSRS;

class NoUrlException extends \Exception
{
  protected $message = 'No OpenSRS URL set.';
}
