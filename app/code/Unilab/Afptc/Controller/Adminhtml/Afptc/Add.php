<?php
/**
 * @category  Unilab
 * @package   Unilab_Afptc
 * @author    Kristian Claridad
 */
namespace Unilab\Afptc\Controller\Adminhtml\Afptc;

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
    private $gridFactory;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry,
     * @param \Unilab\Afptc\Model\AfptcFactory $gridFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Unilab\Afptc\Model\AfptcFactory $afptcFactory
    ) {
        parent::__construct($context);
        $this->coreRegistry = $coreRegistry;
        $this->afptcFactory = $afptcFactory;
    }

    /**
     * Mapped Grid List page.
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $this->_forward('edit');
        // $rowId = (int) $this->getRequest()->getParam('id');
        // $rowData = $this->afptcFactory->create();
        // /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        // if ($rowId) {
        //     $rowData = $rowData->load($rowId);
        //     $rowTitle = $rowData->getPriceLevelId();
        //     if (!$rowData->getPriceId()) {
        //         $this->messageManager->addError(__('afptc no longer exist.'));
        //         $this->_redirect('afptc/afptc/index');
        //         return;
        //     }
        // }

        // $this->coreRegistry->register('awafptc_rule', $rowData);
        // $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        // $title = $rowId ? __('Edit Rule').$rowTitle : __('New Rule');
        // $resultPage->getConfig()->getTitle()->prepend($title);
        // return $resultPage;
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Unilab_Afptc::add_afptc');
    }
}
