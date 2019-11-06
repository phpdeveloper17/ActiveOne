<?php

namespace Unilab\Benefits\Model\Source;

class RefreshPeriod extends \Magento\Eav\Model\Entity\Attribute\Source\Table
{ 
    
    protected $_eavConfig;

    public function __construct(
        \Magento\Eav\Model\Config $eavConfig
    ) {
        $this->_eavConfig = $eavConfig;
    }

    public function getAllOptions()
    {

        $attribute_code = "refresh_period"; 
        $attribute_details = $this->_eavConfig->getAttribute("catalog_product", $attribute_code); 
        $options = $attribute_details->setStoreId(0)->getSource()->getAllOptions(); 
      
        return $options;
    }
}