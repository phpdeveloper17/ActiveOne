<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Unilab\Customers\Model\Data;

use \Magento\Framework\Api\AttributeValueFactory;

/**
 * Class Customer
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 */
class Customer extends \Magento\Customer\Model\Data\Customer
{
    /**
     * @var \Magento\Customer\Api\CustomerMetadataInterface
     */
    protected $metadataService;

    /**
     * Initialize dependencies.
     *
     * @param \Magento\Framework\Api\ExtensionAttributesFactory $extensionFactory
     * @param AttributeValueFactory $attributeValueFactory
     * @param \Magento\Customer\Api\CustomerMetadataInterface $metadataService
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Api\ExtensionAttributesFactory $extensionFactory,
        AttributeValueFactory $attributeValueFactory,
        \Magento\Customer\Api\CustomerMetadataInterface $metadataService,
        $data = []
    ) {
        $this->metadataService = $metadataService;
        parent::__construct($extensionFactory, $attributeValueFactory, $data);
    }

    
    /**
     * Get gender
     *
     * @return string|null
     */
    public function getCivilStatus()
    {
        return $this->_get('civil_status');
    }

    /**
     * Set gender
     *
     * @param string $gender
     * @return $this
     */
    public function setCivilStatus($civil_status)
    {
        return $this->setData('civil_status', $civil_status);
    }

}
