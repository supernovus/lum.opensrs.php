<?php

namespace Lum\OpenSRS;

/**
 * OpenSRS Response XML class.
 *
 * Works like a regular SimpleXMLElement, but with some added
 * methods that make working with the OpenSRS API format easier.
 */
class ResponseXML extends \SimpleDOM
{
  /**
   * Was the request successful?
   */
  public function isSuccess ()
  {
    $root = $this->rootElement();
    $is_succ = $root->xpath('//item[@key="is_success"]');
    if (isset($is_succ) && count($is_succ) > 0)
    {
      if ((string)$is_succ[0] == '1')
      {
        return true;
      }
    }
    return false;
  }

  /**
   * Return a PHP array representing the 'attributes' property.
   */
  public function srsAttributes ()
  {
    $root = $this->rootElement();
    $attrXML = $root->xpath('//item[@key="attributes"]');
    if (isset($attrXML) && count($attrXML) > 0)
    {
      $attrXML = $attrXML[0]; // there can be only one.
      return $attrXML->getContents();
    }
  }

  /**
   * Return a PHP array representing all returned body properties.
   */
  public function srsBody ()
  {
    $root = $this->rootElement();
    return $root->body->getContents();
  }

  /**
   * For any element, if it has a <dt_assoc/> child, return an 
   * associative array, or if it has a <dt_array/> child, return 
   * a flat array.
   */
  public function getContents ()
  {
    if (isset($this->dt_assoc))
    {
      return $this->dt_assoc->getItems(true);
    }
    elseif (isset($this->dt_array))
    {
      return $this->dt_array->getItems(false);
    }
  }

  /**
   * Used to either a <dt_assoc/> or <dt_array/> element, it will
   * return all <item/> elements a PHP array. It will further
   * convert any nested <dt_assoc/> or <dt_array/> structures.
   */
  public function getItems ($isAssoc)
  {
    if (!isset($this->item))
      return null;
    $items = [];
    foreach ($this->item as $item)
    {
      $key = (string)$item['key'];
      if (isset($item->dt_assoc))
      {
        $val = $item->dt_assoc->getItems(true);
      }
      elseif (isset($item->dt_array))
      {
        $val = $item->dt_array->getItems(false);
      }
      else
      {
        $val = (string)$item;
      }
      if (!$isAssoc)
        $key = intval($key);
      $items[$key] = $val;
    }
    return $items;
  }

  /**
   * Return the DNS records as an object.
   *
   * @return ZoneRecords  The records object.
   */
  public function dnsRecords ()
  {
    $attrs = $this->srsAttributes();
    if (isset($attrs['records']))
    {
      return new ZoneRecords($attrs['records']);
    }
  }

}


