<?php
/**
 * DragonPay View XML.
 * @category  Unilab
 * @package   DragonPay
 * @author    Kristian Claridad
 */
    namespace Unilab\DragonPay\Block;
    
    class Redirect extends \Magento\Framework\View\Element\AbstractBlock
    {
    	protected $_objectManager;
    	protected $_scopeConfig;

        const PROD_GATEWAY_URL 	= "https://secure.dragonpay.ph/Pay.aspx";
	    const TEST_GATEWAY_URL 	= "http://test.dragonpay.ph/Pay.aspx";
	    const SHA1_PREFIX		= "X2";
	    const _PAYMENT_FORM    	= "dragonpay_standard_checkout";
	    const _PAYMENT_METHOD   = "GET";


	    public function __construct(
	        \Magento\Backend\Block\Template\Context $context,
	        \Magento\Framework\Registry $registry,
	        \Magento\Framework\Data\FormFactory $formFactory,
	        \Unilab\DragonPay\Model\Status $options,
	        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
	        array $data = []
	    ) {
	        $this->_options = $options;
	        $this->_scopeConfig = $scopeConfig;
			$this->_objectManager = \Magento\Framework\App\ObjectManager::getInstance();
			$this->_isScopePrivate = true;
	        parent::__construct($context, $data);
	    }

		protected function _toHtml()
	    {
	    	
	        $html = '<html><body>';
	        $html.= '<b>'.'You will be redirected to the (DragonPay) website in a few seconds.'.'</b>';
	        $html.= $this->createhtmlform();
	        $html.= '<script type="text/javascript">document.getElementById("'.self::_PAYMENT_FORM.'").submit();</script>';
	        $html.= '</body></html>';

	        return $html;
	    }

	    function createhtmlform(){
			$idSuffix = $this->_objectManager->create('\Magento\Framework\Math\Random')->getUniqueHash();
			$actionURL = self::PROD_GATEWAY_URL."?ts=".time();
	        if($this->_scopeConfig->getValue('payment/dragonpay/testmode')){
	            $actionURL = self::TEST_GATEWAY_URL."?ts=".time();
	        }

			$fhtml = '<form id="'.self::_PAYMENT_FORM.'"
					  		enctype="multipart/form-data"
					  		name="'.self::_PAYMENT_FORM.'"
					  		action ="'.$actionURL.'"
					  		method="'.self::_PAYMENT_METHOD.'">';

			$standard = $this->_objectManager->create('\Unilab\DragonPay\Model\Method\Standard');

			$fhtml .= '<input type="hidden" name="form_key" value="'.$idSuffix.'"/>';
	    	foreach ($standard->getStandardCheckoutFormFields() as $field=>$value) {
	    		$fhtml .= '<input type="hidden" name="'.$field.'" value="'.$value.'"/>';
	        }

	        
	        $id = "submit_to_dragonpay_button_{$idSuffix}";
	        $fhtml .='<input type="submit" id="'.$id.'" value="Click here if you are not redirected within 10 seconds...">';
			$fhtml .= '</form>';
			
			return $fhtml;
		}
		public function getCacheLifetime()
		{
			return null;
		}
		
    }