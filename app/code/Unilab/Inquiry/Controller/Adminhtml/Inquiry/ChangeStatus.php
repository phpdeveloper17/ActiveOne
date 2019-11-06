<?php
/**
 * Unilab Grid Record Delete Controller.
 * @category  Unilab
 * @package   Unilab_Grid
 * @author    Unilab
 * @copyright Copyright (c) 2010-2017 Unilab Software Private Limited (https://Unilab.com)
 * @license   https://store.Unilab.com/license.html
 */
namespace Unilab\Inquiry\Controller\Adminhtml\Inquiry;

use Magento\Framework\Controller\ResultFactory;
use Magento\Backend\App\Action\Context;
use Magento\Ui\Component\MassAction\Filter;
use Unilab\Inquiry\Model\Inquiry;
use Unilab\Inquiry\Model\ResourceModel\Inquiry\CollectionFactory;

class ChangeStatus extends \Magento\Backend\App\Action
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
        CollectionFactory $collectionFactory,
        Inquiry $inquiryModel
    ) {

        $this->_filter = $filter;
        $this->_collectionFactory = $collectionFactory;
        $this->_inquiryModel = $inquiryModel;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        $requestParams = $this->getRequest()->getParams();
        // echo "<pre>";
        // var_dump($requestParams['is_read']);
        // echo "</pre>";
        $collection = $this->_filter->getCollection($this->_collectionFactory->create());
        $recordUpdated = 0;
        foreach ($collection->getItems() as $record) {

            $record = $this->_inquiryModel->load($record->getId());
            $record->setIsRead($requestParams['is_read']);
            $record->save();
            // $record->setId($record->getId());
            // $record->delete();
            $recordUpdated++;
        }
        $this->messageManager->addSuccess(__('A total of %1 record(s) have been updated.', $recordUpdated));
        return $this->resultFactory->create(ResultFactory::TYPE_REDIRECT)->setPath('*/*/index');
    }

    /**
     * Check Category Map recode delete Permission.
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Unilab_Inquiry::mass_delete');
    }
}
