<?php
/**
 * @category  Unilab
 * @package   Unilab_City
 * @author    Ron Mark Peroso Rudas
 */
namespace Unilab\Inquiry\Controller\Adminhtml\Inquiry;

use Magento\Framework\Controller\ResultFactory;

class View extends \Magento\Backend\App\Action
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

        $rowData = $this->gridFactory->create();
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        if ($rowId) {
            $rowData = $rowData->load($rowId);

            $rowTitle = $rowData->getEmailAddress();
            $inquiryDate = $this->dateFormatter->date(new \DateTime($rowData->getCreatedTime()))->format('M d, Y h:i:s A');
            $department = $this->departmentHelper->getDepartmentByCodeCstm($rowData->getDepartment());
            $inquiryDept = !empty($department) ? $department['name'] : "";

            if (!$rowData->getInquiryId()) {
                $this->messageManager->addError(__('Inquiry no longer exists.'));
                $this->_redirect('unilab_inquiry/inquiry/index');
                return;
            }
        }

        $this->coreRegistry->register('row_data', $rowData);
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $title = __('Inquiry For ('.$inquiryDept.') '. "From " .$rowTitle . " | " . $inquiryDate);
        $resultPage->getConfig()->getTitle()->prepend($title);
        return $resultPage;
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Unilab_Inquiry::view_inquiry');
    }
}
