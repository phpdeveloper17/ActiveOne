<?php
/**
 * This class is solely to work-around
 * missing getMessage() method in 2.1.x version of Magento 2.
 */
namespace MageAurigaIT\SmtpMailer\Model\Mail;

class Transport extends \Magento\Framework\Mail\Transport
{
    /**
     * @inheritdoc
     */
    public function getMessage()
    {
        return $this->_message;
    }
}
