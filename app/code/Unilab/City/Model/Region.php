<?php
/**
 * City View XML.
 * @category  Unilab
 * @package   Unilab_City
 * @author    Ron Mark Peroso Rudas   
 */
namespace Unilab\City\Model;

use Unilab\City\Api\Data\CityInterface;

class Region extends \Magento\Framework\Model\AbstractModel 
{
  
    const CACHE_TAG = 'directory_country_region';

    protected $_cacheTag = 'directory_country_region';

    protected $_eventPrefix = 'directory_country_region';

    protected function _construct()
    {
        $this->_init('Unilab\City\Model\ResourceModel\Region');
    }
   
    public function getRegionId()
    {
        return $this->getData('region_id');
    }

    public function setRegionId($regionId)
    {
        return $this->setData('region_id', $regionId);
    }

    public function getRegionName()
    {
        return $this->getData('default_name');
    }

  
    public function setRegionName($region_name)
    {
        return $this->setData('default_name', $region_name);
    }

    public function getCountryId()
    {
        return $this->getData('country_id');
    }

  
    public function setCountryId($country_id)
    {
        return $this->setData('country_id', $country_id);
    }

    public function setRegionCode($region_code)
    {
        return $this->setData('code', $region_code);
    }

    public function getRegionCode()
    {
        return $this->getData('code');
    }

}
