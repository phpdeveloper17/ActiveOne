<?php

namespace Unilab\Pricelist\Controller\Adminhtml\Promo\Catalog;

use Magento\Framework\Controller\ResultFactory;
use Magento\Backend\App\Action\Context;
use Magento\Ui\Component\MassAction\Filter;
use Unilab\Pricelist\Model\ResourceModel\CollectionFactory;

class MassDelete extends \Magento\Backend\App\Action
{
    /**
     * Massactions filter.
     * @var Filter
     */
    protected $_filter;

    /**
     * @var CollectionFactory
     */
    protected $_collectionFactory;

    /**
     * @param Context           $context
     * @param Filter            $filter
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(
        Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory
    ) {

        $this->_filter = $filter;
        $this->_collectionFactory = $collectionFactory;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        
        $data = $this->_collectionFactory->create();
        $collection = $this->_filter->getCollection($data);
        
        $ruleRepository = $this->_objectManager->get(
            \Magento\CatalogRule\Api\CatalogRuleRepositoryInterface::class
        );
        
        $recordDeleted = 0;
        foreach ($collection->getItems() as $record) {
            $record->setRuleId($record->getRuleId());
            $record->delete();
            // $ruleRepository->deleteById($record->getRuleId());
            $recordDeleted++;
        }
        $this->messageManager->addSuccess(__('A total of %1 record(s) have been deleted.', $recordDeleted));
        return $this->resultFactory->create(ResultFactory::TYPE_REDIRECT)->setPath('*/*/index');
    }

    /**
     * Check Category Map recode delete Permission.
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Unilab_Pricelist::massdelete');
    }
}
