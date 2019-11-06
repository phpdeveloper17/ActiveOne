<?php
/**
 * @category  Unilab
 * @package   Unilab_Pricelist
 * @author    Kristian Claridad
 */
namespace Unilab\Pricelist\Api\Data;

interface PricelistInterface
{
    /**
     * Constants for keys of data array. Identical to the name of the getter in snake case.
     */
    
    const PRICE_ID = 'price_id';
    const NAME = 'name';
    const COMPANY = 'company';
    const PRICE_LEVEL_ID = 'price_level_id';
    const FROM_DATE = 'from_date';
    const TO_DATE = 'to_date';
    const LIMITED_DAYS = 'limited_days';
    const LIMITED_TIME_FROM = 'limited_time_from';
    const LIMITED_TIME_TO = 'limited_time_to';
    const ACTIVE = 'active';
    const UPLOADED_BY = 'uploaded_by';
    const ID = 'id';
   /**
    * Get EntityId.
    *
    * @return int
    */
    public function getPriceId();
    public function setPriceId($price_id);

    public function getName();
    public function setName($name);

    public function getCompany();
    public function setCompany($company);

    public function getPriceLevelId();
    public function setPriceLevelId($price_level_id);

    public function getFromdate();
    public function setFromdate($from_date);

    public function getTodate();
    public function setTodate($to_date);

    public function getLimitedDays();
    public function setLimitedDays($limited_days);

    public function getLimitedTimeFrom();
    public function setLimitedTimeFrom($limited_time_from);

    public function getLimitedTimeTo();
    public function setLimitedTimeTo($limited_time_to);

    public function getActive();
    public function setActive($active);

    public function getUploadeBy();
    public function setUploadeBy($uploaded_by);

    public function getId();
    public function setId($id);

}
