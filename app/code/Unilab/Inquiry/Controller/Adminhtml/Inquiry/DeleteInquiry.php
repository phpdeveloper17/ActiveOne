<?php
/**
 * @category  Unilab
 * @package   Unilab_City
 * @author    Ron Mark Peroso Rudas
 */
namespace Unilab\Inquiry\Controller\Adminhtml\Inquiry;

use Magento\Framework\Controller\ResultFactory;

class DeleteInquiry extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Framework\Registry
     */
    private $coreRegistry;

    /**
     * @var \Unilab\Grid\Model\GridFactory
     */
    private $gridFactory;

    private $dateFormatter;

    private $departmentHelper;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry,
     * @param \Unilab\Inquiry\Model\InquiryFactory $gridFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Unilab\Inquiry\Model\InquiryFactory $inquiryFactory,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $dateFormatter,
        \Unilab\Inquiry\Helper\Data $departmentHelper
    ) {
        parent::__construct($context);
        $this->coreRegistry = $coreRegistry;
        $this->gridFactory = $inquiryFactory;
        $this->dateFormatter = $dateFormatter;
        $this->departmentHelper = $departmentHelper;
    }

    /**
     * Mapped Grid List page.
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {

        $rowId = (int) $this->getRequest()->getParam('id');

        $inquiryModel = $this->gridFactory->create();


        if ($rowId) {
            try {

                $inquiryModel = $inquiryModel->load($rowId);
                $inquiryModel->delete();

                $this->messageManager->addSuccess(__("Inquiry was successfully deleted"));
                $this->_redirect('unilab_inquiry/inquiry/index');

            } catch (\Exception $e) {


                $this->messageManager->addError($e->getMessage());
                $this->_redirect('*/*/view', array('id' => $rowId));

            }

        }

        return;
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Unilab_Inquiry::delete_inquiry');
    }
}
