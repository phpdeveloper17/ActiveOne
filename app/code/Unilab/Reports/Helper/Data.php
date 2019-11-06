<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Reports data helper
 */
namespace Unilab\Reports\Helper;

use Magento\Framework\Data\Collection;
use Magento\Framework\Stdlib\DateTime;

/**
 * @api
 * @since 100.0.2
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    const REPORT_PERIOD_TYPE_DAY = 'day';

    const REPORT_PERIOD_TYPE_MONTH = 'month';

    const REPORT_PERIOD_TYPE_YEAR = 'year';

    /**
     * Item factory
     *
     * @var \Magento\Reports\Model\ItemFactory
     */
    protected $_itemFactory;
    protected $reportsFactory;

    /**
     * Constructor
     *
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Reports\Model\ItemFactory $itemFactory
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Unilab\Reports\Model\ItemFactory $itemFactory,
        \Unilab\Reports\Model\Reports $reportsFactory
    ) {
        parent::__construct($context);
        $this->_itemFactory = $itemFactory;
        $this->reportsFactory = $reportsFactory;
    }

    /**
     * Retrieve array of intervals
     *
     * @param string $from
     * @param string $to
     * @param string $period
     * @return array
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function getIntervals($from, $to, $period = self::REPORT_PERIOD_TYPE_DAY)
    {
        $intervals = [];
        if (!$from && !$to) {
            return $intervals;
        }

        $dateStart = new \DateTime($from);
        $dateEnd = new \DateTime($to);
        while ($dateStart->diff($dateEnd)->invert == 0) {
            switch ($period) {
                case self::REPORT_PERIOD_TYPE_DAY:
                    $intervals[] = $dateStart->format('Y-m-d');
                    $dateStart->add(new \DateInterval('P1D'));
                    break;
                case self::REPORT_PERIOD_TYPE_MONTH:
                    $intervals[] = $dateStart->format('Y-m');
                    $dateStart->add(new \DateInterval('P1M'));
                    break;
                case self::REPORT_PERIOD_TYPE_YEAR:
                    $intervals[] = $dateStart->format('Y');
                    $dateStart->add(new \DateInterval('P1Y'));
                    break;
            }
        }
        return $intervals;
    }

    /**
     * Add items to interval collection
     *
     * @param Collection $collection
     * @param string $from
     * @param string $to
     * @param string $periodType
     * @return void
     */
    public function prepareIntervalsCollection($collection, $from, $to, $periodType = self::REPORT_PERIOD_TYPE_DAY)
    {
        $intervals = $this->getIntervals($from, $to, $periodType);

        foreach ($intervals as $interval) {
            $item = $this->_itemFactory->create();
            $item->setPeriod($interval);
            $item->setIsEmpty();
            $collection->addItem($item);
        }
    }
    public function _getReport($report_id = 1)
    {
        $this->_objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        if (isset($this->_report)) {
            return $this->_report;
        }

        $report = $this->reportsFactory;
        $report->load($report_id);

        $this->_report = $report;
        return $this->_report;
    }
    public function filenameExport($report_id){
        $title = str_replace(' ','_', $this->_getReport($report_id)->getTitle());
        $mdYhis = date('mdYhis');
        $fileName = $title;
        return $fileName;
    }
}
