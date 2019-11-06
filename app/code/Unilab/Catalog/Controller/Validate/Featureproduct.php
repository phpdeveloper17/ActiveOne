<?php 

namespace Unilab\Catalog\Controller\Validate;

class Featureproduct extends \Magento\Framework\App\Action\Action
{
    protected $resultJsonFactory;
    protected $_productFactory;

    /**
     * @param Context $context
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Catalog\Model\ProductFactory $productFactory
    )
    {
        $this->resultJsonFactory = $resultJsonFactory;
        $this->_productFactory = $productFactory;
        parent::__construct($context);
    }

    /**
     * Collect relations data
     *
     * @return \Magento\Framework\Controller\Result\Json
     */
    public function execute()
    {
        $resultJson = $this->resultJsonFactory->create();
        $product_id = $this->getRequest()->getPostValue();
        
        $product = $this->_productFactory->create()->load(@$product_id['product_id']);
        $data['isAvailableProduct'] = $product->isAvailable();
        return $resultJson->setData($data);
    }

    
}