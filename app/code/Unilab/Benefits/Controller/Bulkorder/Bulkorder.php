<?php

namespace Unilab\Benefits\Controller\Bulkorder;

class Bulkorder extends \Magento\Framework\App\Action\Action
{
    protected $resultJsonFactory;
    protected $resourceConnection;
    protected $customerSession;
    protected $checkoutCart;
	protected $catalogProduct;
    protected $catalogStockInterface;
	protected $checkoutSession;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Framework\App\ResourceConnection $resourceConnection,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Checkout\Model\Cart $checkoutCart,
		\Magento\Catalog\Model\Product $catalogProduct,
		\Magento\CatalogInventory\Api\StockStateInterface $catalogStockInterface,
		\Magento\Checkout\Model\Session $checkoutSession,
		\Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
		\Magento\Framework\Data\Form\FormKey $formKey
    ) {
        
        $this->resultJsonFactory = $resultJsonFactory;
        $this->resourceConnection = $resourceConnection;
        $this->customerSession = $customerSession;
        $this->checkoutCart = $checkoutCart;
		$this->catalogProduct = $catalogProduct;
		$this->catalogStockInterface = $catalogStockInterface;
		$this->checkoutSession = $checkoutSession;
		$this->productRepository = $productRepository;
		$this->formKey = $formKey;
		parent::__construct($context);
    }

    public function execute()
    {
		$arr_result = array();
		$fullpath = $_FILES['upload_csv']['tmp_name'];
        $filename = $_FILES['upload_csv']['name'];
        $size = $_FILES['upload_csv']['size'];
		$ext = pathinfo($filename, PATHINFO_EXTENSION);
        
        $request = $this->getRequest()->getPostValue();
		$file = $_FILES['upload_csv']['tmp_name'];
		if(strtolower($ext) != 'csv'){
			$arr_result = array('result' => 'error', 'msg' => $filename.' Invalid File Type');
		}elseif( $this->checkProductIfExists($file) > 0 ){
	    	$arr_result = array('result' => 'error', 'msg' => 'Invalid SKU for item #'.$this->checkProductIfExists($file).'. Kindly update csv file and try again.');
        }else{
            if( $this->checkProductIfEnable($file) > 0){
	    		$arr_result = array('result' => 'error', 'msg' => 'No available stocks for item #'.$this->checkProductIfEnable($file).'. Kindly update csv file and try again.');
			}
	    	// else if( $this->checkProductIfRx($file) > 0 ){
	    	// 	$arr_result = array('result' => 'error', 'msg' => 'Prescription is required for item #'.$this->checkProductIfRx($file).'. Kindly update csv file and try again.');
            // }
            // else if( $this->checkProductDuplicate($file) > 0 ){
	    	// 	$arr_result = array('result' => 'error', 'msg' => 'Duplicate items found. Kindly update csv file and try again.');
	    	// }
	    	else
	    	{
				try{
					$result = $this->extractFile($file,'validate');
					if($result['line']  > 0 ){
						$arr_result = array('result' => 'error', 'msg' => 'Quantity for item #'.$result['line'].' must be divisible by '.$result['moq'].'. Kindly update csv file and try again.');
					}
					elseif($this->chckQtyisGreaterthanStockQty($file) > 0){
						$arr_result = array('result' => 'error', 'msg' => 'We don\'t have as many stocks for item #'.$this->chckQtyisGreaterthanStockQty($file).'. Kindly update csv file and try again.');
					}
					else
					{
						if ($this->extractFile($file,'save') == 0) {
							$arr_result = array('result' => 'success', 'msg' => '');
						}
						else
						{
							$arr_result = array('result' => 'error', 'msg' => 'Oops! transaction failed. Please try again.');
						}
					}
				}catch (\Magento\Framework\Exception\LocalizedException $e) {
					$arr_result = array('result' => 'error', 'msg' => $e->getMessage());
				}
	    	}
        }
        echo json_encode($arr_result);
    
	}
	public function chckQtyisGreaterthanStockQty($file){
		$handle = fopen($file,"r"); 
		$data = array();
	   	$ctr = 0;
		$result = 0;
		$s='';
		while (($data = fgetcsv($handle,1000,",")) !== FALSE) {
			if($ctr > 0){
				$_sku = $data[0];
                $id = $this->catalogProduct->getIdBySku($_sku);
				$product = $this->catalogProduct->load($id);
				
				$product_stock = $this->catalogStockInterface->getStockQty($id);
				if($data[2] < $product_stock){
					// $s=$data[2].' < '.$product_stock;
				}else{
					return $ctr;
				}
				
			}
			$ctr++;
		}
		return $result;
	}
    public function checkProductIfExists($file){

		$handle = fopen($file,"r"); 
		$data = array();
	   	$ctr = 0;
	   	$result = 0;
	    while (($data = fgetcsv($handle,1000,",")) !== FALSE) { 
	     	if($ctr > 0)
	     	{
	     		$_sku = $data[0];
	     		$id = $this->catalogProduct->getIdBySku($_sku);
				if ($id) {
				   //sku exists
				}
				else {
				   return $ctr;
				}
	     	}
	        $ctr++;
	    }

	    return $result;
	}
    public function checkProductIfEnable($file){

        $handle = fopen($file,"r"); 

        $ctr = 0;
        $result = 0;
        while (($data = fgetcsv($handle,1000,",")) !== FALSE) {
			
            if($ctr > 0)
            {
                $_sku = $data[0];
                $id = $this->catalogProduct->getIdBySku($_sku);
				$product = $this->catalogProduct->load($id);
				
				$product_stock = $this->catalogStockInterface->getStockQty($id);
				
                if ($product->getStatus() == 1 && $product_stock > 0) {
				//product enable
				
                }
                else {
                return $ctr;
                }
            }
            $ctr++;
        }
        return $result;
    }
    public function checkProductIfRx($file){

		$handle = fopen($file,"r"); 

	   	$ctr = 0;
	   	$result = 0;
	    while (($data = fgetcsv($handle,1000,",")) !== FALSE) { 
	     	if($ctr > 0)
	     	{
	     		$_sku = $data[0];
	     		$id = $this->catalogProduct->getIdBySku($_sku);
	     		$product = $this->catalogProduct->load($id);
				if ($product->getUnilabRx() == 1) {
				   return $ctr;
				}
	     	}
	        $ctr++;
	    }

	    return $result;
    }
    
    public function extractFile($file,$action)
	{
		// Get customer session
		$session = $this->customerSession;
        $cart = $this->checkoutCart;
        // $cart->save();
		$handle = fopen($file,"r");
	   	$ctr = 0;
	   	$result = 0;
		$skus = [];
	    while (($data = fgetcsv($handle,1000,",")) !== FALSE) {
			
	     	if($ctr > 0)
	     	{
	     		$_sku = $data[0];
				$skus[] = [
					'sku' => $_sku,
					'qty' => $data[2]
				]; 
				//$_catalog = $this->catalogProduct;
				$product = $this->productRepository->get($_sku);
				//$_productId = $_catalog->getIdBySku($_sku);
				//$_product = $this->catalogProduct->load($_productId);
				
				if ( $data[2] % $product->getunilab_moq() == 0) {
					
				}
				else
				{
					$arrayResult = array('line' => $ctr, 'moq' => $product->getunilab_moq());
					return $arrayResult;
				}
				
	     	}
			
	        $ctr++;
			
	    }

		if ($action == 'save') {
			if(count($skus) > 0) {
				foreach($skus as $sku) : 
					$product = $this->productRepository->get($sku['sku']);

					$params = [
						'form_key' => $this->formKey->getFormKey(),
						'product' => $product->getId(),
						'qty' => $sku['qty']
					];
					$this->checkoutCart->addProduct($product, $params);

				endforeach;
				$this->checkoutCart->save();
			}
		}
	    return $result;
    }
    public function checkProductDuplicate($file){
		
		// Get cart instance
		$cart = $this->checkoutCart;
		// $cart->init();

		$handle = fopen($file,"r"); 

	   	$ctr = 0;
	   	$result = 0;
		
		$skus = array();
	    while (($data = fgetcsv($handle,1000,",")) !== FALSE) { 
	     	if($ctr > 0)
	     	{
	     		$_sku = $data[0];
				
				if (in_array($_sku, $skus)){
					$result = 1;
					break;
				}
	 
				array_push($skus,$_sku);
	     	}
	        $ctr++;
	    }

	    return $result;
	}
}