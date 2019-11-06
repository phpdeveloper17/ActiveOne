<?php

namespace Unilab\Inquiry\Helper;

use Magento\Framework\App\Area;
use Magento\Framework\Mail\Template\TransportBuilder;

class Email extends \Magento\Framework\App\Helper\AbstractHelper
{
    protected $scopeConfig;

    protected $_transportBuilder;

    const EMAIL_TEMPLATE_PATH = "inquiry/general/email_template";

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        TransportBuilder $transportBuilder
    ) {

        $this->scopeConfig = $scopeConfig;
        $this->_transportBuilder = $transportBuilder;
        return parent::__construct($context);
    }

    public function sendEmail($templateId, $data, $recipient, $store_id, $from)
    {
        // sample working template id
        // $templateId = $this->scopeConfig->getValue(
        //     self::EMAIL_TEMPLATE_PATH,
        //     \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
        //     1
        // );

        $transport = $this->_transportBuilder->setTemplateIdentifier($templateId)
           ->setTemplateOptions(['area' => Area::AREA_FRONTEND, 'store' => $store_id])
           ->setTemplateVars($data)
           ->setFrom($from)
           ->addTo($recipient)
           ->setReplyTo($data['email'])
           // ->addCc($ccEmails) @array
           ->getTransport();
       $transport->sendMessage();


    }
}
