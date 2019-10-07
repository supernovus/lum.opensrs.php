<?php

namespace Lum\OpenSRS;

/**
 * OpenSRS Request XML class.
 *
 * Works like a regular SimpleXMLElement, but with some added
 * methods that make working with the OpenSRS API format easier.
 */
class RequestXML extends \SimpleDOM
{
  /**
   * Determine if an array is associative or not.
   *
   * @param array $a  PHP Array to test.
   * @return bool  Is the array associative?
   */
  public static function is_assoc ($a)
  {
    if (!is_array($a) || empty($a))
    { // Not an array, or an empty array.
      return null;
    }
    foreach (array_keys($a) as $k => $v)
    {
      if ($k !== $v)
      {
        return true;
      }
    }
    return false;
  }

  /**
   * Add a <data_block/> element.
   *
   * @param array $data  If specified, will be the child content.
   * @return RequestXML  The <data_block/> element.
   */
  public function addDataBlock ($data=null)
  {
    $dblock = $this->addChild('data_block');
    if (isset($data) && is_array($data))
    {
      if (self::is_assoc($data))
      {
        $dblock->addAssoc($data);
      }
      else
      {
        $dblock->addArray($data);
      }
    }
    return $dblock;
  }

  /**
   * Add a <dt_assoc/> element.
   *
   * @param array $data  If specified, will be the child content.
   * @return RequestXML  The <dt_assoc/> element.
   */
  public function addAssoc ($data=null)
  {
    $assoc = $this->addChild('dt_assoc');
    if (isset($data) && is_array($data))
    {
      foreach ($data as $key => $val)
      {
        $assoc->addItem($key, $val);
      }
    }
    return $assoc;
  }

  /**
   * Add a <dt_array/> element.
   *
   * @param array $data  If specified, will be the child content.
   * @return RequestXML  The <dt_array/> element.
   */
  public function addArray ($data=null)
  {
    $array = $this->addChild('dt_array');
    if (isset($data) && is_array($data))
    {
      foreach ($data as $key => $val)
      {
        $array->addItem($key, $val);
      }
    }
    return $array;
  }

  /**
   * Add an <item key="name" /> element.
   *
   * @param str|int $key  The array key for the item.
   * @param mixed $val  The value for the item.
   *
   * The value may be a string, number, associative array, flat array,
   * or an object with a 'toReqXML()' method to handle serialization.
   *
   * @return RequestXML  The <item/> element.
   */
  public function addItem ($key, $val)
  {
    if (is_string($val) || is_numeric($val))
    {
      $iel = $this->addChild('item', $val);
      $iel['key'] = $key;
    }
    elseif (is_array($val))
    {
      $iel = $this->addChild('item');
      $iel['key'] = $key;
      if (self::is_assoc($val))
      {
        $iel->addAssoc($val);
      }
      else
      {
        $iel->addArray($val);
      }
    }
    elseif (is_object($val) && is_callable([$val, 'toReqXML']))
    {
      $iel = $this->addChild('item');
      $iel['key'] = $key;
      $val->toReqXML($iel);
    }

    if (!isset($iel))
      throw new InvalidItemException();

    return $iel;
  }

}

