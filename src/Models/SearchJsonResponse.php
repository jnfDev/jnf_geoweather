<?php
/*
 * WeatherAPILib
 *
 * This file was automatically generated by APIMATIC v2.0 ( https://apimatic.io ).
 */

namespace WeatherAPILib\Models;

use JsonSerializable;

/**
 * @todo Write general description for this model
 */
class SearchJsonResponse implements JsonSerializable
{
    /**
     * @todo Write general description for this property
     * @var integer|null $id public property
     */
    public $id;

    /**
     * Local area name.
     * @var string|null $name public property
     */
    public $name;

    /**
     * Local area region.
     * @var string|null $region public property
     */
    public $region;

    /**
     * Country
     * @var string|null $country public property
     */
    public $country;

    /**
     * Area latitude
     * @var double|null $lat public property
     */
    public $lat;

    /**
     * Area longitude
     * @var double|null $lon public property
     */
    public $lon;

    /**
     * @todo Write general description for this property
     * @var string|null $url public property
     */
    public $url;

    /**
     * Constructor to set initial or default values of member properties
     * @param integer $id      Initialization value for $this->id
     * @param string  $name    Initialization value for $this->name
     * @param string  $region  Initialization value for $this->region
     * @param string  $country Initialization value for $this->country
     * @param double  $lat     Initialization value for $this->lat
     * @param double  $lon     Initialization value for $this->lon
     * @param string  $url     Initialization value for $this->url
     */
    public function __construct()
    {
        if (7 == func_num_args()) {
            $this->id      = func_get_arg(0);
            $this->name    = func_get_arg(1);
            $this->region  = func_get_arg(2);
            $this->country = func_get_arg(3);
            $this->lat     = func_get_arg(4);
            $this->lon     = func_get_arg(5);
            $this->url     = func_get_arg(6);
        }
    }


    /**
     * Encode this object to JSON
     */
    public function jsonSerialize()
    {
        $json = array();
        $json['id']      = $this->id;
        $json['name']    = $this->name;
        $json['region']  = $this->region;
        $json['country'] = $this->country;
        $json['lat']     = $this->lat;
        $json['lon']     = $this->lon;
        $json['url']     = $this->url;

        return $json;
    }
}