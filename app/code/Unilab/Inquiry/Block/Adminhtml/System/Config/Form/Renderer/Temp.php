<?php

namespace Unilab\Inquiry\Block\Adminhtml\System\Config\Form\Renderer;

class Temp extends \Magento\Framework\View\Element\Html\Select
{
    protected $data;
    public function __construct(
        \Magento\Framework\View\Element\Context $context,
        array $data
    ) {
        $this->data = $data;

        parent::__construct($context, $data);
    }


    /**
     * @param string $value
     * @return $this
     */
    public function setInputName($value)
    {
        return $this->setName($value);
    }

    /**
     * Render block HTML
     *
     * @return string
     */
    public function _toHtml()
    {
        // $column = $this->data['column'];

        // return  '<select id="email_template#{_elm_id}" name="' . $this->getInputName() . '"'.
		// 				(isset($column['size']) 		 ? 'size="' . $column['size'] . '"'  : '') . ' class="' .
		// 				(isset($column['class']) ? $column['class'] 				 : 'input-select') . '"'.
		// 				(isset($column['style']) ? ' style="'.$column['style'] . '"' : '').'>' .
        //                 '<option value="First">First value</option>' .
        //                 '<option value="Second">Second value</option>' .
		// 		'</select>';

        // $html = '<select name="">';
        // $html .= '<option value="First">First value</option>';
        // $html .= '<option value="Second">Second value</option>';
        // $html .= '</select>';
        // return $html;
    }
}
