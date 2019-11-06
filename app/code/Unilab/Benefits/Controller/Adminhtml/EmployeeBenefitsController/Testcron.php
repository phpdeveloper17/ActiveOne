<?php
/**
 * Pricelist Index Controller.
 * @category  Unilab
 * @package   Unilab_Pricelist
 * @author    Kristian Claridad
 */
namespace Unilab\Benefits\Controller\Adminhtml\EmployeeBenefitsController;

class Testcron extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    private $resultPageFactory;
	protected $_autorefresh;
    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
		\Unilab\Benefits\Cron\Autorefreshperiod $autorefresh
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
		$this->_autorefresh = $autorefresh;
    }

    /**
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
		try{
			$autoref = $this->_autorefresh->refreshBenefits();
			$this->messageManager->addSuccess(__('Successfully Refresh Period.'));
		}catch(\Exception $e){
			$this->messageManager->addError(__($e->getMessage()));
		}
        
    }

    /**
     * Check Order Import Permission.
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return true;
    }
}
