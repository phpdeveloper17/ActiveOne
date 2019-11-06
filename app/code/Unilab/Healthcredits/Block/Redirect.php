<?php
/**
 * Healthcredits View XML.
 * @category  Unilab
 * @package   Healthcredits
 * @author    Kristian Claridad
 */
    namespace Unilab\Healthcredits\Block;
    
    class Redirect extends \Magento\Framework\View\Element\AbstractBlock
    {
    	protected $_objectManager;
		protected $_scopeConfig;
    	protected $_directorylist;

        const _PAYMENT_FORM = 'healthcredits_form_checkout';
		const _PAYMENT_METHOD = 'GET';


	    public function __construct(
	        \Magento\Backend\Block\Template\Context $context,
	        \Magento\Framework\Registry $registry,
	        \Magento\Framework\Data\FormFactory $formFactory,
	        \Unilab\Healthcredits\Model\Status $options,
	        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
	        array $data = []
	    ) {
	        $this->_options = $options;
	        $this->_scopeConfig = $scopeConfig;
			$this->_objectManager = \Magento\Framework\App\ObjectManager::getInstance();
			$this->_assetRepo = $this->_objectManager->get("\Magento\Framework\View\Asset\Repository");
	        parent::__construct($context, $data);
	    }

		protected function _toHtml()
	    {
			$mediaurl =$this->_assetRepo->createAsset('images/loader-1.gif')->getUrl();
			// $html = '<html><body>';
			$html = '<html><body onload="document.'.self::_PAYMENT_FORM.'.submit()">';
	        $html.= '<center><div style="text-align:center; color:#252265; margin-top:15%; width:25%;"><img src="'.$mediaurl.'" />
			<br /><br /><b>'.__('Please wait..').'</b></center></div>';
	        $html.= $this->createhtmlform();
			$html.= '<script type="text/javascript">document.getElementById("'.self::_PAYMENT_FORM.'").submit();</script>';
	        $html.= '</body></html>';
			
	        return $html;
	    }

	    function createhtmlform(){
			$config = $this->_objectManager->get('\Unilab\Healthcredits\Model\Method\Payment');
			$fhtml = '<form id="'.self::_PAYMENT_FORM.'"
					  		enctype="multipart/form-data"
					  		name="'.self::_PAYMENT_FORM.'"
					  		action ="'.$config->getConfig()->getPaymentUrl().'"
					  		method="'.self::_PAYMENT_METHOD.'">';

			$conf = $config->getCheckoutFormFields();
			unset($conf["rra_storevirualurl"]);
			ksort($conf);
			$gen_hash_code = $config->getConfig()->getMerchantSecureHashSecret().$config->getConfig()->getMerchantAccesscode();
		
			foreach ($conf as $field=>$value) {
				if (strlen($value) > 0) {
					$fhtml .= '<input type="hidden" name="'.$field.'" value="'.$value.'"/><br/>';
					if ($field=='rra_ordernumber')
					{
						$gen_hash_code.=$value;
					}
				}
			}
			if (strlen($config->getConfig()->getMerchantSecureHashSecret()) > 0) {
				$fhtml .= '<input type="hidden" name="rra_sechash_code" value="'.strtoupper(md5($gen_hash_code)).'"/><br/>';
			}
			return $fhtml;
			
		}
		
    }