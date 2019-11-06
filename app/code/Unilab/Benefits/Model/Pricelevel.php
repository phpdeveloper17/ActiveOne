<?php
/**
 * Pricelevel View XML.
 * @category  Unilab
 * @package   Unilab_Benefits->Pricelevel
 * @author    Kristian Claridad
 */
namespace Unilab\Benefits\Model;

use Unilab\Benefits\Api\Data\PricelevelInterface;

class Pricelevel extends \Magento\Framework\Model\AbstractModel 
{

    const CACHE_TAG = 'rra_pricelevelmaster';

    protected $_cacheTag = 'rra_pricelevelmaster';


    protected $_eventPrefix = 'rra_pricelevelmaster';


    protected function _construct()
    {
        $this->_init('Unilab\Benefits\Model\ResourceModel\Pricelevel');
    }

    public function getId(){
        return $this->getData('id');
    }
    public function setId($id){
        return $this->setData('id', $id);
    }

    public function getPriceName(){
        return $this->getData('price_name');
    }
    public function setPriceName($price_name){
        return $this->setData('price_name', $price_name);
    }

    public function getPriceLevelID(){
        return $this->getData('price_level_id');
    }
    public function setPriceLevelID($price_level_id){
        return $this->setData('price_level_id', $price_level_id);
    }

    public function getIsActive(){
        return $this->getData('is_active');
    }
    public function setIsActive($is_active){
        return $this->setData('is_active', $is_active);
    }

    public function getMemo(){
        return $this->getData('memo');
    }
    public function setMemo($memo){
        return $this->setData('memo', $memo);
    }

    public function getPrefix(){
        return $this->getData('prefix');
    }
    public function setPrefix($prefix){
        return $this->setData('prefix', $prefix);
    }

    public function getCreatedTime(){
        return $this->getData('created_time');
    }
    public function setCreatedTime($created_time){
        return $this->setData('created_time', $created_time);
    }

    public function getUpdateTime(){
        return $this->getData('update_time');
    }
    public function setUpdateTime($update_time){
        return $this->setData('update_time', $update_time);
    }

}
