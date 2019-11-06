<?php
/**
 * Adminlogs Index Controller.
 * @category  Unilab
 * @package   Unilab_Adminlogs
 * @author    Kristian Claridad
 */
namespace Unilab\Adminlogs\Controller\Adminhtml\Adminlogs;

class Index extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    private $resultPageFactory;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
    }

    /**
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Unilab_Adminlogs::Adminlogs_list');
        $resultPage->getConfig()->getTitle()->prepend(__('Manage Admin Logs'));
        return $resultPage;
    }

    /**
     * Check Order Import Permission.
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Unilab_Adminlogs::Adminlogs_list');
    }
}
