<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Unilab\Inquiry\Model\Adminhtml\System\Config;

use Magento\Framework\App\Cache\TypeListInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Config\Value;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Math\Random;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;
use Magento\Framework\Serialize\Serializer\Json;

/**
 * Class CountryCreditCard
 */
class CustomSerialized extends Value
{
    /**
     * @var \Magento\Framework\Math\Random
     */
    protected $mathRandom;

    /**
     * @var \Magento\Framework\Serialize\Serializer\Json
     */
    private $serializer;

    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $config
     * @param \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList
     * @param \Magento\Framework\Math\Random $mathRandom
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb $resourceCollection
     * @param array $data
     * @param \Magento\Framework\Serialize\Serializer\Json $serializer
     */
    public function __construct(
        Context $context,
        Registry $registry,
        ScopeConfigInterface $config,
        TypeListInterface $cacheTypeList,
        Random $mathRandom,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = [],
        Json $serializer = null
    ) {
        $this->mathRandom = $mathRandom;
        $this->serializer = $serializer ?: \Magento\Framework\App\ObjectManager::getInstance()
            ->get(Json::class);
        parent::__construct($context, $registry, $config, $cacheTypeList, $resource, $resourceCollection, $data);
    }

    /**
     * Prepare data before save
     *
     * @return $this
     */
    public function beforeSave()
    {
        $value = $this->getValue();

        if (is_array($value)) {
            unset($value['__empty']);
        }
        // $result = [];
        // foreach ($value as $data) {
        //     if (empty($data['code']) || empty($data['name']) || empty($data['email']) || empty($data['subject']) || empty($data['template']) || empty($data['sortorder'])) {
        //         continue;
        //     }
        //     $country = $data['country_id'];
        //     if (array_key_exists($country, $result)) {
        //         $result[$country] = $this->appendUniqueCountries($result[$country], $data['cc_types']);
        //     } else {
        //         $result[$country] = $data['cc_types'];
        //     }
        // }
        $this->setValue($this->serializer->serialize($value));
        return $this;
    }

    /**
     * Process data after load
     *
     * @return $this
     */
    public function afterLoad()
    {
        if ($this->getValue()) {
            $value = $this->serializer->unserialize($this->getValue());
            if (is_array($value)) {
                $this->setValue($this->encodeArrayFieldValue($value));
            }
        }
        return $this;
    }

    /**
     * Encode value to be used in \Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray
     *
     * @param array $value
     * @return array
     */
    protected function encodeArrayFieldValue(array $value)
    {
        $result = [];
        foreach ($value as $key => $val) {
            $id = $this->mathRandom->getUniqueHash('_');
            $res = $key == "template"  && preg_match("/[a-z]/i", $val) ? 1: $val;
            // $result[$id] = ['country_id' => $country, 'cc_types' => $creditCardType];
            $result[$key] = $res;
        }
        return $result;
    }

    /**
     * Append unique countries to list of exists and reindex keys
     *
     * @param array $countriesList
     * @param array $inputCountriesList
     * @return array
     */
    private function appendUniqueCountries(array $countriesList, array $inputCountriesList)
    {
        $result = array_merge($countriesList, $inputCountriesList);
        return array_values(array_unique($result));
    }
}
