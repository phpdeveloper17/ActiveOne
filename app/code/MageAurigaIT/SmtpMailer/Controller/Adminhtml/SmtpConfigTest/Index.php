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

namespace MageAurigaIT\SmtpMailer\Controller\Adminhtml\SmtpConfigTest;

use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Backend\App\Action;
use MageAurigaIT\SmtpMailer\Helper\Config;

class Index extends Action
{

    /**
     * @var PageFactory
     */
    private $resultPageFactory;

    /** @var Config */
    private $config;

    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param Cofig $config
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        Config $config
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->config = $config;
        parent::__construct($context);
    }

    /**
     * @return void
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function execute()
    {
        $request = $this->getRequest();

        $username = $request->getPost('username');
        $password = $request->getPost('isPwInputPristine') == 'yes' ?
                        $this->config->getPassword() : $request->getPost('password');
        $auth = strtolower($request->getPost('auth'));

        if ($auth != 'none' && (empty($username) || empty($password))) {
            $this->getResponse()->setBody(__('Please enter a valid username/password'));
            return;
        }

        $to = $request->getPost('test_email') ? : $username;

        //SMTP server configuration
        $smtpHost = $request->getPost('smtphost');

        $smtpConf = [
            'name' => $request->getPost('name'),
            'port' => $request->getPost('smtpport')
        ];

        if ($auth != 'none') {
            $smtpConf['auth'] = $auth;
            $smtpConf['username'] = $username;
            $smtpConf['password'] = $password;
        }

        $ssl = $request->getPost('enc_protocol');
        if ($ssl != 'none') {
            $smtpConf['ssl'] = $ssl;
        }

        $transport = new \Zend_Mail_Transport_Smtp($smtpHost, $smtpConf);

        //Create email
        $mail = new \Zend_Mail();

        $from = trim($request->getPost('from_email'));
        $from = \Zend_Validate::is($from, 'EmailAddress') ? $from : $username;
        $senderName = trim($request->getPost('sender_name'));
        $senderName = $senderName ? :'SMTP Mailer Test';

        $mail->setFrom($from, $senderName);
        $mail->addTo($to, $to);
        $mail->setSubject('Test mail from SmtpMailer');
        $mail->setBodyHtml('Your SMTP configuration is correct, you can save it. :)');

        $setReturnPath = trim($request->getPost('set_return_path'));
        switch ($setReturnPath) {
            case "use_from_address":
                $mail->setReturnPath($from);
                break;

            case "custom":
                $userdef_return_path = trim($request->getPost('user_defined_return_path'));
                if (\Zend_Validate::is($userdef_return_path, 'EmailAddress')) {
                    $mail->setReturnPath($userdef_return_path);
                }
                break;
        }

        $replyToPath = trim($request->getPost('user_defined_reply_to_path'));
        if ($replyToPath) {
            if (\Zend_Validate::is($replyToPath, 'EmailAddress') && !$mail->getReplyTo()) {
                $mail->setReplyTo($replyToPath);
            }
        }

        try {
            if ($mail->send($transport) instanceof \Zend_Mail) {
                $result = __('Email sent successfully. Please check you inbox: ') . ' ' . $to;
            }
        } catch (\Exception $e) {
            $result = __($e->getMessage());
        }

        $this->getResponse()->setBody($result);
    }

    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magento_Config::config_system');
    }
}
