<?php
/**
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Unilab\Pricelist\Controller\Adminhtml\Promo\Catalog;

class Edit extends \Magento\CatalogRule\Controller\Adminhtml\Promo\Catalog
{
    /**
     * @return void
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('id');

        /** @var \Magento\CatalogRule\Api\CatalogRuleRepositoryInterface $ruleRepository */
        $ruleRepository = $this->_objectManager->get(
            \Magento\CatalogRule\Api\CatalogRuleRepositoryInterface::class
        );

        if ($id) {
            try {
                $model = $ruleRepository->get($id);
            } catch (\Magento\Framework\Exception\NoSuchEntityException $exception) {
                $this->messageManager->addError(__('This rule no longer exists.'));
                $this->_redirect('pricelist/*');
                return;
            }
        } else {
            /** @var \Magento\CatalogRule\Model\Rule $model */
            $model = $this->_objectManager->create(\Magento\CatalogRule\Model\Rule::class);
        }

        // set entered data if was error when we do save
        $data = $this->_objectManager->get(\Magento\Backend\Model\Session::class)->getPageData(true);
        if (!empty($data)) {
            $model->addData($data);
        }
        $model->getConditions()->setFormName('pricelist_form');
        $model->getConditions()->setJsFormObject(
            $model->getConditionsFieldSetId($model->getConditions()->getFormName())
        );

        $this->_coreRegistry->register('current_promo_catalog_rule', $model);

        $this->_initAction();
        $this->_view->getPage()->getConfig()->getTitle()->prepend(__('Manage Price List'));
        $this->_view->getPage()->getConfig()->getTitle()->prepend(
            $model->getRuleId() ? 'Edit '.$model->getName() : __('New Price List')
        );

        $breadcrumb = $id ? __('Edit Price List') : __('New Price List');
        $this->_addBreadcrumb($breadcrumb, $breadcrumb);
        $this->_view->renderLayout();
    }
	    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Unilab_Pricelist::edit_pricelist');
    }
}
