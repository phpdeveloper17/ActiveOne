<?php
/**
 * @category  Unilab
 * @package   Unilab_City
 * @author    Ron Mark Peroso Rudas   
 */
namespace Unilab\City\Api\Data;

interface CityInterface
{
    /**
     * Constants for keys of data array. Identical to the name of the getter in snake case.
     */
    const CITY_ID = 'city_id';
    const CITY_NAME = 'name';
   /**
    * Get EntityId.
    *
    * @return int
    */
    public function getCityId();

   /**
    * Set EntityId.
    */
    public function setCityId($CityId);

   /**
    * Get Title.
    *
    * @return varchar
    */
    public function getCityName();

   /**
    * Set Title.
    */
    public function setCityName($CityName);


    public function getCountryId();
    public function setCountryId($CountryId);
}
