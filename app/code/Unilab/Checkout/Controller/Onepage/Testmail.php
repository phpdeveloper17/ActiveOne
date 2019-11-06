<?php
 
namespace Unilab\Checkout\Controller\Onepage;

use Magento\Framework\Controller\ResultFactory;
 

class Testmail extends \Magento\Framework\App\Action\Action
{
	protected $pageFactory;
	public function __construct(
		\Magento\Store\Model\StoreManagerInterface $storeManager,
		\Magento\Framework\Mail\Template\TransportBuilder $_transportBuilder,
		\Magento\Framework\Translate\Inline\StateInterface $inlineTranslation,
		\Magento\Framework\App\Action\Context $context
		)
	{
		$this->storeManager = $storeManager;		
		$this->_transportBuilder  = $_transportBuilder;
		$this->inlineTranslation = $inlineTranslation;
		return parent::__construct($context);
	}

	public function execute()
	{
		$templateOptions = array('area' => \Magento\Framework\App\Area::AREA_FRONTEND, 'store' => $this->storeManager->getStore()->getId());
		$templateVars = array(
							'store' => $this->storeManager->getStore(),
							'customer_name' => 'Ron Mark',
							'message'   => 'Hello World!!.'
						);
		$from = array('email' => "test@webkul.com", 'name' => 'Name of Sender');
		$this->inlineTranslation->suspend();
		$to = array('ronmarkrudas.web30@gmail.com');
		$transport = $this->_transportBuilder->setTemplateIdentifier('hello_template')
						->setTemplateOptions($templateOptions)
						->setTemplateVars($templateVars)
						->setFrom($from)
						->addTo($to)
						->getTransport();
		$transport->sendMessage();
		$this->inlineTranslation->resume();
	}
}