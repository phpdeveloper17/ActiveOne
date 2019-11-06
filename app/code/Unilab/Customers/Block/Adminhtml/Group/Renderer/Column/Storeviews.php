<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Unilab\Customers\Block\Adminhtml\Group\Renderer\Column;

use Magento\Store\Model\System\Store as SystemStore;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\Escaper;

class Storeviews extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
    private $_systemStore;
    protected $escaper;

    public function __construct(
        \Magento\Store\Model\System\Store $systemStore,
        Escaper $escaper
    ) {
        $this->_systemStore = $systemStore;
        $this->escaper = $escaper;
    }
    public function render(\Magento\Framework\DataObject $row)
    {
        $this->_objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $storeids = $row->getData('webstore_id');

        $content = '';
        if (!empty($storeids)) {
            $origStores = $storeids;
        }
        if (empty($origStores)) {
            $origStores[] = 0;
        }
        if (!is_array($origStores)) {
            $origStores = [$origStores];
        }
        if (in_array(0, $origStores) && count($origStores) == 1) {
            return __('All Store Views');
        }
        $origStores = explode(',',@$origStores[0]);
        $data = $this->_systemStore->getStoresStructure(false, $origStores);
        foreach ($data as $website) {
            $content .= '<strong>'.$website['label'] . "</strong><br/>";
            foreach ($website['children'] as $group) {
                $content .= str_repeat('&nbsp;', 3) . '<strong>'.$this->escaper->escapeHtml($group['label']) . "</strong><br/>";
                foreach ($group['children'] as $store) {
                    $content .= str_repeat('&nbsp;', 6) . $this->escaper->escapeHtml($store['label']) . "<br/>";
                }
            }
        }

        return $content;
    }
    
}