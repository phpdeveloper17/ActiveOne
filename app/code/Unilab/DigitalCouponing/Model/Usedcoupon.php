<?php

namespace Unilab\DigitalCouponing\Model;


use Magento\Framework\Model\AbstractModel;
use Magento\Framework\DataObject\IdentityInterface;

class Usedcoupon extends AbstractModel implements IdentityInterface
{
    const CACHE_TAG = 'unilab_dc_usedcoupon';

    protected $_cacheTag = 'unilab_dc_usedcoupon';

    protected $_eventPrefix = 'unilab_dc_usedcoupon';

    public function _construct()
    {
        $this->_init('Unilab\DigitalCouponing\Model\ResourceModel\Usedcoupon');
    }

    /**
    * Get EntityId.
    *
    * @return int
    */
    public function getCouponId()
    {
        return $this->getData('id');
    }

   /**
    * Set EntityId.
    */
    public function setCouponId($id)
    {
        return $this->setData('id', $id);
    }

    public function getCouponCode()
    {
        return $this->getData('couponcode');
    }

    public function setCouponCode($coupon)
    {
        return $this->setData('couponcode', $coupon);
    }

    public function getSku()
    {
        return $this->getData('sku');
    }

    public function setSku($sku)
    {
        return $this->setData('sku', $sku);
    }
    public function getCustomerEmail()
    {
        return $this->getData('customeremail');
    }

    public function setCustomerEmail($customeremail)
    {
        return $this->setData('customeremail', $customeremail);
    }
    public function getOrderId()
    {
        return $this->getData('orderid');
    }

    public function setOrderId($orderid)
    {
        return $this->setData('orderid', $orderid);
    }
    public function getCreatedDateTime()
    {
        return $this->getData('created_datetime');
    }

    public function setCreatedDateTime($created_datetime)
    {
        return $this->setData('created_datetime', $created_datetime);
    }


    public function getIdentities()
	{
		return [self::CACHE_TAG . '_' . $this->getId()];
	}
}