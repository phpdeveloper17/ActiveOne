<?php
/**
 * @category  Unilab
 * @package   Unilab_Benefits->Productpricelist
 * @author    Kristian Claridad
 */
namespace Unilab\Benefits\Controller\Adminhtml\Productpricelist;

class Save extends \Magento\Backend\App\Action
{
    protected $_storeManager;
    /**
     * @var \Unilab\Benefits\Model\ProductpricelistFactory
     */
    protected $_objectManager;
    protected $authSession;
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Unilab\Benefits\Model\ProductpricelistFactory $productpricelistFactory,
        \Magento\Backend\Model\Auth\Session $authSession,
        \Magento\CatalogRule\Model\Rule $priceListFactory,
        \Magento\Store\Model\StoreFactory $storeManager
    ) {
        parent::__construct($context);
        $this->productpricelistFactory = $productpricelistFactory;
        $this->authSession = $authSession;
        $this->priceListFactory = $priceListFactory;
        $this->_objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $this->_storeManager = $storeManager;
    }

    public function execute()
    {
        $newdata = array();
        $postData = $this->getRequest()->getPostValue();
        $storeIds = implode(',',$postData['webstore_id']);
        $storeData = $this->_storeManager->create()->getCollection()->addFieldToFilter('store_id',['in' => $storeIds]);
        $storeData->getSelect()->group('website_id');
        $websiteId = array_values(array_column($storeData->getData(), 'website_id'));
        $website_ids = implode(',', $websiteId);
        
        $this->getRequest()->setPostValue('webstore_id',$storeIds);
        $this->getRequest()->setPostValue('website_id',$website_ids);
        $data = $this->getRequest()->getPostValue();
        // echo "<pre>";
        //     print_r($data);
        // echo "</pre>";
        // die;
        $this->getRequest()->setPostValue('qty_from', str_replace(',', '', $data['qty_from']));
        $this->getRequest()->setPostValue('qty_to', str_replace(',', '', $data['qty_to']));
        $this->getRequest()->setPostValue('unit_price', str_replace(',', '', $data['unit_price']));
        $this->getRequest()->setPostValue('discount_in_amount', str_replace(',', '', $data['discount_in_amount']));
        $this->getRequest()->setPostValue('discount_in_percent', str_replace(',', '', $data['discount_in_percent']));
        
        $data = $this->getRequest()->getPostValue();
        $data['uploaded_by'] = $this->authSession->getUser()->getUsername();
        if (!$data) {
            $this->_redirect('unilab_benefits/productpricelist/add_productpricelist');
            return;
        }
        try {
            $pricelistData = $this->priceListFactory->getCollection()->addFieldToFilter('name',$data['pricelist_id'])->getFirstItem();
            
            if($pricelistData->getRuleId()){
                $productpricelistData = $this->productpricelistFactory->create();
                
                $checkOrigData = $productpricelistData->getCollection()
                    ->addFieldToFilter('pricelist_id', $data['pricelist_id'])
                    ->addFieldToFilter('product_sku', $data['product_sku'])
                    ->count();
                $validate=false;
                $redirect = array();
                if(isset($data['id'])){ // check if id is set, this condition is for edit only
                    $checkDataExistEdit = $productpricelistData->getCollection()
                    ->addFieldToFilter('pricelist_id', $data['pricelist_id'])
                    ->addFieldToFilter('product_sku', $data['product_sku'])
                    ->addFieldToFilter('id', $data['id'])
                    ->count();
                    if($checkDataExistEdit > 0){ //count 1 > 0
                        $validate=false;
                    }elseif($checkOrigData > 0){
                        $validate=true;
                    }else{
                        $validate=false;
                    }
                }else{
                    if($checkOrigData > 0){ // check if company_code is existing. 
                        $validate=true;
                    }else{
                        $validate=false;
                    }
                }
                if($validate){
                    $this->messageManager->addError(__('Product Price List '.$data['pricelist_id'].' And SKU '.$data['product_sku'].' Already Exists!!!'));
                    $this->_redirect('unilab_benefits/productpricelist/add');
                }else{
                    if (isset($data['id'])) {
                        $productpricelistData->setEntityId($data['id']);
                    }
                    
                    $productpricelistData->setData($data); 
                    $productpricelistData->save();
                
                //End Save to rra_pricelistproduct Table
                $this->messageManager->addSuccess(__('Product Price list was successfully saved!.'));
                $this->_redirect('unilab_benefits/productpricelist/index');
                }
            }else{
                $this->messageManager->addError(__('Price list does not exists!.'));
            }
        } catch (\Exception $e) {
           
            $this->messageManager->addError(__($e->getMessage()));
        }
        $this->_redirect('unilab_benefits/productpricelist/index');
    }

    /**
     * @return bool
     */
    
    protected function _isAllowed()
    {
        return true;
    }
}
