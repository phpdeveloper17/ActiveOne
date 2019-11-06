<?php
/**
 * BpiSecurepay View XML.
 * @category  Unilab
 * @package   BpiSecurepay
 * @author    Kristian Claridad
 */
namespace Unilab\BpiSecurepay\Block;

use Magento\Framework\Phrase;
use Magento\Payment\Block\ConfigurableInfo;

class Form extends ConfigurableInfo{

	protected function _construct() {
		parent::_construct();
		$this->setTemplate('Unilab_BpiSecurepay::payment/form.phtml');
	}
}