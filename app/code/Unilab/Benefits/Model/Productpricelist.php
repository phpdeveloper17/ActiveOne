<?php
/**
 * Productpricelist View XML.
 * @category  Unilab
 * @package   Unilab_Benefits->Productpricelist
 * @author    Kristian Claridad
 */
namespace Unilab\Benefits\Model;

use Unilab\Benefits\Api\Data\ProductpricelistInterface;

class Productpricelist extends \Magento\Framework\Model\AbstractModel 
{

    const CACHE_TAG = 'rra_pricelistproduct';

    protected $_cacheTag = 'rra_pricelistproduct';


    protected $_eventPrefix = 'rra_pricelistproduct';


    protected function _construct()
    {
        $this->_init('Unilab\Benefits\Model\ResourceModel\Productpricelist');
    }

    public function getId(){
        return $this->getData('id');
    }
    public function setId($id){
        return $this->setData('id', $id);
    }
    public function getPriceListID(){
        return $this->getData('pricelist_id');
    }
    public function setPriceListID($pricelist_id){
        return $this->setData('pricelist_id', $pricelist_id);
    }
    public function getProductSKU(){
        return $this->getData('product_sku');
    }
    public function setProductSKU($product_sku){
        return $this->setData('product_sku', $product_sku);
    }

    public function getProductName(){
        return $this->getData('product_name');
    }
    public function setProductName($product_name){
        return $this->setData('product_name', $product_name);
    }

    public function getQtyFrom(){
        return $this->getData('qty_from');
    }
    public function setQtyFrom($qty_from){
        return $this->setData('qty_from', $qty_from);
    }

    public function getQtyTo(){
        return $this->getData('qty_to');
    }
    public function setQtyTo($qty_to){
        return $this->setData('qty_to', $qty_to);
    }

    public function getUnitPrice(){
        return $this->getData('unit_price');
    }
    public function setUnitPrice($unit_price){
        return $this->setData('unit_price', $unit_price);
    }

    public function getDiscountAmount(){
        return $this->getData('discount_in_amount');
    }
    public function setDiscountAmount($discount_in_amount){
        return $this->setData('discount_in_amount', $discount_in_amount);
    }

    public function getDiscountPercent(){
        return $this->getData('discount_in_percent');
    }
    public function setDiscountPercent($discount_in_percent){
        return $this->setData('discount_in_percent', $discount_in_percent);
    }

    public function getFromDate(){
        return $this->getData('from_date');
    }
    public function setFromDate($from_date){
        return $this->setData('from_date', $from_date);
    }

    public function getToDate(){
        return $this->getData('to_date');
    }
    public function setToDate($to_date){
        return $this->setData('to_date', $to_date);
    }

    public function getuploadedBy(){
        return $this->getData('uploaded_by');
    }
    public function setuploadedBy($uploaded_by){
        return $this->setData('uploaded_by', $uploaded_by);
    }
}
