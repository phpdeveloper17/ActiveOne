<?php

namespace Unilab\Wishlist\Block\Customer;

class Wishlist extends \Magento\Wishlist\Block\Customer\Wishlist
{
    protected function _prepareLayout()
    {
        $this->pageConfig->getTitle()->set(__('My Favorites'));
    }
}