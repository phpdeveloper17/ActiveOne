<?php
/**
 * @category  Unilab
 * @package   Unilab_Movshipping
 * @author    Kristian Claridad
 */
namespace Unilab\Movshipping\Controller\Adminhtml\Shipping;

use Magento\Framework\Controller\ResultFactory;

class Edit extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Framework\Registry
     */
    private $coreRegistry;

    /**
     * @var \Unilab\Grid\Model\GridFactory
     */
    private $gridFactory;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry,
     * @param \Unilab\City\Model\CityFactory $gridFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Unilab\Movshipping\Model\ShippingFactory $shippingFactory
    ) {
        parent::__construct($context);
        $this->coreRegistry = $coreRegistry;
        $this->shippingFactory = $shippingFactory;
    }

    /**
     * Mapped Grid List page.
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $rowId = (int) $this->getRequest()->getParam('id');
        $rowData = $this->shippingFactory->create();
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        if ($rowId) {
            $rowData = $rowData->load($rowId);
            $rowTitle = $rowData->getMovGroup();
            if (!$rowData->getMovId()) {
                $this->messageManager->addError(__('Movshipping no longer exist.'));
                $this->_redirect('movshipping/shipping/index');
                return;
            }
        }

        $this->coreRegistry->register('row_data', $rowData);
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $title = __('Edit ').$rowTitle;
        $resultPage->getConfig()->getTitle()->prepend($title);
        return $resultPage;
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Unilab_Movshipping::edit_movshipping');
    }
}
