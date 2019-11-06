<?php
/**
 * Pricelist View XML.
 * @category  Unilab
 * @package   Unilab_Pricelist
 * @author    Kristian Claridad
 */
namespace Unilab\Pricelist\Model;

use Unilab\Pricelist\Api\Data\PricelistInterface;

class CatalogRule extends \Magento\Framework\Model\AbstractModel 
{

    const CACHE_TAG = 'catalogrule';

    protected $_cacheTag = 'catalogrule';


    protected $_eventPrefix = 'catalogrule';


    protected function _construct()
    {
        $this->_init('Unilab\Pricelist\Model\ResourceModel\Catalogrule');
    }

    // public function getPriceId(){
    //     return $this->getData('price_id');
    // }
    // public function setPriceId($price_id){
    //     return $this->setData('price_id', $price_id);
    // }

    // public function getName(){
    //     return $this->getData('name');
    // }
    // public function setName($name){
    //     return $this->setData('name', $name);
    // }

    // public function getCompany(){
    //     return $this->getData('company');
    // }
    // public function setCompany($company){
    //     return $this->setData('company', $company);
    // }

    // public function getPriceLevelId(){
    //     return $this->getData('price_level_id');
    // }
    // public function setPriceLevelId($price_level_id){
    //     return $this->setData('price_level_id', $price_level_id);
    // }

    // public function getFromdate(){
    //     return $this->getData('from_date');
    // }
    // public function setFromdate($from_date){
    //     return $this->setData('from_date', $from_date);
    // }

    // public function getTodate(){
    //     return $this->getData('to_date');
    // }
    // public function setTodate($to_date){
    //     return $this->setData('to_date', $to_date);
    // }

    // public function getLimitedDays(){
    //     return $this->getData('limited_days');
    // }
    // public function setLimitedDays($limited_days){
    //     return $this->setData('limited_days', $limited_days);
    // }

    // public function getLimitedTimeFrom(){
    //     return $this->getData('limited_time_from');
    // }
    // public function setLimitedTimeFrom($limited_time_from){
    //     return $this->setData('limited_time_from', $limited_time_from);
    // }

    // public function getLimitedTimeTo(){
    //     return $this->getData('limited_time_to');
    // }
    // public function setLimitedTimeTo($limited_time_to){
    //     return $this->setData('limited_time_to', $limited_time_to);
    // }

    // public function getActive(){
    //     return $this->getData('active');
    // }
    // public function setActive($active){
    //     return $this->setData('active', $active);
    // }

    // public function getUploadeBy(){
    //     return $this->getData('uploaded_by');
    // }
    // public function setUploadeBy($uploaded_by){
    //     return $this->setData('uploaded_by', $uploaded_by);
    // }

    // public function getId(){
    //     return $this->getData('id');
    // }
    // public function setId($id){
    //     return $this->setData('id', $id);
    // }
}
