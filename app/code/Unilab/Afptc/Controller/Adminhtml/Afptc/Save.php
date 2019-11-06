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
        try {
            $rule = $this->_objectManager->create('Unilab\Afptc\Controller\Adminhtml\Afptc\Edit')->_initRule();
            $request = new \Magento\Framework\DataObject(
                $this->_filterDateTime(
                    $this->getRequest()->getParams(), array('start_date', 'end_date')
                )
            );
            
            $request->sety_qty($this->getRequest()->getParam('y_qty'));
            $request->setx_qty($this->getRequest()->getParam('x_qty'));
            $request->setStore_ids(implode(',', $request['store_ids']));
            $request->setCustomer_groups(implode(',', $request['customer_groups']));
            
            $this
                // ->_prepareDates($request)
                ->_prepareConditions($request)
            ;
            
            $rule
                ->addData($request->getData())
                ->loadPost($request->getData())
                ->save()
            ;
           
            if (!$rule->getProductId() && $rule->getSimpleAction() == \Unilab\Afptc\Model\Rule::BY_PERCENT_ACTION) {
                $this->_objectManager->create("\Magento\Framework\Message\ManagerInterface")->addNotice($this->__('No action product specified'));
            }
            
            $this->_objectManager->create("\Magento\Framework\Message\ManagerInterface")->addSuccess(__('Rule successfully saved'));
       
        } catch (Exception $e) {
            $request->setBack(true);
            $this->_prepareDates($request);
            $this->_objectManager->create("\Magento\Framework\Message\ManagerInterface")
                ->addError($e->getMessage())
                ->setFormActionData($request->getData())
            ;
        }

        if ($request->getBack()) {
            return $this->_redirect('*/*/edit', array('id' => $rule->getId(), 'tab' => $request->getTab()));
        }
        return $this->_redirect('*/*/');
    }

    /**
     * @return bool
     */
    public function _filterDateTime($post_arr=array(),$fieldPost=array())
    {
        $this->timezone = $this->_objectManager->create("\Magento\Framework\Stdlib\DateTime\TimezoneInterface");
        $postArray[] = $post_arr;
        foreach($fieldPost as $f){
            $fieldvalue = array_column($postArray, $f);
            $convertdate = (new \DateTime())->setTimestamp(strtotime($fieldvalue[0]));
            $d = $convertdate->format(\Magento\Framework\Stdlib\DateTime::DATETIME_PHP_FORMAT);
            $post_arr[$f] = $d;
        }
        return $post_arr;
    }
    
    protected function _prepareDates(\Magento\Framework\DataObject $request)
    {
        if (null !== $request->getStartDate()) {
            $request->setStartDate($this->_objectManager->create("\Magento\Framework\Stdlib\DateTime\DateTime")
                ->gmtDate(null, $request->getStartDate())
            );
        }
        if (null !== $request->getEndDate()) {
            $request->setStartDate($this->_objectManager->create("\Magento\Framework\Stdlib\DateTime\DateTime")
                ->gmtDate(null, $request->getEndDate())
            );
        }
        return $this;
    }
    protected function _prepareConditions(\Magento\Framework\DataObject $request)
    {
        $data = $request->getData();
        if (isset($data['rule']['conditions'])) {
            $data['conditions'] = $data['rule']['conditions'];
            unset($data['rule']);
            $request->setData($data);
        }
        return $this;
    }
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Unilab_Afptc::save');
    }
}
