<?php
namespace Unilab\Afptc\Controller\Adminhtml\Afptc;

use Magento\Backend\App\Action;
 
class Delete extends Action
{
    protected $_model;
 
    /**
     * @param Action\Context $context
     * @param \Maxime\Jobs\Model\Department $model
     */
    public function __construct(
        Action\Context $context,
        \Unilab\Afptc\Model\Afptc $model
    ) {
        parent::__construct($context);
        $this->_model = $model;
    }
 
    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Unilab_Afptc::delete');
    }
 
    /**
     * Delete action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('id');
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($id) {
            try {
                //delete data from wspi_afptc
                $model = $this->_model;
                $model->load($id);
                $model->delete();
                $this->messageManager->addSuccess(__('Price List deleted'));
                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
                return $resultRedirect->setPath('afptc/afptc/edit', ['id' => $id]);
            }
        }
        $this->messageManager->addError(__('Price List does not exist'));
        return $resultRedirect->setPath('*/*/');
    }
}