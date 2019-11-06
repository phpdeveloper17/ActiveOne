<?php

/**
 * DigitalCouponing Remainder Save Controller.
 * @category  Unilab
 * @package   Unilab_DigitalCouponing
 * @author    Reyson Aquino
 */
namespace Unilab\DigitalCouponing\Controller\Adminhtml\Remainder;


class Save extends \Magento\Backend\App\Action
{
    /**
     * @var \Unilab\DigitalCouponing\Model\RemainderFactory
     */
    protected $bannerFactory;

    protected $fileSystem;

    protected $fileUploaderFactory;
    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Unilab\DigitalCouponing\Model\RemainderFactory $bannerFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Unilab\DigitalCouponing\Model\RemainderFactory $remainderFactory
    ) 
    {
        $this->remainderFactory = $remainderFactory;
        parent::__construct($context);
    }

    /**
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function execute()
    {
        $data = $this->getRequest()->getPostValue();

        if (!$data) {
            $this->_redirect('unilabdc/remainder/add');
            return;
        }

        $rowData = $this->remainderFactory->create();
        try {
            if(isset($data['id'])) {
                $rowData = $rowData->load($data['id']);
            }
            $rowData->setRemainder($data['remainder_equivalent']);
            $rowData->setLetter($data['letter']);
            
            $rowData->save();

            $this->messageManager->addSuccess(__('Remainder Equivalent has been successfully saved.'));
        } catch (\Exception $e) {
            $this->messageManager->addError(__($e->getMessage()));
        }
        $this->_redirect('unilabdc/remainder/index');
    }

    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Unilab_Banners::save');
    }
}
