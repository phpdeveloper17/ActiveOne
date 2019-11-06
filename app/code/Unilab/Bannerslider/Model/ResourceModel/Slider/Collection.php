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
 * @package     Unilab_Bannerslider
 * @copyright   Copyright (c) 2012 Unilab (http://www.Unilab.com/)
 * @license     http://www.Unilab.com/license-agreement.html
 */

namespace Unilab\Bannerslider\Model\ResourceModel\Slider;

/**
 * Slider Collection
 * @category Unilab
 * @package  Unilab_Bannerslider
 * @module   Bannerslider
 * @author   Unilab Developer
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{

    protected $_idFieldName = 'slider_id';
    /**
     * store view id.
     *
     * @var int
     */
    protected $_storeViewId = null;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * added table
     * @var array
     */
    protected $_addedTable = [];

    /**
     * @var bool
     */
    protected $_isLoadSliderTitle = FALSE;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\Timezone
     */
    protected $_stdTimezone;

    /**
     * _contruct
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Unilab\Bannerslider\Model\Slider', 'Unilab\Bannerslider\Model\ResourceModel\Slider');
    }

    /**
     * @param \Magento\Framework\Data\Collection\EntityFactoryInterface    $entityFactory
     * @param \Psr\Log\LoggerInterface                                     $logger
     * @param \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy
     * @param \Magento\Framework\Event\ManagerInterface                    $eventManager
     * @param \Zend_Db_Adapter_Abstract                                    $connection
     * @param \Magento\Framework\Model\ResourceModel\Db\AbstractDb              $resource
     */
    public function __construct(
        \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Stdlib\DateTime\Timezone $stdTimezone,
        $connection = null,
        \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource = null
    ) {
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $connection, $resource);
        $this->_storeManager = $storeManager;
        $this->_stdTimezone = $stdTimezone;

    }

}
