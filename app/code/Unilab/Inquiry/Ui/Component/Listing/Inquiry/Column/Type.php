<?php
/**
 * Banners Ui Component Action.
 * @category  Unilab
 * @package   Unilab_Banners
 * @author    Unilab
 * @copyright Copyright (c) 2010-2017 Unilab Software Private Limited (https://Unilab.com)
 * @license   https://store.Unilab.com/license.html
 */
namespace Unilab\Inquiry\Ui\Component\Listing\Inquiry\Column;

use Magento\Catalog\Helper\Image;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Ui\Component\Listing\Columns\Column;

class Type extends Column
{
    /** Url path */
    const ALT_FIELD = 'banner';
    /** @var UrlInterface */
    protected $storeManager;

    /**
     * @var string
     */

    /**
     * @param ContextInterface   $context
     * @param UiComponentFactory $uiComponentFactory
     * @param UrlInterface       $urlBuilder
     * @param array              $components
     * @param array              $data
     * @param string             $editUrl
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        Image $imageHelper,
        UrlInterface $urlBuilder,
        StoreManagerInterface $storeManager,
        array $components = [],
        array $data = []
    ) {
        $this->storeManager = $storeManager;
        $this->imageHelper = $imageHelper;
        $this->urlBuilder = $urlBuilder;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * Prepare Data Source.
     *
     * @param array $dataSource
     *
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if(isset($dataSource['data']['items'])) {

            $fieldName = $this->getData('name');

            foreach($dataSource['data']['items'] as & $item) {

                if(!empty($item['customer_id']) && $item['customer_id'] != 0) {
                    $item['type'] = "<span style='color:blue;font-weight: bold;'>". __('CUSTOMER')."</span>";
                }
                else {
                    $item['type'] = "<span style='color:red;font-weight: bold;'>". __('GUEST')."</span>";
                }


                // $url = '';
                // if($item[$fieldName] != '') {
                //     $url = $this->storeManager->getStore()->getBaseUrl(
                //         \Magento\Framework\UrlInterface::URL_TYPE_MEDIA
                //     ).'banners/'.$item[$fieldName];
                // }
                // $item[$fieldName . '_src'] = $url;
                // $item[$fieldName . '_alt'] = $this->getAlt($item) ?: '';
                // $item[$fieldName . '_link'] = $this->urlBuilder->getUrl(
                //     'unilab_banners/banners/edit',
                //     ['id' => $item['banner_id']]
                // );
                // $item[$fieldName . '_orig_src'] = $url;


            }
        }

        // echo "<pre>";
        // var_dump($dataSource['data']['items']);
        // echo "</pre>";
        return $dataSource;
    }

    // protected function getAlt($row)
    // {
    //     $altField = $this->getData('config/altField') ?: self::ALT_FIELD;
    //     return isset($row[$altField]) ? $row[$altField] : null;
    // }
}
