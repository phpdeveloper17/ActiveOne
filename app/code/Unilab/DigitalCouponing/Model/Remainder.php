<?php

namespace Unilab\DigitalCouponing\Model;


use Magento\Framework\Model\AbstractModel;
use Magento\Framework\DataObject\IdentityInterface;

class Remainder extends AbstractModel implements IdentityInterface
{
    const CACHE_TAG = 'unilab_dc_remainderequivs';

    protected $_cacheTag = 'unilab_dc_remainderequivs';

    protected $_eventPrefix = 'unilab_dc_remainderequivs';

    public function _construct()
    {
        $this->_init('Unilab\DigitalCouponing\Model\ResourceModel\Remainder');
    }

    /**
    * Get EntityId.
    *
    * @return int
    */
    public function getRemainderId()
    {
        return $this->getData('id');
    }

   /**
    * Set EntityId.
    */
    public function setRemainderId($id)
    {
        return $this->setData('id', $id);
    }

    public function getRemainder()
    {
        return $this->getData('remainder_equivalent');
    }

    public function setRemainder($remainder)
    {
        return $this->setData('remainder_equivalent', $remainder);
    }

    public function getLetter()
    {
        return $this->getData('letter');
    }

    public function setLetter($letter)
    {
        return $this->setData('letter', $letter);
    }


    public function getIdentities()
	{
		return [self::CACHE_TAG . '_' . $this->getId()];
	}
}