<?php

/**
 * Unilab
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Unilab.com license that is
 * available through the world-wide-web at this URL:
 * http://www.Unilab.com/license-agreement.html
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Unilab
 * @package     Unilab_Benefits
 */

namespace Unilab\Benefits\Ui\Component\Listing\TenderType\Column;

use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\Element\UiComponent\ContextInterface;

/**
 * @category Unilab
 * @package  Unilab_Benefits
 * @module   TenderType
 * @author   Unilab Developer
 */
class ShippingGrid extends \Unilab\Benefits\Ui\Component\Listing\TenderType\Column\AbstractColumn
{

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * Constructor.
     *
     * @param ContextInterface   $context
     * @param UiComponentFactory $uiComponentFactory
     * @param array              $components
     * @param array              $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\Config\ScopeConfigInterface $appConfigScopeConfigInterface,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);
        $this->_storeManager = $storeManager;
        $this->_appConfigScopeConfigInterface = $appConfigScopeConfigInterface;
    }

    /**
     * prepare item.
     *
     * @param array $item
     *
     * @return array
     */
    protected function _prepareItem(array & $item)
    {
            if (isset($item[$this->getData('name')])) {
                $shippingTitle = $this->_appConfigScopeConfigInterface
                ->getValue('carriers/' . $item[$this->getData('name')] . '/title');
                $item[$this->getData('name')] = $shippingTitle;
            } else {
                $item[$this->getData('name')] = '';
            }

        return $item;
    }
}
