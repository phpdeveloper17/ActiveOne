<?php
/**
 * @category  Unilab
 * @package   Unilab_Benefits->Pricelevel
 * @author    Kristian Claridad
 */
namespace Unilab\Benefits\Controller\Adminhtml\Pricelevel;

use Magento\Framework\Controller\ResultFactory;

class Add extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Framework\Registry
     */
    private $coreRegistry;

    /**
     * @var \Unilab\Grid\Model\GridFactory
     */
    private $pricelevelFactory;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry,
     * @param \Unilab\Benefits\Model\PricelevelFactory $pricelevelFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Unilab\Benefits\Model\PricelevelFactory $pricelevelFactory
    ) {
        parent::__construct($context);
        $this->coreRegistry = $coreRegistry;
        $this->pricelevelFactory = $pricelevelFactory;
    }

    /**
     * Mapped Grid List page.
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $rowId = (int) $this->getRequest()->getParam('id');
        $rowData = $this->pricelevelFactory->create();
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        if ($rowId) {
            $rowData = $rowData->load($rowId);
            $rowTitle = $rowData->getPriceLevelID();
            if (!$rowData->getId()) {
                $this->messageManager->addError(__('Pricelevel no longer exist.'));
                $this->_redirect('unilab_benefits/pricelevel/index');
                return;
            }
        }

        $this->coreRegistry->register('pricelevel_data', $rowData);
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $title = $rowId ? __('Edit Price Level').$rowTitle : __('Add New Price Level');
        $resultPage->getConfig()->getTitle()->prepend($title);
        return $resultPage;
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Unilab_Benefits::add_pricelevel');
    }
}
