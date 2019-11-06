<?php
/**
 * Afptc View XML.
 * @category  Unilab
 * @package   Unilab_Afptc
 * @author    Kristian Claridad
 */
namespace Unilab\Afptc\Model;

use Unilab\Afptc\Api\Data\AfptcInterface;

class Afptc extends \Magento\Framework\Model\AbstractModel 
{

    const CACHE_TAG = 'aw_afptc_rules';

    protected $_cacheTag = 'aw_afptc_rules';


    protected $_eventPrefix = 'aw_afptc_rules';


    protected function _construct()
    {
        $this->_init('Unilab\Afptc\Model\ResourceModel\Afptc');
    }

    public function getRule_id(){
        return $this->getData('rule_id');
    }
    public function setRule_id($rule_id){
        return $this->setData('rule_id', $rule_id);
    }

    public function getName(){
        return $this->getData('name');
    }
    public function setName($name){
        return $this->setData('name', $name);
    }

    public function getDescription(){
        return $this->getData('description');
    }
    public function setDescription($description){
        return $this->setData('description', $description);
    }

    public function getStatus(){
        return $this->getData('status');
    }
    public function setStatus($status){
        return $this->setData('status', $status);
    }

    public function getCustomer_groups(){
        return $this->getData('customer_groups');
    }
    public function setCustomer_groups($customer_groups){
        return $this->setData('customer_groups', $customer_groups);
    }

    public function getStore_ids(){
        return $this->getData('store_ids');
    }
    public function setStore_ids($store_ids){
        return $this->setData('store_ids', $store_ids);
    }

    public function getDiscount(){
        return $this->getData('discount');
    }
    public function setDiscount($discount){
        return $this->setData('discount', $discount);
    }

    public function getPriority(){
        return $this->getData('priority');
    }
    public function setPriority($priority){
        return $this->setData('priority', $priority);
    }
    
    public function getSimple_action(){
        return $this->getData('simple_action');
    }
    public function setSimple_action($simple_action){
        return $this->setData('simple_action', $simple_action);
    }

    public function getDiscount_step(){
        return $this->getData('discount_step');
    }
    public function setDiscount_step($discount_step){
        return $this->setData('discount_step', $discount_step);
    }

    public function getN_product(){
        return $this->getData('n_product');
    }
    public function setN_product($n_product){
        return $this->setData('n_product', $n_product);
    }

    public function getX_product(){
        return $this->getData('x_product');
    }
    public function setX_product($x_product){
        return $this->setData('x_product', $x_product);
    }

    public function getX_autoincfreeprod(){
        return $this->getData('x_autoincfreeprod');
    }
    public function setX_autoincfreeprod($x_autoincfreeprod){
        return $this->setData('x_autoincfreeprod', $x_autoincfreeprod);
    }

    public function getAuto_incfreeprod(){
        return $this->getData('auto_incfreeprod');
    }
    public function setAuto_incfreeprod($auto_incfreeprod){
        return $this->setData('auto_incfreeprod', $auto_incfreeprod);
    }

    public function getShow_popup(){
        return $this->getData('show_popup');
    }
    public function setShow_popup($show_popup){
        return $this->setData('show_popup', $show_popup);
    }

    public function getFree_shipping(){
        return $this->getData('free_shipping');
    }
    public function setFree_shipping($free_shipping){
        return $this->setData('free_shipping', $free_shipping);
    }

    public function getProduct_id(){
        return $this->getData('product_id');
    }
    public function setProduct_id($product_id){
        return $this->setData('product_id', $product_id);
    }

    public function getCondition_serialized(){
        return $this->getData('conditions_serialized');
    }
    public function setCondition_serialized($conditions_serialized){
        return $this->setData('conditions_serialized', $conditions_serialized);
    }

    public function getStart_date(){
        return $this->getData('start_date');
    }
    public function setStart_date($start_date){
        return $this->setData('start_date', $start_date);
    }

    public function getCoupon_code(){
        return $this->getData('coupon_code');
    }
    public function setCoupon_code($coupon_code){
        return $this->setData('coupon_code', $coupon_code);
    }

    public function getY_qty(){
        return $this->getData('y_qty');
    }
    public function setY_qty($y_qty){
        return $this->setData('y_qty', $y_qty);
    }
    
    public function getY_item(){
        return $this->getData('y_item');
    }
    public function setY_item($y_item){
        return $this->setData('y_item', $y_item);
    }

    public function getAuto_increment(){
        return $this->getData('auto_increment');
    }
    public function setAuto_increment($auto_increment){
        return $this->setData('auto_increment', $auto_increment);
    }

    public function getTwo_promoitem(){
        return $this->getData('two_promoitem');
    }
    public function setTwo_promoitem($two_promoitem){
        return $this->setData('two_promoitem', $two_promoitem);
    }

    public function getEnd_date(){
        return $this->getData('end_date');
    }
    public function setEnd_date($end_date){
        return $this->setData('end_date', $end_date);
    }

    public function getStop_rules_processing(){
        return $this->getData('stop_rules_processing');
    }
    public function setStop_rules_processing($stop_rules_processing){
        return $this->setData('stop_rules_processing', $stop_rules_processing);
    }
}
