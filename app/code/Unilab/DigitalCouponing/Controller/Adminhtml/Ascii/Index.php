<?php
/**
 * DigitalCouponing Ascii Index Controller.
 * @category  Unilab
 * @package   Unilab_DigitalCouponing
 * @author    Reyson Aquino
 */
namespace Unilab\DigitalCouponing\Controller\Adminhtml\Ascii;

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
     * Mapped eBay Order List page.
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();
        // $resultPage->setActiveMenu('Unilab_Grid::grid_list');
        $resultPage->getConfig()->getTitle()->prepend(__('ASCII Equivalents'));
        return $resultPage;
    }

    /**
     * Check Order Import Permission.
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return true;//$this->_authorization->isAllowed('Unilab_Grid::grid_list');
    }
}
