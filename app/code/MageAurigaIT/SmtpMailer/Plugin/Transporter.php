<?php

/**
 * SmtpMailer extension
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Auriga License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.aurigait.com/magento_extensions/license.txt
 *
 * @category      MageAurigaIT
 * @package       MageAurigaIT_SmtpMailer
 * @copyright     Copyright (c) 2017
 * @license       http://www.aurigait.com/magento_extensions/license.txt Auriga License
 */

namespace MageAurigaIT\SmtpMailer\Plugin;

use Magento\Store\Model\StoreManagerInterface;
use MageAurigaIT\SmtpMailer\Helper\Config;
use Magento\Framework\Mail\MessageInterface;

class Transporter extends \Zend_Mail_Transport_Smtp
{
    /** @var Config */
    protected $config;
    
    /** @var MessageInterface */
    private $message;
    
    /**
     * @param Config $config
     *
     */
    public function __construct(MessageInterface $message, Config $config)
    {
        $this->config = $config;
        $this->message = $message;
    }

    /**
     * @param \Magento\Framework\Mail\TransportInterface $subject
     * @param \Closure $origCallback
     */
    public function aroundSendMessage(\Magento\Framework\Mail\TransportInterface $subject, \Closure $proceed)
    {
        if ($this->config->isSmtpEnabled()) {
            $message = $this->preProcessMessage($subject->getMessage());
            
            $mailArchivist = null;
            $archiveId = null;
            
            if (class_exists("\MageAurigaIT\Outbox\Plugin\MailArchivist")) {
                $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
                $mailArchivist = $objectManager->create('\MageAurigaIT\Outbox\Plugin\MailArchivist');
                $archiveId = $mailArchivist->archiveMessage($message);
            }
            
            try {
                $this->dispatchMessage($message);
                if ($archiveId && $mailArchivist) {
                    $mailArchivist->updateMessageStatus($archiveId, \MageAurigaIT\Outbox\Plugin\MailArchivist::EMAIL_SENT_SUCCESS);
                }
            } catch (\Exception $e) {
                if ($archiveId && $mailArchivist) {
                    $mailArchivist->updateMessageStatus($archiveId, \MageAurigaIT\Outbox\Plugin\MailArchivist::EMAIL_SENT_FAIL);
                }
                throw $e;
            }
        } else {
            return $proceed();
        }
    }
    
    /**
     * Processes email before sending out as per configuration.
     * @param  \Magento\Framework\Mail\MessageInterface $message
     * @return \Magento\Framework\Mail\MessageInterface
     */
    public function preProcessMessage(MessageInterface $message)
    {
        $setReturnPath = $this->config->getSetReturnPath();
        switch ($setReturnPath) {
            case "use_from_address":
                $message->setReturnPath($message->getFrom());
                break;
            
            case "custom":
                $message->setReturnPath(
                    $this->config->getUserDefinedReturnPath()
                );
                break;
        }
                
        $replyToPath = $this->config->getUserDefinedReplyToPath();
        if ($replyToPath && !$message->getReplyTo()) {
                $message->setReplyTo($replyToPath);
        }
        
        $fromEmailAddress = $this->config->getFromEmailAddress();
        if (!$fromEmailAddress) {
            $fromEmailAddress = $message->getFrom();
        }
        
        $senderName = $this->config->getSenderName();
        if (!$senderName) {
            $headers = $message->getHeaders();
            if (isset($headers['From'][0])) {
                $senderName = strip_tags($headers['From'][0], $message->getFrom());
            }
        }
        
        if ($fromEmailAddress && $senderName) {
            $message->clearFrom();
            $message->setFrom($fromEmailAddress, $senderName);
        }
        
        return $message;
    }
    
    /**
     * @param \Magento\Framework\Mail\MessageInterface $message
     * @throws \Magento\Framework\Exception\MailException
     */
    public function dispatchMessage(MessageInterface $message)
    {
        //set config
        $smtpConf = [
            'name' => $this->config->getName(),
            'port' => $this->config->getSmtpPort(),
        ];

        $auth = strtolower($this->config->getAuth());
        if ($auth != 'none') {
            $smtpConf['auth'] = $auth;
            $smtpConf['username'] = $this->config->getUsername();
            $smtpConf['password'] = $this->config->getPassword();
        }

        $ssl = $this->config->getEncryptionProtocol();
        if ($ssl != 'none') {
            $smtpConf['ssl'] = $ssl;
        }
        $smtpHost = $this->config->getSmtpHost();
        $this->initialize($smtpHost, $smtpConf);
        
        try {
            parent::send($message);
        } catch (\Exception $e) {
            throw new \Magento\Framework\Exception\MailException(
                new \Magento\Framework\Phrase($e->getMessage()),
                $e
            );
        }
    }

    /**
     * @param string $host
     * @param array $config
     */
    public function initialize($host = '127.0.0.1', array $config = [])
    {
        if (isset($config['name'])) {
            $this->_name = $config['name'];
        }
        if (isset($config['port'])) {
            $this->_port = $config['port'];
        }
        if (isset($config['auth'])) {
            $this->_auth = $config['auth'];
        }

        $this->_host = $host;
        $this->_config = $config;
    }
}
