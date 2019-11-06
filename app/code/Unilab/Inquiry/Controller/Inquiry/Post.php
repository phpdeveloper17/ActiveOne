<?php

namespace Unilab\Inquiry\Controller\Inquiry;

class Post extends \Magento\Framework\App\Action\Action {

    protected $_inquiryFactory;

    protected $_storeManager;

    protected $_customerSession;

    protected $_messageManager;

    protected $_helper;

    protected $_scopeConfig;

    protected $_customerAddressModel;

    protected $_emailTemplateModel;

    protected $_emailHelper;

    const XML_PATH_EMAIL_RECIPIENT = 'inquiry/general/recipient_email';

    const XML_PATH_EMAIL_SENDER = 'inquiry/general/sender_email_identity';

    const EMAIL_TEMPLATE_PATH = "inquiry/general/email_template";

    const XML_SENDER_COPY = "inquiry/general/sender_copy";

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Unilab\Inquiry\Model\InquiryFactory $inquiryFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Unilab\Inquiry\Helper\Data $helper,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Customer\Model\Address $customerAddressModel,
        \Magento\Email\Model\Template $emailTemplateModel,
        \Unilab\Inquiry\Helper\Email $emailHelper
    ) {
        $this->_inquiryFactory = $inquiryFactory;
        $this->_storeManager = $storeManager;
        $this->_customerSession = $customerSession;
        $this->_messageManager = $messageManager;
        $this->_helper = $helper;
        $this->_scopeConfig = $scopeConfig;
        $this->_customerAddressModel = $customerAddressModel;
        $this->_emailTemplateModel = $emailTemplateModel;
        $this->_emailHelper = $emailHelper;
        parent::__construct($context);
    }

    public function execute()
    {
        $post = $this->getRequest()->getPostValue();
        $store_id = $this->_storeManager->getStore()->getId();
        $customer_id = $this->_customerSession->getCustomer()->getId();
        $customer = $this->_customerSession->getCustomer();
        if($post) {
            try
            {
                if(empty($post['subject'])) {
                    $post['subject'] = '';
                }

                $recipient = $this->_scopeConfig->getValue(self::XML_PATH_EMAIL_RECIPIENT, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);

                $postObject = new \Magento\Framework\DataObject();
                $postObject->setData($post);

                $error = false;

                if (!\Zend_Validate::is(trim($post['name']) , 'NotEmpty')) {
                    $error = true;
                }

                if (!\Zend_Validate::is(trim($post['comment']) , 'NotEmpty')) {
                    $error = true;
                }

                if (!\Zend_Validate::is(trim($post['email']), 'EmailAddress')) {
                    $error = true;
                }

                if ($error) {
                    throw new \Exception();
                }

                // $customerId = null;
				// $customer = null;
				// if($this->_customerSession->isLoggedIn()){
				// 	$customerId = $this->_customerSession->getCustomer()->getId();
				// 	$customer = $this->_customerSession->getCustomer();
				// }
                $options = "";
                if(isset($post['department'])){
                    $options = $this->_helper->getDepartmentByCodeCstm($post['department'],true);
                }
                $inquiryModel = $this->_inquiryFactory->create();
                try {

                    $inquiryModel->setStoreId($store_id);
                    $inquiryModel->setCustomerId($customer_id);
                    $inquiryModel->setConcern(nl2br($post['comment']));
                    $inquiryModel->setDepartment($options['code']);
                    $inquiryModel->setEmailAddress($post['email']);
                    $inquiryModel->setName($post['name']);
                    $inquiryModel->setCreatedTime(date('Y-m-d H:i:s'));
                    $inquiryModel->save();

                } catch (\Exception $e) {
                    $this->_messageManager->addError(__('Unable to submit your inquiry. Please try again later.'));
                    $this->_redirect('*/');
                    return;
                }

                $telephone = NULL;
                if($customer && $customer->getId()){
                    /* Load customer Info to Inquiry */
                    $inquiryModel->setCustomer($customer);
                    $customerAddressId = $customer->getDefaultShipping();
                    if ($customerAddressId){
                        $address = $this->_customerAddressModel->load($customerAddressId);
                        $inquiryModel->setAddress($address->format('html'));
                        $inquiryModel->setMobile($address->getMobile());
                        $inquiryModel->setTelephone($address->getTelephone());
                        $telephone = $address->getTelephone();
                    }
                }

                $allDepartments = $this->_helper->getAllDepartments();

                if(isset($post['department'])){
                    $department = $this->_helper->getDepartmentByCodeCstm($post['department'],true);

                    if (!empty($department)){
                        $recipient = $department['email'];
                        $post['subject'] = '['.$department['name'].']: '.$post['subject'];
                    }

                    // https://webkul.com/blog/magento-2-send-transactional-email-programmatically-in-your-custom-module/
                    // https://community.magento.com/t5/Magento-2-x-Programming/Send-mail-from-custom-module-magento-2/td-p/83053\
                    //https://github.com/magepal/magento2-gmail-smtp-app

                    $templateId = $this->_scopeConfig->getValue(
                        self::EMAIL_TEMPLATE_PATH,
                        \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                        $store_id
                    );

                    $from = $this->_scopeConfig->getValue(
                        self::XML_PATH_EMAIL_SENDER,
                        \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                        $store_id
                    );

                    $default_recipient = $this->_scopeConfig->getValue(
                        self::XML_PATH_EMAIL_RECIPIENT,
                        \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                        $store_id
                    );

                    $data = [
                        "name" => $post['name'],
                        "email" => $post['email'],
                        "telephone" => $telephone,
                        "comment" => $post['comment']
                    ];
                    /**
                    *   @templateId - (string) xml path to template
                    *   @data - (array) parameters used in email template Unilab\Inquiry\view\frontend\email\submitted_form.phtml
                    *   @recipient - (string) receiver email of the setConcern
                    *   @store_id - (int) store id
                    *   @department - (array) department/category of the concern
                    */
                    // $this->_emailHelper->sendEmail($templateId, $data, $recipient, $store_id, $from);
                    $this->_emailHelper->sendEmail($templateId, $data, $default_recipient, $store_id, $from);



                    // echo "<pre>";
                    // var_dump([
                    //     "template" => $department['template'],
                    //     "sender" => $this->_scopeConfig->getValue(self::XML_PATH_EMAIL_SENDER, \Magento\Store\Model\ScopeInterface::SCOPE_STORE),
                    //     "recipient" => $recipient,
                    //     "name" => $department['name'],
                    //     "store_id" => $store_id,
                    //     "vars" => $post,
                    //     "department" => $department
                    // ]);
                    // echo "</pre>";


                    /** Check if send to sender's copy is enabled  */
                    $sendToSender = $this->_scopeConfig->getValue(
                        self::XML_SENDER_COPY,
                        \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                        $store_id
                    );

                    if ($sendToSender == 1){
                        $this->_emailHelper->sendEmail($templateId, $data, $post['email'], $store_id, $from);
                    }

                }
                //
                $this->_messageManager->addSuccess(__('Thank you. We will contact you soon.'));
                $this->_redirect('*/');

                // return;
            } catch (\Exception $e) {
                $this->_messageManager->addError(__($e->getMessage()));
                $this->_redirect('*/');
                return;
            }

        }
        else {
            $this->_redirect('*/');
            return;
        }
    }

}
