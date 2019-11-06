<?php
/**
 * @category  Unilab
 * @package   Unilab_Afptc
 * @author    Kristian Claridad
 */
namespace Unilab\Afptc\Api\Data;

interface AfptcInterface
{
    /**
     * Constants for keys of data array. Identical to the name of the getter in snake case.
     */
    
    const RULE_ID = 'rule_id';
    const NAME = 'name';
    const DESCRIPTION = 'description';
    const STATUS = 'status';
    const STORE_IDS = 'store_ids';
    const CUSTOMER_GROUPS = 'customer_groups';
    const DISCOUNT = 'discount';
    const PRIORITY = 'priority';
    const SIMPLE_ACTION = 'simple_action';
    const DISCOUNT_STEP = 'discount_step';
    const N_PRODUCT = 'n_product';
    const X_PRODUCT = 'x_product';
    const X_AUTOINCFREEPROD = 'x_autoincfreeprod';
    const AUTO_INCFREEPROD = 'auto_incfreeprod';
    const SHOW_POPUP = 'show_popup';
    const SHOW_ONCE = 'show_once';
    const FREE_SHIPPING = 'free_shipping';
    const PRODUCT_ID = 'product_id';
    const CONDITIONS_SERIALIZED = 'conditions_serialized';
    const START_DATE = 'start_date';
    const COUNPON_CODE = 'coupon_code';
    const Y_QTY = 'y_qty';
    const Y_ITEM = 'y_item';
    const AUTO_INCREMENT = 'auto_increment';
    const TWO_PROMOITEM = 'two_promoitem';
    const END_DATE = 'end_date';
    const STOP_RULES_PROCESSING = 'stop_rules_processing';
   /**
    * Get EntityId.
    *
    * @return int
    */
    public function getRule_id();
    public function setRule_id($rule_id);

    public function getName();
    public function setName($name);

    public function getDescription();
    public function setDescription($description);

    public function getStatus();
    public function setStatus($status);

    public function getCustomer_groups();
    public function setCustomer_groups($store_ids);

    public function getStore_ids();
    public function setStore_ids($customer_groups);

    public function getDiscount();
    public function setDiscount($discount);

    public function getPriority();
    public function setPriority($priority);
    
    public function getSimple_action();
    public function setSimple_action($simple_action);

    public function getDiscount_step();
    public function setDiscount_step($discount_step);

    public function getN_product();
    public function setN_product($n_product);

    public function getX_product();
    public function setX_product($x_product);

    public function getX_autoincfreeprod();
    public function setX_autoincfreeprod($x_autoincfreeprod);

    public function getAuto_incfreeprod();
    public function setAuto_incfreeprod($auto_incfreeprod);

    public function getShow_popup();
    public function setShow_popup($show_popup);

    public function getFree_shipping();
    public function setFree_shipping($free_shipping);

    public function getProduct_id();
    public function setProduct_id($product_id);

    public function getCondition_serialized();
    public function setCondition_serialized($conditions_serialized);

    public function getStart_date();
    public function setStart_date($start_date);

    public function getCoupon_code();
    public function setCoupon_code($coupon_code);

    public function getY_qty();
    public function setY_qty($y_qty);
    
    public function getY_item();
    public function setY_item($y_item);

    public function getAuto_increment();
    public function setAuto_increment($auto_increment);

    public function getTwo_promoitem();
    public function setTwo_promoitem($two_promoitem);

    public function getEnd_date();
    public function setEnd_date($end_date);

    public function getStop_rules_processing();
    public function setStop_rules_processing($stop_rules_processing);

}
