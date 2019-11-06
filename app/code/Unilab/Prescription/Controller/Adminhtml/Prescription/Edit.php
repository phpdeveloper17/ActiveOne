<?php
/**
 * Banners Record Index Controller.
 * @category  Unilab
 * @package   Unilab_Banners
 * @author    Reyson Aquino
 */
namespace Unilab\Prescription\Controller\Adminhtml\Prescription;

use Magento\Framework\Controller\ResultFactory;
 
class Edit extends \Magento\Backend\App\Action
{
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\View\Element\Template $template,
        \Magento\Framework\Controller\Result\RawFactory $resultRawFactory,
        \Magento\Framework\Translate\InlineInterface $translateInline,
        \Unilab\Prescription\Model\PrescriptionFactory $prescriptionFactory
    )
    {
        $this->_registry = $registry;
        $this->template = $template;
        $this->_resultRawFactory = $resultRawFactory;
        $this->_translateInline = $translateInline;
        $this->prescriptionFactory = $prescriptionFactory;
        return parent::__construct($context);
    }
    
    public function execute()
    {
        $prescription = $this->prescriptionFactory->create();
        $prescription = $prescription->load($this->getRequest()->getParam('id'));
        $this->_registry->register('prescription', $prescription);

        $html = $this->template->getLayout()->createBlock('Unilab\Prescription\Block\Adminhtml\Prescription\Edit')->toHtml();
        $this->_translateInline->processResponseBody($html);
    
        $resultRaw = $this->_resultRawFactory->create();
        $resultRaw->setContents($html);
        return $resultRaw;
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Unilab_Prescription::edit');
    }
}