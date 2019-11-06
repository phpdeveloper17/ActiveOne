<?php

namespace Unilab\Benefits\Model\Source;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class PrebookStatus
 */
class Province implements OptionSourceInterface
{
    protected $resourceConnection;
    protected $scopeConfig;
    protected $countryFactory;

    public function __construct(
        \Magento\Framework\App\ResourceConnection $resourceConnection,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Directory\Model\Region $countryFactory
    ) {
        $this->resourceConnection = $resourceConnection;
        $this->scopeConfig = $scopeConfig;
        $this->countryFactory = $countryFactory;
    }

    /**
     * Get options
     *
     * @return array
     */
    public function toOptionArray()
    {
        $countryCode = $this->scopeConfig->getValue('general/country/default');
        //https://www.rakeshjesadiya.com/get-all-regions-of-country-by-country-code-in-magento-2/
        $regionCollection = $this->countryFactory
                            ->getResourceCollection()
                            ->addCountryFilter('PH')->load();


        $arr = [];

        foreach ($regionCollection as $key => $value) {
            $arr[] = array(
                'value' => $value['region_id'],
                'label' => $value['default_name']
            );
        }
        // arsort($arr);
        return $arr;
    }
}