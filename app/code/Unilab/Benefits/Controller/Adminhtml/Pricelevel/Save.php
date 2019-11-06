<?php
/**
 * @category  Unilab
 * @package   Unilab_Benefits->Benefits
 * @author    Kristian Claridad
 */
namespace Unilab\Benefits\Controller\Adminhtml\Pricelevel;

class Save extends \Magento\Backend\App\Action
{
    /**
     * @var \Unilab\Benefits\Model\PricelevelFactory
     */
    protected $_objectManager;
    protected $authSession;
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Unilab\Benefits\Model\PricelevelFactory $pricelevelFactory,
        \Magento\Backend\Model\Auth\Session $authSession
    ) {
        parent::__construct($context);
        $this->pricelevelFactory = $pricelevelFactory;
        $this->authSession = $authSession;
        $this->_objectManager = \Magento\Framework\App\ObjectManager::getInstance();
    }

    public function execute()
    {
        $newdata = array();
        $data = $this->getRequest()->getPostValue();
        
        if (!$data) {
            $this->_redirect('unilab_benefits/pricelevel/add_pricelevel');
            return;
        }
        try {
            
            $pricelistData = $this->pricelevelFactory->create();
            $checkCompanyCodeExist = $pricelistData->getCollection()->addFieldToFilter('price_level_id', $data['price_level_id'])->count();
            $validate=false;
            $redirect = array();
            if(isset($data['id'])){ // check if id is set, this condition is for edit only
                $checkDataExistEdit = $pricelistData->getCollection()
                ->addFieldToFilter('price_level_id', $data['price_level_id'])
                ->addFieldToFilter('id', $data['id'])
                ->count();
                if($checkDataExistEdit > 0){ //count 1 > 0
                    $validate=false;
                }elseif($checkCompanyCodeExist > 0){
                    $validate=true;
                }else{
                    $validate=false;
                }
            }else{
                if($checkCompanyCodeExist > 0){ // check if company_code is existing. 
                    $validate=true;
                }else{
                    $validate=false;
                }
            }
            if($validate){
                $this->messageManager->addError(__('Price Level '.$data['price_level_id'].' Already Exists!!!'));
                return $this->_redirect('unilab_benefits/pricelevel/add');
            }else{
                if (isset($data['id'])) {
                    $pricelistData->setEntityId($data['id']);
                }
                $pricelistData->setData($data); 
                $pricelistData->save();
                //End Save to wspi_pricelist Table
                $this->messageManager->addSuccess(__('Price Level was successfully saved!.'));
                return $this->_redirect('unilab_benefits/pricelevel/index');
            }
        } catch (\Exception $e) {
            $this->messageManager->addError(__($e->getMessage()));
        }
        $this->_redirect('unilab_benefits/pricelevel/index');
    }

    /**
     * @return bool
     */
    
    protected function _isAllowed()
    {
        return true;
    }
}
