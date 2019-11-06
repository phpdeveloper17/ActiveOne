<?php
/**
 * BpiSecurepay View XML.
 * @category  Unilab
 * @package   BpiSecurepay
 * @author    Kristian Claridad
 */
    namespace Unilab\BpiSecurepay\Block;
    
    class Redirect extends \Magento\Framework\View\Element\Template
    {
    	protected $_systemStore;

	    const _PAYMENT_FORM = 'bpisecurepay_form_checkout';
	    const _PAYMENT_METHOD = 'GET';

	    public function __construct(
	        \Magento\Backend\Block\Template\Context $context,
	        \Magento\Framework\Registry $registry,
	        \Magento\Framework\Data\FormFactory $formFactory,
	        \Unilab\BpiSecurepay\Model\Status $options,
	        array $data = []
	    ) {
	        $this->_options = $options;
	        parent::__construct($context, $data);
	    }

		protected function _toHtml() {
		 	$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        	$config = $objectManager->get('\Unilab\BpiSecurepay\Model\Method\Payment');
  
	        $html = '<html><body onload="document.'.self::_PAYMENT_FORM.'.submit()">';
	        // $html = '<html><body>';
	        $html.= '<div><b>'.('You will be redirected to BPI Securepay payment gateway in a few seconds...Please wait.').'</b></div>';
	        $html.= $this->createhtmlform();
	        $html.= '</body></html>';
	        return $html;

		}
		function createhtmlform(){
			$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        	$config = $objectManager->get('\Unilab\BpiSecurepay\Model\Method\Payment');
			$fhtml = '<form id="'.self::_PAYMENT_FORM.'"
					  		enctype="multipart/form-data"
					  		name="'.self::_PAYMENT_FORM.'"
					  		action ="'.$config->getConfig()->getPaymentUrl().'"
					  		method="'.self::_PAYMENT_METHOD.'">';

			$conf = $config->getCheckoutFormFields();
			
	        unset($conf["virtualPaymentClientURL"]);
	        ksort($conf);
	        $urldata = '';
	        $HashData = '';
	        
	        $md5HashData = $config->getConfig()->getMerchantSecureHashSecret();
	        
	        foreach ($conf as $field=>$value) {
	            if (strlen($value) > 0) {
	            	$fhtml .= '<input type="hidden" name="'.$field.'" value="'.$value.'"/><br/>';

	                $urldata .= ("" ? "" : "&") . urlencode($field) . "=" . urlencode($value);
	                $HashData .= $field . "=" . $value . "&";

	            }
	        }
			
			$concathashdata = rtrim($HashData,"&");
	        $securhash = strtoupper(hash_hmac('SHA256',$concathashdata, pack("H*",$md5HashData)));
	        
	        if (strlen($config->getConfig()->getMerchantSecureHashSecret()) > 0) {
	        	$fhtml .= '<input type="hidden" name="vpc_SecureHash" value="'.$securhash.'"/><br/>';
	        }
	        
	        $fhtml .= '<input type="hidden" name="vpc_SecureHashType" value="SHA256"/><br/>';
			$fhtml .= '</form>';
			return $fhtml;
		}
    }