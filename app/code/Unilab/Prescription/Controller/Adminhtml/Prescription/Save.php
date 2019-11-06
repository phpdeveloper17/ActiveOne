<?php

/**
 * Prescription Admin Cagegory Map Record Save Controller.
 * @category  Unilab
 * @package   Unilab_Prescription
 * @author    Unilab
 * @copyright Copyright (c) 2010-2016 Unilab Software Private Limited (https://Unilab.com)
 * @license   https://store.Unilab.com/license.html
 */
namespace Unilab\Prescription\Controller\Adminhtml\Prescription;


class Save extends \Magento\Backend\App\Action
{
    /**
     * @var \Unilab\Prescription\Model\PrescriptionFactory
     */
    protected $prescriptionFactory;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Unilab\Prescription\Model\PrescriptionFactory $prescriptionFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Unilab\Prescription\Model\PrescriptionFactory $prescriptionFactory,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
    ) 
    {
        $this->prescriptionFactory  = $prescriptionFactory;
        $this->jsonFactory = $resultJsonFactory;
        parent::__construct($context);
    }

    /**
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function execute()
    {
        $data = $this->getRequest()->getPostValue();
        
        // echo "<pre>";
        //     print_r($data);
        // echo "</pre>";
        try{
            
            $prescription = $this->prescriptionFactory->create();

            $prescription->setData($data);
            
            $prescription->save();
           
            $response = ['message' => 'Prescription has been saved.', 'status' => 'message message-success success'];

        } catch(\Exception $e) {

            $response = ['message' => $e->getMessage(), 'status' => 'message message-error error'];

        }
        
        return $this->jsonFactory->create()->setData($response);
    }

    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Unilab_Prescription::save');
    }
}
