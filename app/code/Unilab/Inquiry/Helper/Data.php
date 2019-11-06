<?php

namespace Unilab\Inquiry\Helper;

/**
 *
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{

    const XML_PATH_ENABLED   	 = 'inquiry/general/enabled';
    const XML_PATH_DEPARTMENTS   = 'inquiry/general/departments';

    protected $_customerSession;

    protected $_jsonUnserializer;

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\Serialize\Serializer\Json $jsonSerializer
    ) {
        $this->_customerSession = $customerSession;
        $this->_jsonSerializer = $jsonSerializer;
        parent::__construct($context);
    }

    public function isEnabled()
    {
        return  $this->scopeConfig->getValue(
            self::XML_PATH_ENABLED,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getDepartmentOptions()
    {
        // https://nwdthemes.com/2018/06/21/magento-2-working-with-arrayserialized-backend-model/

        $departments = $this->_jsonSerializer->unserialize($this->scopeConfig->getValue(self::XML_PATH_DEPARTMENTS, \Magento\Store\Model\ScopeInterface::SCOPE_STORE));
        $options = array();

        if(!empty($departments)) {
            foreach ($departments as $department){

                if (!$department['name'] || !$department['email'] || $department['code'] =='DOCTOR' ||  $department['code'] =='PHARMACIST') continue;

                $options[] = array(
                    'label' => $department['name'],
                    'value' => md5($department['code']),
                    'sortorder' => $department['sortorder'],

                );
            }
    		$this->sortDepartments($options,'sortorder');
            if (count($options)){
                array_unshift($options, array('value' => '', 'label' => __('-- Please Select --')));
            }
        }

        return $options;
    }

    public function getDepartmentByHash($hash)
    {
        if($departments = $this->_jsonSerializer->unserialize($this->scopeConfig->getValue(self::XML_PATH_DEPARTMENTS, \Magento\Store\Model\ScopeInterface::SCOPE_STORE))){
			foreach ($departments as $department){
				if (!$department['name'] || !$department['email']) continue;
				if (md5($department['email']) == $hash){
					return array('name' => $department['name'], 'email' => $department['email']);
				}
			}
		}
        return null;
    }

	public function getDepartmentByCode($code, $hash = true)
	{
		if($departments	= $this->_jsonSerializer->unserialize($this->scopeConfig->getValue(self::XML_PATH_DEPARTMENTS, \Magento\Store\Model\ScopeInterface::SCOPE_STORE))){
			$department_obj = new \Magento\Framework\DataObject();
			foreach ($departments as $department){

				if (!$department['code'] || !$department['email'] || !$department['template']) continue;

				if($hash){
					if($code == md5($department['code'])){
						return $department_obj->setData($department);
					}
				}else{
					if($code == $department['code']){
						return $department_obj->setData($department);
					}
				}
			}
		}
        return null;
	}

	protected function sortDepartments(&$options, $col, $dir = SORT_ASC)
	{
		$sort_col = array();
		foreach ($options as $key=> $row) {
			$sort_col[$key] = $row[$col];
		}
		array_multisort($sort_col, $dir, $options);
	}


	public function getAllDepartments(){

		$departments = $this->_jsonSerializer->unserialize($this->scopeConfig->getValue(self::XML_PATH_DEPARTMENTS, \Magento\Store\Model\ScopeInterface::SCOPE_STORE));
        $options = array();
        if(!empty($departments))
        {
            foreach ($departments as $department){
                $options[$department['code']] = array(
                	'code' => $department['code'],
                    'name' => $department['name'],
                    'email' => $department['email'],
                    'subject' => $department['subject'],
                    'template' => $department['template'],
                    'sortorder' => $department['sortorder']
                );
            }
        }
		return $options;
	}

	public function getDepartmentByCodeCstm($code,$isHash=false)
	{
		$options = $this->getAllDepartments();
		if(!$isHash){
			if(isset($options[$code])){
				return $options[$code];
			}
		}else{
			foreach($options as $key=>$option){
				if(md5($key) == $code){
					return $option;
				}
			}
		}

		return '';
	}
}
