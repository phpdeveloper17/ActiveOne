<?php
/**
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Unilab\Pricelist\Controller\Adminhtml\Promo\Catalog;

use Magento\Backend\App\Action\Context;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Registry;
use Magento\Framework\Stdlib\DateTime\Filter\Date;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\CatalogRule\Model\RuleFactory as PricelistFactory;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Save extends \Magento\CatalogRule\Controller\Adminhtml\Promo\Catalog
{
    /**
     * @var DataPersistorInterface
     */
    protected $dataPersistor;

    /**
     * @param Context $context
     * @param Registry $coreRegistry
     * @param Date $dateFilter
     * @param DataPersistorInterface $dataPersistor
     */
    public function __construct(
        Context $context,
        Registry $coreRegistry,
        Date $dateFilter,
        DataPersistorInterface $dataPersistor,
        PricelistFactory $pricelistFactory
    ) {
        $this->pricelistFactory = $pricelistFactory;
        $this->dataPersistor = $dataPersistor;
        parent::__construct($context, $coreRegistry, $dateFilter);
    }

    /**
     * @return void
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function execute()
    {
        
        if ($this->getRequest()->getPostValue()) {

            /** @var \Magento\CatalogRule\Api\CatalogRuleRepositoryInterface $ruleRepository */
            $ruleRepository = $this->_objectManager->get(
                \Magento\CatalogRule\Api\CatalogRuleRepositoryInterface::class
            );

            /** @var \Magento\CatalogRule\Model\Rule $model */
            $model = $this->_objectManager->create(\Magento\CatalogRule\Model\Rule::class);

            try {
                $this->_eventManager->dispatch(
                    'adminhtml_controller_catalogrule_prepare_save',
                    ['request' => $this->getRequest()]
                );
                $data = $this->getRequest()->getPostValue();
                
                $rowData = $this->pricelistFactory->create();
                $checkOrigData = $rowData->getCollection()
                                ->addFieldToFilter('name', $data['name'])->count();
                $validate=false;
                $redirect = array();
                if(isset($data['id'])){ // check if id is set, this condition is for edit only
                    $checkDataExistEdit = $rowData->getCollection()
                    ->addFieldToFilter('name', $data['name'])
                    ->addFieldToFilter('rule_id', $data['rule_id'])
                    ->count();
                    if($checkDataExistEdit > 0){ //count 1 > 0
                        $validate=false;
                    }elseif($checkOrigData > 0){
                        $validate=true;
                    }else{
                        $validate=false;
                    }
                }else{
                    if($checkOrigData > 0){
                        $validate=true;
                    }else{
                        $validate=false;
                    }
                }
                if($validate){
					$this->messageManager->addError(__('Duplicate Price List for '.$data['name']));
					return $this->_redirect('pricelist/*/');
				}
                $this->getRequest()->setPostValue('website_ids', implode(',',$data['website_ids']));
                $this->getRequest()->setPostValue('customer_group_ids', implode(',',$data['customer_group_ids']));
                $this->getRequest()->setPostValue('limit_days', implode(',',$data['limit_days']));
                $this->getRequest()->setPostValue('updated_date', date('Y-m-d H:i:s'));
                $data = $this->getRequest()->getPostValue();
                $id = $this->getRequest()->getParam('rule_id');
                if ($id) {
                    $model = $ruleRepository->get($id);
                }

                $validateResult = $model->validateData(new \Magento\Framework\DataObject($data));
                if ($validateResult !== true) {
                    foreach ($validateResult as $errorMessage) {
                        $this->messageManager->addError($errorMessage);
                    }
                    $this->_getSession()->setPageData($data);
                    $this->dataPersistor->set('catalog_rule', $data);
                    $this->_redirect('pricelist/*/edit', ['id' => $model->getId()]);
                    return;
                }

                if (isset($data['rule'])) {
                    $data['conditions'] = $data['rule']['conditions'];
                    unset($data['rule']);
                }

                $model->loadPost($data);

                // echo "<pre>";
                //     print_r($model->getData());
                // echo "</pre>";
                // exit();

                $this->_objectManager->get(\Magento\Backend\Model\Session::class)->setPageData($data);
                $this->dataPersistor->set('catalog_rule', $data);

                $ruleRepository->save($model);

                $this->messageManager->addSuccess(__('You saved the rule.'));
                $this->_objectManager->get(\Magento\Backend\Model\Session::class)->setPageData(false);
                $this->dataPersistor->clear('catalog_rule');

                if ($this->getRequest()->getParam('auto_apply')) {
                    $this->getRequest()->setParam('rule_id', $model->getId());
                    $this->_forward('applyRules');
                } else {
                    if ($model->isRuleBehaviorChanged()) {
                        $this->_objectManager
                            ->create(\Magento\CatalogRule\Model\Flag::class)
                            ->loadSelf()
                            ->setState(1)
                            ->save();
                    }
                    if ($this->getRequest()->getParam('back')) {
                        $this->_redirect('pricelist/*/edit', ['id' => $model->getId()]);
                        return;
                    }
                    $this->_redirect('pricelist/*/');
                }
                //apply rules
                $ruleJob = $this->_objectManager->get(\Magento\CatalogRule\Model\Rule\Job::class);
                $ruleJob->applyAll();
                return;
            } catch (LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addError(
                    __('Something went wrong while saving the rule data. Please review the error log.')
                );
                $this->_objectManager->get(\Psr\Log\LoggerInterface::class)->critical($e);
                $this->_objectManager->get(\Magento\Backend\Model\Session::class)->setPageData($data);
                $this->dataPersistor->set('catalog_rule', $data);
                $this->_redirect('pricelist/*/edit', ['id' => $this->getRequest()->getParam('rule_id')]);
                return;
            }
        }
        $this->_redirect('pricelist/*/');
    }
	    protected function _isAllowed()
    {
        return true;
    }
}
