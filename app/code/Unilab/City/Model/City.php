<?php
/**
 * City View XML.
 * @category  Unilab
 * @package   Unilab_City
 * @author    Ron Mark Peroso Rudas   
 */
namespace Unilab\City\Model;

use Unilab\City\Api\Data\CityInterface;

class City extends \Magento\Framework\Model\AbstractModel 
{

    const CACHE_TAG = 'unilab_cities';

    protected $_cacheTag = 'unilab_cities';


    protected $_eventPrefix = 'unilab_cities';


    protected function _construct()
    {
        $this->_init('Unilab\City\Model\ResourceModel\City');
    }

    public function getCityId()
    {
        return $this->getData('city_id');
    }
    public function setCityId($countryId)
    {
        return $this->setData('city_id', $countryId);
    }
    public function setCountryId($countryId)
    {
        return $this->setData('country_id', $countryId);
    }

    public function getCountryId()
    {
        return $this->getData('country_id');
    }

    public function setRegionCode($region_code)
    {
        return $this->setData('region_code', $region_code);
    }

    public function getRegionCode()
    {
        return $this->getData('region_code');
    }

    public function setEntityId($cityId)
    {
        return $this->setData('city_id', $cityId);
    }

    public function getCityName()
    {
        return $this->getData('name');
    }

    public function setCityName($cityName)
    {
        return $this->setData('name', $cityName);
    }

}
