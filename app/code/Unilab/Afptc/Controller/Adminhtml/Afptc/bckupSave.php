<?php
/**
 * @category  Unilab
 * @package   Unilab_Afptc
 * @author    Kristian Claridad
 */
namespace Unilab\Afptc\Controller\Adminhtml\Afptc;

class Save extends \Magento\Backend\App\Action
{
    /**
     * @var \Unilab\Afptc\Model\AfptcFactory
     */
    protected $_objectManager;
    protected $authSession;
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Unilab\Afptc\Model\AfptcFactory $afptcFactory,
        \Magento\Backend\Model\Auth\Session $authSession
    ) {
        parent::__construct($context);
        $this->afptcFactory = $afptcFactory;
        $this->authSession = $authSession;
        $this->_objectManager = \Magento\Framework\App\ObjectManager::getInstance();
    }

    public function execute()
    {
        $newdata = array();
        $data = $this->getRequest()->getPostValue();

        
        
        if (!$data) {
            $this->_redirect('afptc/afptc/add_afptc');
            return;
        }
        try {
            $afptcData = $this->afptcFactory->create();
            $afptcData->setName($data['name']);
            $afptcData->setDescription($data['description']);
            $afptcData->setStatus(empty($data['status'])?0:1);
            $afptcData->setStore_ids(implode(',',$data['store_ids']));
            $afptcData->setCustomer_groups(implode(',',$data['customer_groups']));
            $afptcData->setDiscount($data['discount']);
            $afptcData->setPriority($data['priority']);
            $afptcData->setSimple_action($data['simple_action']);
            $afptcData->setDiscount_step($data['discount_step']);
            $afptcData->setN_product(0);
            $afptcData->setX_product(0);
            $afptcData->setX_autoincfreeprod(0);
            $afptcData->setAuto_incfreeprod(0);
            $afptcData->setShow_popup($data['show_popup']);
            $afptcData->setFree_shipping($data['free_shipping']);
            $afptcData->setProduct_id($data['product_id']);
            $afptcData->setCondition_serialized(serialize($data['rule']['conditions']));
            $afptcData->setStart_date($data['start_date']);
            $afptcData->setY_qty($data['y_qty']);
            $afptcData->setAuto_increment($data['auto_increment']);
            $afptcData->setTwo_promoitem($data['two_promoitem']);
            $afptcData->setEnd_date($data['end_date']);
            $afptcData->setStop_rules_processing($data['stop_rules_processing']);

            

            if (isset($data['id'])) {
                $afptcData->setEntityId($data['id']);
              
            }
            $afptcData->save();
            $this->messageManager->addSuccess(__('New Rule been successfully saved.'));
        } catch (\Exception $e) {
            $this->messageManager->addError(__($e->getMessage()));
        }
        $this->_redirect('afptc/afptc/index');
    }

    /**
     * @return bool
     */
    
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Unilab_Afptc::save');
    }
}
