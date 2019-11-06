<?php
/**
 * @category  Unilab
 * @package   Unilab_Benefits->Productpricelist
 * @author    Kristian Claridad
 */
namespace Unilab\Benefits\Controller\Adminhtml\Productpricelist;

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
    private $productpricelistFactory;
    protected $catalogruleInterface;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry,
     * @param \Unilab\City\Model\CityFactory $gridFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Unilab\Benefits\Model\ProductpricelistFactory $productpricelistFactory
    ) {
        parent::__construct($context);
        $this->coreRegistry = $coreRegistry;
        $this->productpricelistFactory = $productpricelistFactory;
    }

    /**
     * Mapped Grid List page.
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $catalogData = array();
        $rowId = (int) $this->getRequest()->getParam('id');
        $rowData = $this->productpricelistFactory->create();
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        if ($rowId) {
            $rowData = $rowData->load($rowId);
            $rowTitle = $rowData->getPriceListID();
            if (!$rowData->getId()) {
                $this->messageManager->addError(__('Product Price List no longer exist.'));
                $this->_redirect('unilab_benefits/productpricelist/index');
                return;
            }
        }
      
        $this->coreRegistry->register('productpricelist_data', $rowData);
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $title = __('Edit Product Price List ');
        $resultPage->getConfig()->getTitle()->prepend($title);
        return $resultPage;
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Unilab_Benefits::edit_productpricelist');
    }
}
