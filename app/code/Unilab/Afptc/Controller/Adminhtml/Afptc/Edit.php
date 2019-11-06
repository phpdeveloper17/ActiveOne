<?php
/**
 * @category  Unilab
 * @package   Unilab_Afptc
 * @author    Kristian Claridad
 */
namespace Unilab\Afptc\Controller\Adminhtml\Afptc;

use Magento\Framework\Controller\ResultFactory;

class Edit extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Framework\Registry
     */
    private $coreRegistry;

    /**
     * @var \Unilab\Grid\Model\GridFactory
     */
    private $gridFactory;
    protected $afptcFactory;
    protected $catalogruleInterface;
    protected $_logger;
    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry,
     * @param \Unilab\City\Model\CityFactory $gridFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Unilab\Afptc\Model\AfptcFactory $afptcFactory,
        \Unilab\Afptc\Logger\Logger $logger
    ) {
        parent::__construct($context);
        $this->coreRegistry = $coreRegistry;
        $this->afptcFactory = $afptcFactory;
        $this->_logger = $logger;
    }

    /**
     * Mapped Grid List page.
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $rowId = (int) $this->getRequest()->getParam('id');
        $rowData = $this->afptcFactory->create();
        $rowTitle = '';
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        if ($rowId) {
            $rowData = $rowData->load($rowId);
            $rowTitle = $rowData->getName();
            if (!$rowData->getRule_id()) {
                $this->messageManager->addError(__('Add Free Product To Cart no longer exist.'));
                $this->_redirect('afptc/afptc/index');
                return;
            }
        }
        $rule = $this->_initRule();
      
        $rule->getConditions()->setJsFormObject('rule_conditions_fieldset');
        // $this->coreRegistry->register('awafptc_rule', $rowData);
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $title = $rowId ? __('Edit Rule ').'"'.$rowTitle.'"' : __('New Rule');
        $resultPage->getConfig()->getTitle()->prepend($title);
        return $resultPage;
    }
    public function _initRule()
    {
        $ruleModel = $this->_objectManager->create('\Unilab\Afptc\Model\Rule');
        $ruleId  = (int) $this->getRequest()->getParam('id');
        if ($ruleId) {
            try {
                $ruleModel->load($ruleId);
            } catch (\Exception $e) {
                $this->_logger->error(sprintf($e));
            }
        }

        if (null !== $this->_objectManager->create("\Magento\Backend\Model\Session")->getFormActionData()) {
            $ruleModel->addData($this->_objectManager->create("\Magento\Backend\Model\Session")->getFormActionData());
            $this->_objectManager->create("\Magento\Backend\Model\Session")->setFormActionData(null);
        }
        $this->coreRegistry->register('awafptc_rule', $ruleModel);

        return $ruleModel;
    }
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Unilab_Afptc::edit_afptc');
    }
}
