<?php
/**
 * Movshipping View XML.
 * @category  Unilab
 * @package   Unilab_Movshipping
 * @author    Kristian Claridad
 */
namespace Unilab\Movshipping\Model;

use Unilab\Movshipping\Api\Data\MovshippingInterface;

class Shipping extends \Magento\Framework\Model\AbstractModel 
{

    const CACHE_TAG = 'unilab_mov_shipping';

    protected $_cacheTag = 'unilab_mov_shipping';


    protected $_eventPrefix = 'unilab_mov_shipping';


    protected function _construct()
    {
        $this->_init('Unilab\Movshipping\Model\ResourceModel\Shipping');
    }

    public function getMovId()
    {
        return $this->getData('id');
    }
    public function setMovId($mov_id)
    {
        return $this->setData('id', $mov_id);
    }
    public function getMovGroup()
    {
        return $this->getData('group_name');
    }

    public function setMovGroup($mov_group)
    {
        return $this->setData('group_name', $mov_group);
    }

    public function getMovListOfCities()
    {
        return $this->setData('listofcities');
    }

    public function setMovListOfCities($listofcities)
    {
        return $this->getData('listofcities', $listofcities);
    }

    public function getGreaterEqualMOV()
    {
        return $this->getData('greaterEqualMOV');
    }

    public function setGreaterEqualMOV($greaterEqualMOV)
    {
        return $this->setData('greaterEqualMOV', $greaterEqualMOV);
    }

    public function getLessthanMOV()
    {
        return $this->getData('lessthan_MOV');
    }

    public function setLessthanMOV($lessthan_MOV)
    {
        return $this->setData('lessthan_MOV', $lessthan_MOV);
    }

   

}
