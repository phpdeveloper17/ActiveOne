<?php
/**
 * @category  Unilab
 * @package   Unilab_Afptc
 * @author    Kristian Claridad
 */
namespace Unilab\Afptc\Controller\Adminhtml\Afptc;

use Magento\Framework\Controller\ResultFactory;

class ProductsGrid extends \Magento\Backend\App\Action
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

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry,
     * @param \Unilab\City\Model\CityFactory $gridFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Unilab\Afptc\Model\AfptcFactory $afptcFactory
    ) {
        parent::__construct($context);
        $this->coreRegistry = $coreRegistry;
        $this->afptcFactory = $afptcFactory;
    }

    /**
     * Mapped Grid List page.
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $this->getResponse()->setBody($this->_view->getLayout()->createBlock('Unilab\Afptc\Block\Adminhtml\Afptc\Edit\Renderer\Products')
            ->setCheckedValues((array) $this->getRequest()->getParam('checkedValues', array()))
            ->toHtml()
        );
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Unilab_Afptc::edit_afptc');
    }
}
