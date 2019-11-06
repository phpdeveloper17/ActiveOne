<?php
namespace Unilab\Conveniencefee\Plugin\Checkout\Model;


class ShippingInformationManagement
{
    /**
     * @var \Magento\Quote\Model\QuoteRepository
     */
    protected $quoteRepository;

    /**
     * @var \Magecomp\Extrafee\Helper\Data
     */
    protected $dataHelper;

    /**
     * @param \Magento\Quote\Model\QuoteRepository $quoteRepository
     * @param \Magecomp\Extrafee\Helper\Data $dataHelper
     */
    public function __construct(
        \Magento\Quote\Model\QuoteRepository $quoteRepository,
        \Unilab\Conveniencefee\Helper\Data $dataHelper
    )
    {
        $this->quoteRepository = $quoteRepository;
        $this->dataHelper = $dataHelper;
        $this->_objectManager = \Magento\Framework\App\ObjectManager::getInstance();
    }

    /**
     * @param \Magento\Checkout\Model\ShippingInformationManagement $subject
     * @param $cartId
     * @param \Magento\Checkout\Api\Data\ShippingInformationInterface $addressInformation
     */
    public function beforeSaveAddressInformation(
        \Magento\Checkout\Model\ShippingInformationManagement $subject,
        $cartId,
        \Magento\Checkout\Api\Data\ShippingInformationInterface $addressInformation
    )
    {
        // save to quote, quote_address table
        $quoteRepository = $this->_objectManager->create('Magento\Quote\Model\QuoteRepository');
        // $Conveniencefee = 13.39;
        // $BaseConvenienceFee = 15;
        $Conveniencefee = $addressInformation->getConveniencefee();
        $BaseConvenienceFee = $addressInformation->getBaseConveniencefee();
        // echo $Conveniencefee;
        // exit();
        $quote = $this->quoteRepository->getActive($cartId);
        if ($Conveniencefee) {
            $quote->setData('conveniencefee', $quote->getConveniencefee());
            $quote->setData('base_conveniencefee', $quote->getBaseConveniencefee());
        } else {
            $quote->setData('conveniencefee', NULL);
            $quote->setData('base_conveniencefee', NULL);

        }
    }
}

