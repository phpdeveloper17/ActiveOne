<?php
/**
 * DigitalCouponing Ascii MassDelete Controller.
 * @category  Unilab
 * @package   Unilab_DigitalCouponing
 * @author    Reyson Aquino
 */
namespace Unilab\DigitalCouponing\Controller\Adminhtml\Ascii;

use Magento\Framework\Controller\ResultFactory;

class MassDelete extends \Magento\Backend\App\Action
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
     * @param \Unilab\DigitalCouponing\Model\AsciiFactory $gridFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Unilab\DigitalCouponing\Model\AsciiFactory $asciiFactory,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
    ) {
        parent::__construct($context);
        $this->coreRegistry = $coreRegistry;
        $this->asciiFactory = $asciiFactory;
        $this->resultJsonFactory = $resultJsonFactory;
    }

    /**
     * Mapped Grid List page.
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $data   = $this->getRequest()->getPost();
        $result = $this->resultJsonFactory->create();
        try {
            foreach($data['selected'] as $id) :
                $ascii = $this->asciiFactory->create()->load($id);
                $ascii->delete();
            endforeach; 

            $this->messageManager->addSuccess(__('A total of %1 record(s) have been deleted.', count($data['selected'])));
            
        } catch(\Exception $e) {
            $this->messageManager->addError($e->getMessage());
        }
        
        $this->_redirect('unilabdc/ascii/index');
        
        return;
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Unilab_DigitalCouponing::massdelete_asciiequivalent');
    }
}
