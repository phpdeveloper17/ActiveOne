<?php

namespace Unilab\Prescription\Block\Adminhtml\Grid\Column\Renderer;

use Magento\Framework\UrlInterface;


class View extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
    protected $actionUrlBuilder;

    public function __construct(
        \Magento\Backend\Block\Context $context,
        UrlInterface $actionUrlBuilder,
        \Psr\Log\LoggerInterface $logger,
        array $data = []
    ) {
        $this->actionUrlBuilder = $actionUrlBuilder;
        $this->logger = $logger;
        parent::__construct($context, $data);
    }

    public function render(\Magento\Framework\DataObject $row) {
        $value = $row->getData($this->getColumn()->getIndex());
        // $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/test.log');
        // $logger = new \Zend\Log\Logger();
        // $logger->addWriter($writer);
        // $logger->info($value);

        // $id = $row->getData($this->getColumn()->getIndex());
        return '<a class="open-prescription-modal" data-id="'
                .$value.'" href="#">' . __('View') . '</a>';        

    }
}