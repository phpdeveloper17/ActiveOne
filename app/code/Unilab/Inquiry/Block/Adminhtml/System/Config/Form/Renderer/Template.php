<?php

namespace Unilab\Inquiry\Block\Adminhtml\System\Config\Form\Renderer;

class Template extends \Magento\Framework\View\Element\Html\Select
{
    protected $data;

    protected $_registry;

    protected $_emailTempalteResource;

    protected $_templateCollectionFactory;

    protected $_scopeConfig;

    protected $_appConfig;

    public function __construct(
        \Magento\Framework\View\Element\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Email\Model\ResourceModel\Template\CollectionFactory $templateCollectionFactory,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\App\Config $appConfig,
        array $data=[]
    ) {
        $this->data = $data;
        $this->_registry = $registry;
        $this->_templateCollectionFactory = $templateCollectionFactory;
        $this->_scopeConfig = $scopeConfig;
        $this->_appConfig = $appConfig;
        parent::__construct($context, $data);
    }


    /**
     * @param string $value
     * @return $this
     */
    // public function setInputName($value)
    // {
    //     return $this->setName($value);
    // }
    const XML_PATH_TEMPLATE_EMAIL = 'global/template/email/';

        public function setInputName($value)
        {
            return $this->setName($value);
        }

    	public function _toHtml()
        {
            if (!$this->getOptions()) {


               // $templates =  $this->_templateCollectionFactory->create();
               //
               // $attributes = $templates->toOptionArray();
               $attributes = $this->getEmailTemplates();

               foreach ($attributes as $attribute) {
                   $this->addOption($attribute['value'], $attribute['label']);
               }
           }

           return parent::_toHtml();
    		// $column = $this->getColumn();
            //
    		// return  '<select id="email_template#{_elm_id}'.$this->getValue().'123" name="' . $this->getInputName() . '"'.
    		// 				($column['size'] 		 ? 'size="' . $column['size'] . '"'  : '') . ' class="' .
    		// 				(isset($column['class']) ? $column['class'] 				 : 'input-select') . '"'.
    		// 				(isset($column['style']) ? ' style="'.$column['style'] . '"' : '').'>' .
    		// 			$this->getEmailTemplates() .
    		// 		'</select>';
        }

    	protected function getEmailTemplates()
    	{
    		// if(!$collection = $this->_registry->registry('config_system_email_template')){
            //     $collection = $this->_templateCollectionFactory->load();
            //     $this->_registry->register('config_system_email_template', $collection);
            // }
            $collection = $this->_templateCollectionFactory->create();
            $templates		= array();
    		$options   		= "";

    		$email_template_paths = array('inquiry_general_email_template',
    		                              'inquiry_doctor_email_template',
    									  'inquiry_pharmacist_email_template'
    									 );

    		foreach($email_template_paths as $path){

    			$nodeName = str_replace('/', '_', $path);
    			$templateLabelNode = $this->_appConfig->getValue(self::XML_PATH_TEMPLATE_EMAIL . $nodeName . '/label');
                $templateName = "";
                if ($templateLabelNode) {
    				$templateName = __((string)$templateLabelNode);
    				$templateName = __('%s (Default Template from Locale)', $templateName);
    			}
    			array_push(
    				$templates,
    				array(
    					'value' => $nodeName,
    					'label' => $templateName
    				)
    			);
    		}
    		$templates = array_merge_recursive(
    				$templates,
    				$collection->toOptionArray()
    		);

    		foreach($templates as $template){
    			$options .= '<option value="'.$template['value'].'">'.$template['label'].'</option>';
    		}

    		return $templates;
            // return $collection->toOptionArray();
    	}
}
