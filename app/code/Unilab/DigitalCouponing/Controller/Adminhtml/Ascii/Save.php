<?php

/**
 * DigitalCouponing Admin Cagegory Map Record Save Controller.
 * @category  Unilab
 * @package   Unilab_Banners
 * @author    Unilab
 * @copyright Copyright (c) 2010-2016 Unilab Software Private Limited (https://Unilab.com)
 * @license   https://store.Unilab.com/license.html
 */
namespace Unilab\DigitalCouponing\Controller\Adminhtml\Ascii;


class Save extends \Magento\Backend\App\Action
{
    /**
     * @var \Unilab\DigitalCouponing\Model\AsciiFactory
     */
    protected $bannerFactory;

    protected $fileSystem;

    protected $fileUploaderFactory;
    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Unilab\DigitalCouponing\Model\AsciiFactory $bannerFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Unilab\DigitalCouponing\Model\AsciiFactory $asciiFactory
    ) 
    {
        $this->asciiFactory = $asciiFactory;
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
            $this->_redirect('unilabdc/ascii/add');
            return;
        }
        $rowData = $this->asciiFactory->create();
        try {
            if(isset($data['id'])) {
                $rowData = $rowData->load($data['id']);
            }
            $rowData->setAscii($data['ascii_equivalent']);
            $rowData->setLetter($data['letter']);
            
            $rowData->save();

            $this->messageManager->addSuccess(__('ASCII Equivalent has been successfully saved.'));
        } catch (\Exception $e) {
            $this->messageManager->addError(__($e->getMessage()));
        }
        $this->_redirect('unilabdc/ascii/index');
    }

    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Unilab_DigitalCouponing::save_asciiequivalent');
    }
}
