<?php
/**
 * Movshipping View XML.
 * @category  Unilab
 * @package   Unilab_Movshipping
 * @author    Kristian Claridad
 */
namespace Unilab\Movshipping\Controller\Adminhtml\Shipping;

class Save extends \Magento\Backend\App\Action
{
    /**
     * @var \Unilab\Movshipping\Model\ShippingFactory
     */
    var $cityFactory;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Unilab\City\Model\CityFactory $cityFactory,
        \Unilab\Movshipping\Model\ShippingFactory $shippingFactory
    ) {
        parent::__construct($context);
        $this->cityFactory = $cityFactory;
        $this->shippingFactory = $shippingFactory;
        
    }

    public function execute()
    {
        $newdata = array();
        $data = $this->getRequest()->getPostValue();
        $data['listofcities'] = implode(',', $data['listofcities']);

        if (!$data) {
            $this->_redirect('movshipping/shipping/add_movshipping');
            return;
        }
        try {
            $movshippingData = $this->shippingFactory->create();
            $movshippingData->setData($data);

            if (isset($data['id'])) {
                $movshippingData->setEntityId($data['id']);
              
            }
            $movshippingData->save();
            $this->messageManager->addSuccess(__('Movshipping Group has been successfully saved.'));
        } catch (\Exception $e) {
            $this->messageManager->addError(__($e->getMessage()));
        }
        $this->_redirect('movshipping/shipping/index');
    }

    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return true;
    }
}
