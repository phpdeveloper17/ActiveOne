<?php
/**
 * @category  Unilab
 * @package   Unilab_Afptc
 * @author    Kristian Claridad
 */
namespace Unilab\Afptc\Controller\Adminhtml\Afptc;

use Magento\Rule\Model\Condition\AbstractCondition;

class NewConditionHtml extends \Magento\CatalogRule\Controller\Adminhtml\Promo\Catalog
{
    /**
     * @return void
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('id');
        // echo "<pre>";
        //     print_r($id);
        // echo "</pre>";
        $formName = $this->getRequest()->getParam('form_namespace');
        $typeArr = explode('|', str_replace('-', '/', $this->getRequest()->getParam('type')));
        $type = $typeArr[0];

        $model = $this->_objectManager->create($type)
            ->setId($id)
            ->setType($type)
            ->setRule($this->_objectManager->create(\Unilab\Afptc\Model\Rule::class))
            ->setPrefix('conditions');

        if (!empty($typeArr[1])) {
            $model->setAttribute($typeArr[1]);
        }

        if ($model instanceof AbstractCondition) {
            $model->setJsFormObject($this->getRequest()->getParam('form'));
            $model->setFormName($formName);
            $html = $model->asHtmlRecursive();
        } else {
            $html = '';
        }
        $this->getResponse()->setBody($html);
    }
}
