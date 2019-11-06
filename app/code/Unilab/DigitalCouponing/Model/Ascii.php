<?php

namespace Unilab\DigitalCouponing\Model;


use Magento\Framework\Model\AbstractModel;
use Magento\Framework\DataObject\IdentityInterface;

class Ascii extends AbstractModel implements IdentityInterface
{
    const CACHE_TAG = 'unilab_dc_asciiequivalents';

    protected $_cacheTag = 'unilab_dc_asciiequivalents';

    protected $_eventPrefix = 'unilab_dc_asciiequivalents';

    public function _construct()
    {
        $this->_init('Unilab\DigitalCouponing\Model\ResourceModel\Ascii');
    }

    /**
    * Get EntityId.
    *
    * @return int
    */
    public function getAsciiId()
    {
        return $this->getData('id');
    }

   /**
    * Set EntityId.
    */
    public function setAsciiId($id)
    {
        return $this->setData('id', $id);
    }

    public function getAscii()
    {
        return $this->getData('ascii_equivalent');
    }

    public function setAscii($ascii)
    {
        return $this->setData('ascii_equivalent', $ascii);
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