<?php
/**
 * @category  Unilab
 * @package   Unilab_Benefits->Productpricelist
 * @author    Kristian Claridad
 */
namespace Unilab\Benefits\Api\Data;

interface ProductpricelistInterface
{
    /**
     * Constants for keys of data array. Identical to the name of the getter in snake case.
     */
    
    const ID = 'id';
    const PRICELIST_ID = 'pricelist_id';
    const PRODUCT_SKU = 'product_sku';
    const PRODUCT_NAME = 'product_name';
    const QTY_FROM = 'qty_from';
    const QTY_TO = 'qty_to';
    const UNIT_PRICE = 'unit_price';
    const DISCOUNT_AMOUNT = 'discount_in_amount';
    const DISCOUNT_PERCENT = 'discount_in_percent';
    const FROM_DATE = 'from_date';
    const TO_DATE = 'to_date';
    const UPLOADED_BY = 'uploaded_by';
   
   /**
    * Get EntityId.
    *
    * @return int
    */
    public function getId();
    public function setId($id);

    public function getPriceListID();
    public function setPriceListID($pricelist_id);

    public function getProductSKU();
    public function setProductSKU($product_sku);

    public function getProductName();
    public function setProductName($product_name);

    public function getQtyFrom();
    public function setQtyFrom($qty_from);

    public function getQtyTo();
    public function setQtyTo($qty_to);

    public function getUnitPrice();
    public function setUnitPrice($unit_price);

    public function getDiscountAmount();
    public function setDiscountAmount($discount_in_amount);

    public function getDiscountPercent();
    public function setDiscountPercent($discount_in_percent);

    public function getFromDate();
    public function setFromDate($from_date);

    public function getToDate();
    public function setToDate($to_date);

    public function getuploadedBy();
    public function setuploadedBy($uploaded_by);

}
