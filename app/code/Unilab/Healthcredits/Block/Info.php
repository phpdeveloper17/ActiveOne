<?php
/**
 * Healthcredits View XML.
 * @category  Unilab
 * @package   Healthcredits
 * @author    Kristian Claridad
 */
namespace Unilab\Healthcredits\Block;

use Magento\Framework\Phrase;
use Magento\Payment\Block\ConfigurableInfo;

class Info extends ConfigurableInfo
{
    /**
     * Returns label
     *
     * @param string $field
     * @return Phrase
     */
    protected function getLabel($field)
    {
        return __($field);
    }

    /**
     * Returns value view
     *
     * @param string $field
     * @param string $value
     * @return string | Phrase
     */
   
}
