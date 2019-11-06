<?php

namespace Unilab\Inquiry\Model;

use Unilab\Inquiry\Api\Data\InquiryInterface;

class Inquiry extends \Magento\Framework\Model\AbstractModel implements InquiryInterface
{
    const CACHE_TAG = 'unilab_inquiry';

    protected $_cacheTag = 'unilab_inquiry';

    protected $_eventPrefix = 'unilab_inquiry';

    public function _construct()
    {
        $this->_init('Unilab\Inquiry\Model\ResourceModel\Inquiry');
    }

    public function getInquiryId()
    {
        return $this->getData(self::INQUIRY_ID);
    }

    public function setInquiryId($data)
    {
        $this->setData(self::INQUIRY_ID, $data);
    }

    public function getStoreId()
    {
        return $this->getData(self::STORE_ID);
    }

    public function setStoreId($data)
    {
        return $this->setData(self::STORE_ID, $data);
    }

    public function getCustomerId()
    {
        return $this->getData(self::CUSTOMER_ID);
    }

    public function setCustomerId($data)
    {
        return $this->setData(self::CUSTOMER_ID, $data);
    }

    public function getDepartment()
    {
        return $this->getData(self::DEPARTMENT);
    }

    public function setDepartment($data)
    {
        return $this->setData(self::DEPARTMENT, $data);
    }

    public function getDepartmentEmail()
    {
        return $this->getData(self::DEPARTMENT_EMAIL);
    }

    public function setDepartmentEmail($data)
    {
        return $this->setData(self::DEPARTMENT_EMAIL, $data);
    }

    public function getConcern()
    {
        return $this->getData(self::CONCERN);
    }

    public function setConcern($data)
    {
        return $this->setData(self::CONCERN, $data);
    }

    public function getEmailAddress()
    {
        return $this->getData(self::EMAIL_ADDRESS);
    }

    public function setEmailAddress($data)
    {
        return $this->setData(self::EMAIL_ADDRESS, $data);
    }

    public function getName()
    {
        return $this->getData(self::NAME);
    }

    public function setName($data)
    {
        return $this->setData(self::NAME, $data);
    }

    public function getIsRead()
    {
        return $this->getData(self::IS_READ);
    }

    public function setIsRead($data)
    {
        return $this->setData(self::IS_READ, $data);
    }

    public function getCreatedTime()
    {
        return $this->getData(self::CREATED_TIME);
    }

    public function setCreatedTime($data)
    {
        return $this->setData(self::CREATED_TIME, $data);
    }
}
