<?php 

namespace Unilab\Catalog\Controller\Validate;

class QuoteRefresh extends \Magento\Framework\App\Action\Action
{
    protected $resultJsonFactory;
    protected $cartHelper;

    /**
     * @param Context $context
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Checkout\Helper\Cart $cartHelper
    )
    {
        $this->resultJsonFactory = $resultJsonFactory;
        $this->cartHelper = $cartHelper;
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

        $data['quote_id'] = $this->cartHelper->getQuote()->getId();
        return $resultJson->setData($data);
    }

    
}