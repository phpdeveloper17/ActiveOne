<?php
/**
 * DigitalCouponing Remainder MassDelete Controller.
 * @category  Unilab
 * @package   Unilab_DigitalCouponing
 * @author    Reyson Aquino
 */
namespace Unilab\DigitalCouponing\Controller\Adminhtml\Remainder;

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
        \Unilab\DigitalCouponing\Model\RemainderFactory $remainderFactory,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
    ) {
        parent::__construct($context);
        $this->coreRegistry = $coreRegistry;
        $this->remainderFactory = $remainderFactory;
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
                $remainder = $this->remainderFactory->create()->load($id);
                $remainder->delete();
            endforeach; 

            $this->messageManager->addSuccess(__('A total of %1 record(s) have been deleted.', count($data['selected'])));
            
        } catch(\Exception $e) {
            $this->messageManager->addError($e->getMessage());
        }
        
        $this->_redirect('unilabdc/remainder/index');
        
        return;
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Unilab_DigitalCouponing::add_asciiequivalent');
    }
}
