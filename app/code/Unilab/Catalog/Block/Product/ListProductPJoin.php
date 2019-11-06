<?php

namespace Unilab\Catalog\Block\Product;

use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Catalog\Block\Product\ProductList\Toolbar;
use Magento\Catalog\Model\Category;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\Eav\Model\Entity\Collection\AbstractCollection;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Exception\NoSuchEntityException;

class ListProduct extends \Magento\Catalog\Block\Product\AbstractProduct implements IdentityInterface
{
    /**
     * Default toolbar block name
     *
     * @var string
     */
    protected $_defaultToolbarBlock = Toolbar::class;

    /**
     * Product Collection
     *
     * @var AbstractCollection
     */
    protected $_productCollection;

    /**
     * Catalog layer
     *
     * @var \Magento\Catalog\Model\Layer
     */
    protected $_catalogLayer;

    /**
     * @var \Magento\Framework\Data\Helper\PostHelper
     */
    protected $_postDataHelper;

    /**
     * @var \Magento\Framework\Url\Helper\Data
     */
    protected $urlHelper;

    /**
     * @var CategoryRepositoryInterface
     */
    protected $categoryRepository;

    protected $_storeManager;

    protected $_productPrice;

    protected $_priceHelper;
    protected $_weeeHelper;
    protected $_taxHelper ;
    protected $_catalogHelper;
    protected $_helper;
    protected $_cart;
    protected $_checkoutSession;
    protected $_ruleFactory;
    /**
     * @param Context $context
     * @param \Magento\Framework\Data\Helper\PostHelper $postDataHelper
     * @param \Magento\Catalog\Model\Layer\Resolver $layerResolver
     * @param CategoryRepositoryInterface $categoryRepository
     * @param \Magento\Framework\Url\Helper\Data $urlHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Framework\Data\Helper\PostHelper $postDataHelper,
        \Magento\Catalog\Model\Layer\Resolver $layerResolver,
        CategoryRepositoryInterface $categoryRepository,
        \Magento\Framework\Url\Helper\Data $urlHelper,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Unilab\Catalog\Block\Product\Price $productPrice,
        \Magento\Framework\Pricing\Helper\Data $priceHelper,
        \Magento\Weee\Helper\Data $weeeHelper,
        \Magento\Tax\Helper\Data $taxHelper,
        \Magento\Catalog\Helper\Data $catalogHelper,
        \Magento\Catalog\Helper\Output $outputHelper,
        \Magento\Checkout\Model\Cart $cart,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\CatalogRule\Model\RuleFactory $ruleFactory,
        array $data = []
    ) {
        $this->_catalogLayer = $layerResolver->get();
        $this->_postDataHelper = $postDataHelper;
        $this->categoryRepository = $categoryRepository;
        $this->_storeManager = $storeManager;
        $this->urlHelper = $urlHelper;
        $this->_productPrice = $productPrice;
        $this->_priceHelper = $priceHelper;
        $this->_weeeHelper = $weeeHelper;
        $this->_taxHelper = $taxHelper;
        $this->_catalogHelper = $catalogHelper;
        $this->_helper = $outputHelper;
        $this->_cart = $cart;
        $this->_customerSession = $customerSession;
        $this->_ruleFactory = $ruleFactory;
        $this->_objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        parent::__construct(
            $context,
            $data
        );
    }

    /**
     * Retrieve loaded product collection
     *
     * The goal of this method is to choose whether the existing collection should be returned
     * or a new one should be initialized.
     *
     * It is not just a caching logic, but also is a real logical check
     * because there are two ways how collection may be stored inside the block:
     *   - Product collection may be passed externally by 'setCollection' method
     *   - Product collection may be requested internally from the current Catalog Layer.
     *
     * And this method will return collection anyway,
     * even when it did not pass externally and therefore isn't cached yet
     *
     * @return AbstractCollection
     */
    protected function _getProductCollection()
    {
        if ($this->_productCollection === null) {
            $this->_productCollection = $this->initializeProductCollection();
        }

        return $this->_productCollection;
    }

    /**
     * Get catalog layer model
     *
     * @return \Magento\Catalog\Model\Layer
     */
    public function getLayer()
    {
        return $this->_catalogLayer;
    }

    /**
     * Retrieve loaded category collection
     *
     * @return AbstractCollection
     */
    public function getLoadedProductCollection()
    {
        return $this->_getProductCollection();
    }

    /**
     * Retrieve current view mode
     *
     * @return string
     */
    public function getMode()
    {
        return $this->getChildBlock('toolbar')->getCurrentMode();
    }

    /**
     * Need use as _prepareLayout - but problem in declaring collection from
     * another block (was problem with search result)
     * @return $this
     */
    protected function _beforeToHtml()
    {
        $collection = $this->_getProductCollection();
        $this->configureToolbar($this->getToolbarBlock(), $collection);
        $collection->load();

        return parent::_beforeToHtml();
    }

    /**
     * Retrieve Toolbar block
     *
     * @return Toolbar
     */
    public function getToolbarBlock()
    {
        $blockName = $this->getToolbarBlockName();
        if ($blockName) {
            $block = $this->getLayout()->getBlock($blockName);
            if ($block) {
                return $block;
            }
        }
        $block = $this->getLayout()->createBlock($this->_defaultToolbarBlock, uniqid(microtime()));
        return $block;
    }

    /**
     * Retrieve additional blocks html
     *
     * @return string
     */
    public function getAdditionalHtml()
    {
        return $this->getChildHtml('additional');
    }

    /**
     * Retrieve list toolbar HTML
     *
     * @return string
     */
    public function getToolbarHtml()
    {
        return $this->getChildHtml('toolbar');
    }

    /**
     * @param AbstractCollection $collection
     * @return $this
     */
    public function setCollection($collection)
    {
        $this->_productCollection = $collection;
        return $this;
    }

    /**
     * @param array|string|integer|\Magento\Framework\App\Config\Element $code
     * @return $this
     */
    public function addAttribute($code)
    {
        $this->_getProductCollection()->addAttributeToSelect($code);
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPriceBlockTemplate()
    {
        return $this->_getData('price_block_template');
    }

    /**
     * Retrieve Catalog Config object
     *
     * @return \Magento\Catalog\Model\Config
     */
    protected function _getConfig()
    {
        return $this->_catalogConfig;
    }

    /**
     * Prepare Sort By fields from Category Data
     *
     * @param \Magento\Catalog\Model\Category $category
     * @return \Magento\Catalog\Block\Product\ListProduct
     */
    public function prepareSortableFieldsByCategory($category)
    {
        if (!$this->getAvailableOrders()) {
            $this->setAvailableOrders($category->getAvailableSortByOptions());
        }
        $availableOrders = $this->getAvailableOrders();
        if (!$this->getSortBy()) {
            $categorySortBy = $this->getDefaultSortBy() ?: $category->getDefaultSortBy();
            if ($categorySortBy) {
                if (!$availableOrders) {
                    $availableOrders = $this->_getConfig()->getAttributeUsedForSortByArray();
                }
                if (isset($availableOrders[$categorySortBy])) {
                    $this->setSortBy($categorySortBy);
                }
            }
        }

        return $this;
    }

    /**
     * Return identifiers for produced content
     *
     * @return array
     */
    public function getIdentities()
    {
        $identities = [];
        foreach ($this->_getProductCollection() as $item) {
            $identities = array_merge($identities, $item->getIdentities());
        }
        $category = $this->getLayer()->getCurrentCategory();
        if ($category) {
            $identities[] = Product::CACHE_PRODUCT_CATEGORY_TAG . '_' . $category->getId();
        }
        return $identities;
    }

    /**
     * Get post parameters
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return string
     */
    public function getAddToCartPostParams(\Magento\Catalog\Model\Product $product)
    {
        $url = $this->getAddToCartUrl($product);
        return [
            'action' => $url,
            'data' => [
                'product' => $product->getEntityId(),
                \Magento\Framework\App\ActionInterface::PARAM_NAME_URL_ENCODED => $this->urlHelper->getEncodedUrl($url),
            ]
        ];
    }

    /**
     * @param \Magento\Catalog\Model\Product $product
     * @return string
     */
    public function getProductPrice(\Magento\Catalog\Model\Product $product)
    {
        $priceRender = $this->getPriceRender();

        $price = '';
        if ($priceRender) {
            $price = $priceRender->render(
                \Magento\Catalog\Pricing\Price\FinalPrice::PRICE_CODE,
                $product,
                [
                    'include_container' => true,
                    'display_minimal_price' => true,
                    'zone' => \Magento\Framework\Pricing\Render::ZONE_ITEM_LIST,
                    'list_category_page' => true
                ]
            );
        }

        return $price;
    }

    /**
     * Specifies that price rendering should be done for the list of products
     * i.e. rendering happens in the scope of product list, but not single product
     *
     * @return \Magento\Framework\Pricing\Render
     */
    protected function getPriceRender()
    {
        return $this->getLayout()->getBlock('product.price.render.default')
            ->setData('is_product_list', true);
    }

    /**
     * Configures product collection from a layer and returns its instance.
     *
     * Also in the scope of a product collection configuration, this method initiates configuration of Toolbar.
     * The reason to do this is because we have a bunch of legacy code
     * where Toolbar configures several options of a collection and therefore this block depends on the Toolbar.
     *
     * This dependency leads to a situation where Toolbar sometimes called to configure a product collection,
     * and sometimes not.
     *
     * To unify this behavior and prevent potential bugs this dependency is explicitly called
     * when product collection initialized.
     *
     * @return Collection
     */
    private function initializeProductCollection()
    {
        $layer = $this->getLayer();
        /* @var $layer \Magento\Catalog\Model\Layer */
        if ($this->getShowRootCategory()) {
            $this->setCategoryId($this->_storeManager->getStore()->getRootCategoryId());
        }

        // if this is a product view page
        if ($this->_coreRegistry->registry('product')) {
            // get collection of categories this product is associated with
            $categories = $this->_coreRegistry->registry('product')
                ->getCategoryCollection()->setPage(1, 1)
                ->load();
            // if the product is associated with any category
            if ($categories->count()) {
                // show products from this category
                $this->setCategoryId(current($categories->getIterator())->getId());
            }
        }

        $origCategory = null;
        if ($this->getCategoryId()) {
            try {
                $category = $this->categoryRepository->get($this->getCategoryId());
            } catch (NoSuchEntityException $e) {
                $category = null;
            }

            if ($category) {
                $origCategory = $layer->getCurrentCategory();
                $layer->setCurrentCategory($category);
            }
        }
		$res = $layer->getProductCollection();
		
        $collection = $layer->getProductCollection();
        
        $collection->addAttributeToSelect('*');
        $collection->getSelect()
            ->joinLeft(
                ['ccg' => 'catalogrule_customer_group'],
                'price_index.customer_group_id = ccg.customer_group_id',
                ['ccg.rule_id']
            )->joinLeft(
                ['c' => 'catalogrule'],
                 'ccg.rule_id = c.rule_id',
                ['c.name']
            )->joinLeft(
                ['cgw' => 'catalogrule_website'],
                 'c.rule_id = cgw.rule_id',
                ['']
            )->joinLeft(
                ['pp' => 'rra_pricelistproduct'],
                'c.name = pp.pricelist_id',
                ['pp.pricelist_id', 'pp.product_sku','pp.visibility']
            );
        // echo "<pre>";
        //     print_r($collection->getSelect()->__toString());
        // echo "</pre>";
        // die;
        /**
         * Customer Sidebar Filter
         */
        $strfilter = $this->getRequest()->getParam('strfilter');
        $tid = $this->getRequest()->getParam('tid');
        $fid = $this->getRequest()->getParam('fid');
        $bid = $this->getRequest()->getParam('bid');
        $sid = $this->getRequest()->getParam('sid');

        if ($strfilter == true || $strfilter == 1) {

            $layer->getProductCollection();

            if ($tid != NULL && $tid != 0)
            {
                $collection->addAttributeToFilter('unilab_type', [['finset' => [$tid]]]);
            }
            if ($fid != NULL && $fid != 0)
            {
                $collection->addAttributeToFilter('unilab_format', [['finset' => [$fid]]]);
            }
            if ($bid != NULL && $bid != 0)
            {
                $collection->addAttributeToFilter('unilab_benefit', [['finset' => [$bid]]]);
            }

            if ($sid != NULL && $sid != 0)
            {
                $collection->addAttributeToFilter('unilab_segment', [['finset' => [$sid]]]);
            }
        }
        // else {

        //     $currentstoreid   = $this->_storeManager->getStore()->getId();

        //     if ($currentstoreid == 1) {
        //         $collection->addAttributeToFilter('unilab_benefit', ['neq' => 757])->addAttributeToFilter('unilab_benefit', ['neq' => 758])->addAttributeToFilter('unilab_benefit', ['neq' => 759]);
        //     }

        // }
        /**
         * End Custom Filter
         */

        // $collection = $layer->getProductCollection();

        $this->prepareSortableFieldsByCategory($layer->getCurrentCategory());

        if ($origCategory) {
            $layer->setCurrentCategory($origCategory);
        }

        $toolbar = $this->getToolbarBlock();
        $this->configureToolbar($toolbar, $collection);

        $this->_eventManager->dispatch(
            'catalog_block_product_list_collection',
            ['collection' => $collection]
        );
        
        $collection->getSelect()->group('ccg.rule_id');
        // echo "<pre>";
        //     print_r($collection->getData());
        // echo "</pre>";
        return $collection;
    }

    /**
     * Configures the Toolbar block with options from this block and configured product collection.
     *
     * The purpose of this method is the one-way sharing of different sorting related data
     * between this block, which is responsible for product list rendering,
     * and the Toolbar block, whose responsibility is a rendering of these options.
     *
     * @param ProductList\Toolbar $toolbar
     * @param Collection $collection
     * @return void
     */
    private function configureToolbar(Toolbar $toolbar, Collection $collection)
    {
        // use sortable parameters
        $orders = $this->getAvailableOrders();
        if ($orders) {
            $toolbar->setAvailableOrders($orders);
        }
        $sort = $this->getSortBy();
        if ($sort) {
            $toolbar->setDefaultOrder($sort);
        }
        $dir = $this->getDefaultDirection();
        if ($dir) {
            $toolbar->setDefaultDirection($dir);
        }
        $modes = $this->getModes();
        if ($modes) {
            $toolbar->setModes($modes);
        }
        // set collection to toolbar and apply sort
        $toolbar->setCollection($collection);
        $this->setChild('toolbar', $toolbar);
    }

    public function getFilterIds()
	{

		$datafilter = array();

		$datafilter['tid'] 	= $this->getRequest()->getParam('tid');
		$datafilter['fid'] 	= $this->getRequest()->getParam('fid');
		$datafilter['bid'] 	= $this->getRequest()->getParam('bid');
		$datafilter['sid'] 	= $this->getRequest()->getParam('sid');

		return $datafilter;

	}

    public function getUrlParams()
    {
        $params = array(
            'strfilter' => $this->getRequest()->getParam('strfilter'),
            'tid' => $this->getRequest()->getParam('tid'),
            'fid' => $this->getRequest()->getParam('fid'),
            'bid' => $this->getRequest()->getParam('bid'),
            'sid' => $this->getRequest()->getParam('sid'),
            'store_id' => $this->_storeManager->getStore()->getId()
        );
    }


    public function getCurrentCategory()
    {
        return  $this->_catalogLayer->getCurrentCategory();
    }

    public function getCurrentCategoryId()
    {
        return $this->getCurrentCategory()->getId();
    }


    public function getCustomProductPrice(\Magento\Catalog\Model\Product $_product)
    {

        $_storeId = $_product->getStoreId();
        $_id = $_product->getId();
        $_weeeSeparator = '';
        $_simplePricesTax = ($this->_taxHelper->displayPriceIncludingTax() || $this->_taxHelper->displayBothPrices());
        $_minimalPriceValue = $_product->getMinimalPrice();
        $_minimalPrice = $this->_catalogHelper->getTaxPrice($_product, $_minimalPriceValue, $_simplePricesTax);
        
        $_specialPriceStoreLabel = $_product->getResource()->getAttribute('special_price')->getStoreLabel();

        if($_product->getTypeID() != 'grouped'){
            $_weeeTaxAmount = $this->_productPrice->getAmountForDisplay($_product);

            if ($this->_weeeHelper->typeOfDisplay($_product, array(\Magento\Weee\Model\Tax::DISPLAY_INCL_DESCR, \Magento\Weee\Model\Tax::DISPLAY_EXCL_DESCR_INCL, 4)))
            {
                $_weeeTaxAmount = $this->_productPrice->getAmount($_product);
                $_weeeTaxAttributes = $this->_weeeHelper->getProductWeeeAttributesForDisplay($_product);
            }
            $_weeeTaxAmountInclTaxes = $_weeeTaxAmount;
            if($this->_weeeHelper->isTaxable() && !$this->_taxHelper->priceIncludesTax($_storeId)){
                $_attributes = $this->_weeeHelper->getProductWeeeAttributesForRenderer($_product, null, null, null, true);
                $_weeeTaxAmountInclTaxes = $this->_weeeHelper->getAmountInclTaxes($_attributes);
            }

            $customer = $this->_productPrice->getCustomer();
            $_price = $this->_catalogHelper->getTaxPrice($_product, $_product->getPrice());
            $_regularPrice = $this->_catalogHelper->getTaxPrice($_product, $_product->getPrice(), $_simplePricesTax);
            $_finalPrice = $this->_catalogHelper->getTaxPrice($_product, $_product->getFinalPrice());
           
            $connection = $this->_productPrice->getResourceConnection();
            $rule_price		= null;
    		$dnow 			= date("Y-m-d");
    		$Tday			= date("l");
    		$is_Ctime 		= false;
    		$day_isactive 	= false;
    		$dateis_active	= false;
    		$discount_in_amount  =	0;
    		$discount_in_percent =	0;

            $customerLevelID = $customer->getPriceLevel();
    		$query 				= "SELECT * FROM catalogrule WHERE '$dnow' BETWEEN from_date AND to_date AND price_level_id='$customerLevelID' AND is_active=1";
    		$rowCatalogRule 	= $connection->fetchRow($query);
			
    		$from_date 			=	$rowCatalogRule ['from_date'];
    		$to_date 			=	$rowCatalogRule ['to_date'];
    		$limit_days 		=	$rowCatalogRule ['limit_days'];
    		$limit_time_from 	=	$rowCatalogRule ['limit_time_from'];
    		$limit_time_to 		=	$rowCatalogRule ['limit_time_to'];
    		$is_active 			=	$rowCatalogRule ['is_active'];
    		$price_level_id 	=	$rowCatalogRule ['price_level_id'];
    		$pricelist_id 		=	$rowCatalogRule ['name'];
    		$rule_price 		=	$rowCatalogRule ['rule_id'];

            //Search Group ID by Rule ID
    		$selectGrpID 	=	$connection->select()->from('catalogrule_customer_group', array('*'))->where('rule_id=?',$rule_price);
    		$RwGrpID 	=$connection->fetchAll($selectGrpID);
    		$is_group 	= false;

    		foreach($RwGrpID as $key=>$value):
    			if($key == 'customer_group_id'):
    				if($customer->getGroupId() == $value['customer_group_id']):
    					$is_group 	= true;
    				endif;
    			endif;
    		endforeach;

            if(!empty($from_date) && !empty($to_date)):
    			$frm_date 			=	date("Y-m-d", strtotime($from_date));
    			$t_date 			=	date("Y-m-d", strtotime($to_date));

                if($dnow >= $frm_date && $dnow <= $t_date):
    				$dateis_active	= true;
    			endif;
    		else:
    			$dateis_active	= true;
    		endif;

    		//Search rule_price from catalogrule_product_price
    		$selectPricelevel 		=	$connection->select()->from('rra_pricelevelmaster', array('*'))->where('id=?',$price_level_id);
    		$priceRW 				=	$connection->fetchRow($selectPricelevel);
    		$price_level_isactive 	=	$priceRW ['is_active'];
			
    		if($price_level_isactive == true):
    			if($limit_time_from == '00:00:00' && $limit_time_to == '00:00:00'):
    				$is_Ctime 	= true;
    			elseif (time() >= strtotime($limit_time_from) && time() <= strtotime($limit_time_to)):
    				$is_Ctime 	= true;
    			endif;

    			foreach(explode(",",$limit_days) as $_tday):
    				if(strtolower($Tday) == strtolower($_tday)):
    					$day_isactive = true;
    				elseif(strtolower($_tday) == 'everyday'):
    					$day_isactive = true;
    				endif;
    			endforeach;
    		endif;
			//$res = [];
			//$res['is_active'] = $is_active;
			//$res['day_isactive'] = $day_isactive;
			//$res['is_Ctime'] = $is_Ctime;
			//$res['dateis_active'] = $dateis_active;
			//$res['is_group'] = $is_group;
			//$res['isCtimeFrom'] = time() .'>='. strtotime($limit_time_from) .'&&'. time() .'<='. strtotime($limit_time_to);
			//echo "<pre>";
			//	print_r($res);
			//echo "</pre>";
    		if($is_active == true && $day_isactive == true && $is_Ctime == true && $dateis_active == true && $is_group == true):
    			if(!empty($rule_price)):
    				//Search Unit Price from catalogrule_product_price
    				$selectUnitPrice 	=	$connection->select()->from('rra_pricelistproduct', array('*'))
    				->where('pricelist_id=?',$pricelist_id)
    				->where('product_sku=?',$_product->getSku());
					
    				$UnitpriceRW 		 =	$connection->fetchRow($selectUnitPrice);
    				$unit_price 		 =	$UnitpriceRW ['unit_price'];
    				$discount_in_amount  =	$UnitpriceRW ['discount_in_amount'];
    				$discount_in_percent =	$UnitpriceRW ['discount_in_percent'];
    				$from_date 			 =	$UnitpriceRW ['from_date'];
    				$to_date 			 =	$UnitpriceRW ['to_date'];
    				$from_date			= strtotime($UnitpriceRW['from_date']);
    				$from_date 			= date('Y-m-d',$from_date);
    				$to_date			= strtotime($UnitpriceRW['to_date']);
    				$to_date 			= date('Y-m-d',$to_date);
    				$TodayDate			= date('Y-m-d');

    				if(strtotime($TodayDate)  >= strtotime($from_date) && strtotime($TodayDate)  <= strtotime($to_date)):
    					if(!empty($unit_price) || $unit_price > 0):
    						$final_amount		 = $unit_price;
    						if($discount_in_percent > 0 || !empty($discount_in_percent) ):
    							$discount_percent 	= $unit_price * ($discount_in_percent /100);
    							$final_amount 		= $unit_price - $discount_percent;
    						endif;
    						if($discount_in_amount > 0 || !empty($discount_in_amount) ):
    							$final_amount 		= $unit_price - $discount_in_amount;
    						endif;
    						$_finalPrice 	= $this->_catalogHelper->getTaxPrice($_product, $final_amount);
    					else:
    						$_finalPrice 	= $this->_catalogHelper->getTaxPrice($_product, $rule_price);
    					endif;
    				endif;
    				if($_finalPrice <=0):
    					$_finalPrice =$_regularPrice ;
    					$_finalPrice =$_price;
    				else:
    					$_regularPrice 	= $_finalPrice;
    					$_price 		= $_finalPrice;
    				endif;
    			endif;
    		else:
    			if($_finalPrice < $_price):
    				$_finalPrice = $_price;
    			endif;
    		endif;

            $_finalPriceInclTax = $this->_catalogHelper->getTaxPrice($_product, $_product->getFinalPrice(), true);
            // echo "<pre>";
            //     print_r($_finalPrice);
            //     print_r('test');
            // echo "</pre>";
            // exit;
            $_weeeDisplayType = $this->_weeeHelper->getPriceDisplayType();
            echo '<div class="price-box">';
                if ($_finalPrice >= $_price):
                    if ($this->_taxHelper->displayBothPrices()):
                        if ($_weeeTaxAmount && $this->_weeeHelper->typeOfDisplay($_product, 0)):
                            echo '<span class="price-excluding-tax">';
                                echo '<span class="label">Excl. Tax:</span>';
                                echo '<span class="price" id="price-excluding-tax-'. $_id.'" "'.$this->_productPrice->getIdSuffix().'">';
                                        echo $this->_priceHelper->currency($_price + $_weeeTaxAmount, true, false);
                                echo '</span>';
                            echo '</span>';
                            echo '<span class="price-including-tax">';
                                echo '<span class="label">Incl. Tax:</span>';
                                echo '<span class="price" id="price-including-tax-'.$_id.'" "'.$this->_productPrice->getIdSuffix().'">';
                                    echo $this->_priceHelper->currency($_finalPriceInclTax + $_weeeTaxAmountInclTaxes, true, false);
                                echo '</span>';
                            echo '</span>';
                        elseif ($_weeeTaxAmount && $this->_weeeHelper->typeOfDisplay($_product, 1)):
                            echo 'span class="price-excluding-tax">';
                                echo '<span class="label">Excl. Tax:</span>';
                                echo '<span class="price" id="price-excluding-tax-'.$_id.'" "'.$this->_productPrice->getIdSuffix().'">';
                                    echo $this->_priceHelper->currency($_price + $_weeeTaxAmount, true, false);
                                echo '</span>';
                            echo '</span>';
                            echo '<span class="weee">(';
                                foreach ($_weeeTaxAttributes as $_weeeTaxAttribute):
                                    echo $_weeeSeparator;
                                    echo $_weeeTaxAttribute->getName();
                                    echo ":". $this->_priceHelper->currency($_weeeTaxAttribute->getAmount(), true, true);
                                    $_weeeSeparator = ' + ';
                                endforeach;
                            echo ')</span>';
                            echo '<span class="price-including-tax">';
                                echo '<span class="label">Incl. Tax:</span>';
                                echo '<span class="price" id="price-including-tax-'.$_id.'" "'.$this->_productPrice->getIdSuffix().'">';
                                    echo $this->_priceHelper->currency($_finalPriceInclTax + $_weeeTaxAmountInclTaxes, true, false);
                                echo '</span>';
                            echo '</span>';
                        elseif ($_weeeTaxAmount && $this->_weeeHelper->typeOfDisplay($_product, 4)):
                            echo '<span class="price-excluding-tax">';
                                echo '<span class="label">Excl. Tax:</span>';
                                echo '<span class="price" id="price-excluding-tax-'.$_id.'" "'.$this->_productPrice->getIdSuffix().'">';
                                    echo $this->_priceHelper->currency($_price + $_weeeTaxAmount, true, false);
                                echo '</span>';
                            echo '</span>';
                            echo '<span class="price-including-tax">';
                                echo '<span class="label">Incl. Tax:</span>';
                                echo '<span class="price" id="price-including-tax-'.$_id.'" "'.$this->_productPrice->getIdSuffix() .'?>">';
                                    echo $this->_priceHelper->currency($_finalPriceInclTax + $_weeeTaxAmountInclTaxes, true, false);
                                echo '</span>';
                                echo '<span class="weee">(';
                                    foreach ($_weeeTaxAttributes as $_weeeTaxAttribute):
                                        echo $_weeeSeparator;
                                        echo $_weeeTaxAttribute->getName(); echo ":".$this->_priceHelper->currency($_weeeTaxAttribute->getAmount() + $_weeeTaxAttribute->getTaxAmount(), true, true);
                                        $_weeeSeparator = ' + ';
                                    endforeach;
                                    echo ')</span>';
                            echo '</span>';
                        elseif ($_weeeTaxAmount && $this->_weeeHelper->typeOfDisplay($_product, 2)):
                            echo '<span class="price-excluding-tax">';
                                echo '<span class="label">Excl. Tax:</span>';
                                echo '<span class="price" id="price-excluding-tax-'. $_id.'" "'.$this->_productPrice->getIdSuffix().'">';
                                    echo $this->_priceHelper->currency($_price, true, false);
                                echo '</span>';
                            echo '</span>';
                            foreach ($_weeeTaxAttributes as $_weeeTaxAttribute):
                                echo '<span class="weee">';
                                    echo $_weeeTaxAttribute->getName(); echo " : ". $this->_priceHelper->currency($_weeeTaxAttribute->getAmount(), true, true);
                                echo '</span>';
                            endforeach;
                            echo '<span class="price-including-tax">';
                                echo '<span class="label">Incl. Tax:</span>';
                                echo '<span class="price" id="price-including-tax-'.$_id.'" "'.$this->_productPrice->getIdSuffix().'">';
                                    echo $this->_priceHelper->currency($_finalPriceInclTax + $_weeeTaxAmountInclTaxes, true, false);
                                echo '</span>';
                            echo '</span>';
                        else:
                            echo '<span class="price-excluding-tax">';
                                echo '<span class="label">Excl. Tax:</span>';
                                echo '<span class="price" id="price-excluding-tax-'.$_id.'" "'.$this->_productPrice->getIdSuffix() .'">';
                                    if ($_finalPrice == $_price):
                                        echo $this->_priceHelper->currency($_price, true, false);
                                    else:
                                        echo $this->_priceHelper->currency($_finalPrice, true, false);
                                    endif;
                                echo '</span>';
                            echo '</span>';
                            echo '<span class="price-including-tax">';
                                echo '<span class="label">Incl. Tax:</span>';
                                echo '<span class="price" id="price-including-tax-'.$_id.'" "'.$this->_productPrice->getIdSuffix().'">';
                                    echo $this->_priceHelper->currency($_finalPriceInclTax, true, false);
                                echo '</span>';
                            echo '</span>';
                        endif;
                    else:
                        if ($_weeeTaxAmount && $this->_weeeHelper->typeOfDisplay($_product, 0)):
                            echo '<span class="regular-price" id="product-price-'. $_id.'" "'.$this->_productPrice->getIdSuffix().'">';
                                echo $this->_priceHelper->currency($_price + $_weeeTaxAmount, true, true);
                            echo '</span>';
                        elseif ($_weeeTaxAmount && $this->_weeeHelper->typeOfDisplay($_product, 1)): // incl. + weee
                            echo '<span class="regular-price" id="product-price-'. $_id.'" "'.$this->_productPrice->getIdSuffix().'">';
                                echo $this->_priceHelper->currency($_price + $_weeeTaxAmount, true, true);
                            echo '</span>';
                            echo '<span class="weee">(';
                                foreach ($_weeeTaxAttributes as $_weeeTaxAttribute):
                                    echo $_weeeSeparator;
                                    echo $_weeeTaxAttribute->getName(); echo " : ". $this->_priceHelper->currency($_weeeTaxAttribute->getAmount(), true, true);
                                    $_weeeSeparator = ' + ';
                                endforeach;
                            echo ')</span>';
                        elseif ($_weeeTaxAmount && $this->_weeeHelper->typeOfDisplay($_product, 4)): // incl. + weee
                            echo '<span class="regular-price" id="product-price-'. $_id.'" "'.$this->_productPrice->getIdSuffix().'">';
                                echo $this->_priceHelper->currency($_price + $_weeeTaxAmount, true, true);
                            echo '</span>';
                            echo '<span class="weee">(';
                                foreach ($_weeeTaxAttributes as $_weeeTaxAttribute):
                                    echo $_weeeSeparator;
                                    echo $_weeeTaxAttribute->getName(); echo ' : '. $this->_priceHelper->currency($_weeeTaxAttribute->getAmount() + $_weeeTaxAttribute->getTaxAmount(), true, true);
                                    $_weeeSeparator = ' + ';
                                endforeach;
                                echo ')</span>';
                        elseif ($_weeeTaxAmount && $this->_weeeHelper->typeOfDisplay($_product, 2)): // excl. + weee + final
                            echo '<span class="regular-price">'.$this->_priceHelper->currency($_price,true,true).'</span><br />';
                            foreach ($_weeeTaxAttributes as $_weeeTaxAttribute):
                                echo '<span class="weee">';
                                    echo $_weeeTaxAttribute->getName(); echo ':'. $this->_priceHelper->currency($_weeeTaxAttribute->getAmount(), true, true);
                                echo '</span>';
                            endforeach;
                            echo '<span class="regular-price" id="product-price-'. $_id.'" "'.$this->_productPrice->getIdSuffix().'">';
                                echo $this->_priceHelper->currency($_price + $_weeeTaxAmount, true, true);
                            echo '</span>';
                        else:
                            echo '<span class="regular-price" id="product-price-'. $_id.'" "'.$this->_productPrice->getIdSuffix().'">';
                                if ($_finalPrice == $_price):
                                    echo $this->_priceHelper->currency($_price, true, true);
                                else:
                                    echo $this->_priceHelper->currency($_finalPrice, true, true);
                                endif;
                            echo '</span>';
                        endif;
                    endif;
                else: /* if ($_finalPrice == $_price): */
                    $_originalWeeeTaxAmount = $this->_weeeHelper->getOriginalAmount($_product);

                    if ($_weeeTaxAmount && $this->_weeeHelper->typeOfDisplay($_product, 0)): // including
                        echo'<p class="old-price">';
                            echo '<span class="price-label">Regular Price:</span>';
                            echo '<span class="price" id="old-price-'.$_id .'" "'. $this->_productPrice->getIdSuffix().'">';
                                echo $this->_priceHelper->currency($_regularPrice + $_originalWeeeTaxAmount, true, false);
                            echo '</span>';
                        echo '</p>';

                        if ($this->_taxHelper->displayBothPrices()):
                            echo '<p class="special-price">';
                                echo '<span class="price-label">'.$_specialPriceStoreLabel.'</span>';
                                echo '<span class="price-excluding-tax">';
                                    echo '<span class="label">Excl. Tax:</span>';
                                    echo '<span class="price" id="price-excluding-tax-'.$_id .'" "'. $this->_productPrice->getIdSuffix().'">';
                                        echo $this->_priceHelper->currency($_finalPrice + $_weeeTaxAmount, true, false);
                                    echo '</span>';
                                echo '</span>';
                            echo '<span class="price-including-tax">';
                                echo '<span class="label">Incl. Tax:</span>';
                                echo '<span class="price" id="price-including-tax-'.$_id.'" "'.$this->_productPrice->getIdSuffix().'">';
                                    echo $this->_priceHelper->currency($_finalPriceInclTax + $_weeeTaxAmountInclTaxes, true, false);
                                echo '</span>';
                            echo '</span>';
                            echo '</p>';
                        else:
                            echo '<p class="special-price">';
                                echo '<span class="price-label">'.$_specialPriceStoreLabel.'</span>';
                                echo '<span class="price" id="product-price-'.$_id.'" "'.$this->_productPrice->getIdSuffix().'">';
                                    echo $this->_priceHelper->currency($_finalPrice + $_weeeTaxAmountInclTaxes, true, false);
                                echo '</span>';
                            echo '</p>';
                        endif;

                    elseif ($_weeeTaxAmount && $this->_weeeHelper->typeOfDisplay($_product, 1)): // incl. + weee
                        echo '<p class="old-price">';
                            echo '<span class="price-label">Regular Price:</span>';
                            echo '<span class="price" id="old-price-'.$_id .'" "'. $this->_productPrice->getIdSuffix().'">';
                                echo $this->_priceHelper->currency($_regularPrice + $_originalWeeeTaxAmount, true, false);
                            echo '</span>';
                        echo '</p>';

                    echo '<p class="special-price">';
                        if ($this->_taxHelper->displayBothPrices()):
                            echo '<span class="price-label">'.$_specialPriceStoreLabel.'</span>';
                            echo '<span class="price-excluding-tax">';
                                echo '<span class="label">Excl. Tax:</span>';
                                echo '<span class="price" id="price-excluding-tax-'.$_id.'" "'.$this->_productPrice->getIdSuffix().'">';
                                  echo $this->_priceHelper->currency($_finalPrice + $_weeeTaxAmount, true, false);
                                echo '</span>';
                            echo '</span>';
                            echo '<span class="weee">(';
                                foreach ($_weeeTaxAttributes as $_weeeTaxAttribute):
                                    echo $_weeeSeparator;
                                    echo $_weeeTaxAttribute->getName(); echo ':'. $this->_priceHelper->currency($_weeeTaxAttribute->getAmount(), true, true);
                                    $_weeeSeparator = ' + ';
                                endforeach;
                            echo ')</span>';
                            echo '<span class="price-including-tax">';
                                echo '<span class="label">Incl. Tax:</span>';
                                echo '<span class="price" id="price-including-tax-'.$_id.'" "'.$this->_productPrice->getIdSuffix().'">';
                                echo $this->_priceHelper->currency($_finalPriceInclTax + $_weeeTaxAmountInclTaxes, true, false);
                                echo '</span>';
                            echo '</span>';
                        else:
                            echo '<p class="special-price">';
                            echo '<span class="price-label">Special Price:</span>';
                            echo '<span class="price" id="product-price-'.$_id .'" "'. $this->_productPrice->getIdSuffix().'">';
                                echo $this->_priceHelper->currency($_finalPrice + $_weeeTaxAmountInclTaxes, true, false);
                            echo '</span>';
                            echo '</p>';
                            echo '<span class="weee">(';
                            foreach ($_weeeTaxAttributes as $_weeeTaxAttribute):
                                echo $_weeeSeparator;
                                echo $_weeeTaxAttribute->getName();
                                echo ':'. $this->_priceHelper->currency($_weeeTaxAttribute->getAmount(), true, true);
                                $_weeeSeparator = ' + ';
                            endforeach;
                            echo ')</span>';
                        endif;
                    echo '</p>';
                    elseif ($_weeeTaxAmount && $this->_weeeHelper->typeOfDisplay($_product, 4)): // incl. + weee
                        echo '<p class="old-price">';
                            echo '<span class="price-label">'. __('Regular Price:') .'</span>';
                            echo '<span class="price" id="old-price-'. $_id .'" "'. $this->_productPrice->getIdSuffix() .'">';
                                echo $this->_priceHelper->currency($_regularPrice + $_originalWeeeTaxAmount, true, false);
                            echo '</span>';
                        echo '</p>';

                        echo '<p class="special-price">';
                            echo '<span class="price-label">'.$_specialPriceStoreLabel.'</span>';
                            echo '<span class="price-excluding-tax">';
                                echo '<span class="label"><'. __('Excl. Tax:').'</span>';
                                echo '<span class="price" id="price-excluding-tax-'.$_id .'" "'. $this->_productPrice->getIdSuffix().'">';
                                    echo $this->_priceHelper->currency($_finalPrice + $_weeeTaxAmount, true, false);
                                echo '</span>';
                            echo '</span>';
                        echo '<span class="weee">(';
                         foreach ($_weeeTaxAttributes as $_weeeTaxAttribute):
                             echo $_weeeSeparator;
                             echo $_weeeTaxAttribute->getName(); echo ':'. $this->_priceHelper->currency($_weeeTaxAttribute->getAmount() + $_weeeTaxAttribute->getTaxAmount(), true, true);
                             $_weeeSeparator = ' + ';
                         endforeach;
                            echo ')</span>';
                        echo '<span class="price-including-tax">';
                            echo '<span class="label">'. __('Incl. Tax:') .'</span>';
                            echo '<span class="price" id="price-including-tax-'.$_id .'" "'.$this->_productPrice->getIdSuffix().'">';
                                echo $this->_priceHelper->currency($_finalPriceInclTax + $_weeeTaxAmountInclTaxes, true, false);
                            echo '</span>';
                        echo '</span>';
                        echo '</p>';
                    elseif ($_weeeTaxAmount && $this->_weeeHelper->typeOfDisplay($_product, 2)): // excl. + weee + final
                        echo '<p class="old-price">';
                            echo '<span class="price-label">'. __('Regular Price:').'</span>';
                            echo'<span class="price" id="old-price-'. $_id .'" "'. $this->_productPrice->getIdSuffix();'">';
                                echo $this->_priceHelper->currency($_regularPrice, true, false);
                            echo '</span>';
                        echo '</p>';

                        echo '<p class="special-price">';
                            echo '<span class="price-label">'.$_specialPriceStoreLabel.'</span>';
                            echo '<span class="price-excluding-tax">';
                                echo '<span class="label">'.__('Excl. Tax:') .'</span>';
                                echo '<span class="price" id="price-excluding-tax-'. $_id .'" "'. $this->_productPrice->getIdSuffix() .'">';
                                    echo $this->_priceHelper->currency($_finalPrice, true, false);
                                echo '</span>';
                            echo '</span>';
                            foreach ($_weeeTaxAttributes as $_weeeTaxAttribute):
                                echo '<span class="weee">';
                                    echo $_weeeTaxAttribute->getName(); echo ':'. $this->_priceHelper->currency($_weeeTaxAttribute->getAmount(), true, true);
                                echo '</span>';
                            endforeach;
                            echo '<span class="price-including-tax">';
                                echo '<span class="label">'. __('Incl. Tax:') .'</span>';
                                echo '<span class="price" id="price-including-tax-'. $_id .'""'. $this->_productPrice->getIdSuffix().'">';
                                    echo $this->_priceHelper->currency($_finalPriceInclTax + $_weeeTaxAmountInclTaxes, true, false);
                                echo '</span>';
                            echo '</span>';
                        echo '</p>';
                    else: // excl.
                        echo '<p class="old-price">';
                            echo '<span class="price-label">'.__('Regular Price:') .'</span>';
                            echo '<span class="price" id="old-price-'. $_id .'" "'. $this->_productPrice->getIdSuffix().'">';
                                echo $this->_priceHelper->currency($_regularPrice, true, false);
                            echo '</span>';
                        echo '</p>';

                        if ($this->_taxHelper->displayBothPrices()):
                            echo '<p class="special-price">';
                                echo '<span class="price-label">'. $_specialPriceStoreLabel.'</span>';
                                echo '<span class="price-excluding-tax">';
                                    echo '<span class="label">'. __('Excl. Tax:').'</span>';
                                    echo '<span class="price" id="price-excluding-tax-'. $_id .'" "'. $this->_productPrice->getIdSuffix().'">';
                                        echo $this->_priceHelper->currency($_finalPrice, true, false);
                                    echo '</span>';
                                echo '</span>';
                                echo '<span class="price-including-tax">';
                                    echo '<span class="label">'.__('Incl. Tax:').'</span>';
                                    echo '<span class="price" id="price-including-tax-'. $_id .'" "'. $this->_productPrice->getIdSuffix().'">';
                                        echo $this->_priceHelper->currency($_finalPriceInclTax, true, false);
                                    echo '</span>';
                                echo '</span>';
                            echo '</p>';
                        else:
                        echo '<p class="special-price">';
                            echo '<span class="price-label">'. $_specialPriceStoreLabel.'</span>';
                            echo '<span class="price" id="product-price-'. $_id .'" "'. $this->_productPrice->getIdSuffix().'">';
                                echo $this->_priceHelper->currency($_finalPrice, true, false);
                            echo '</span>';
                        echo '</p>';
                        endif;
                    endif;
                endif; /* if ($_finalPrice == $_price): */
                if ($this->_productPrice->getDisplayMinimalPrice() && $_minimalPriceValue && $_minimalPriceValue < $_product->getFinalPrice()):

                    $_minimalPriceDisplayValue = $_minimalPrice;
                    if ($_weeeTaxAmount && $this->_weeeHelper->typeOfDisplay($_product, array(0, 1, 4))):
                        $_minimalPriceDisplayValue = $_minimalPrice + $_weeeTaxAmount;
                    endif;

                    if ($this->_productPrice->getUseLinkForAsLowAs()):
                        echo '<a href="'. $_product->getProductUrl() .'" class="minimal-price-link">';
                    else:
                        echo '<span class="minimal-price-link">';
                    endif;
                        echo '<span class="label">'.__('As low as:') .'</span>';
                        echo '<span class="price" id="product-minimal-price-'.$_id .'" "'. $this->_productPrice->getIdSuffix().'">';
                            echo $this->_priceHelper->currency($_minimalPriceDisplayValue, true, false);
                        echo '</span>';
                    if ($this->_productPrice->getUseLinkForAsLowAs()):
                        echo '</a>';
                    else:
                        echo '</span>';
                    endif;
                endif; /* if ($this->getDisplayMinimalPrice() && $_minimalPrice && $_minimalPrice < $_finalPrice): */
            echo '</div>';
        }else{ /* if (!$_product->isGrouped()): */

            $showMinPrice = $this->_productPrice->getDisplayMinimalPrice();
            if ($showMinPrice && $_minimalPriceValue) {
                $_exclTax = $this->_catalogHelper->getTaxPrice($_product, $_minimalPriceValue);
                $_inclTax = $this->_catalogHelper->getTaxPrice($_product, $_minimalPriceValue, true);
                $price    = $showMinPrice ? $_minimalPriceValue : 0;
            } else {
                $price    = $_product->getFinalPrice();
                $_exclTax = $this->_catalogHelper->getTaxPrice($_product, $price);
                $_inclTax = $this->_catalogHelper->getTaxPrice($_product, $price, true);
            }

            if ($price):
                echo '<div class="price-box">';
                    echo '<p';
                        if ($showMinPrice):
                            echo 'class="minimal-price"';
                            endif;
                        echo '>';
                        if ($showMinPrice):
                        echo '<span class="price-label">'. __('Starting at:') .'</span>';
                        endif;
                        if ($this->_taxHelper->displayBothPrices()):
                            echo '<span class="price-excluding-tax">';
                                echo '<span class="label">'. __('Excl. Tax:').'</span>';
                                echo '<span class="price" id="price-excluding-tax-'. $_id .'" "'. $this->_productPrice->getIdSuffix().'">';
                                    echo $this->_priceHelper->currency($_exclTax, true, false);
                                echo '</span>';
                            echo '</span>';
                            echo '<span class="price-including-tax">';
                                echo '<span class="label">'. __('Incl. Tax:') .'</span>';
                                echo '<span class="price" id="price-including-tax-'. $_id .'" "'. $this->_productPrice->getIdSuffix() .'">';
                                    echo $this->_priceHelper->currency($_inclTax, true, false);
                                echo '</span>';
                            echo '</span>';
                        else:

                            $_showPrice = $_inclTax;
                            if (!$this->_taxHelper->displayPriceIncludingTax()) {
                                $_showPrice = $_exclTax;
                            }

                        echo '<span class="price" id="product-minimal-price-'. $_id .'" "'. $this->_productPrice->getIdSuffix().'">';
                            echo $this->_priceHelper->currency($_showPrice, true, false);
                        echo '</span>';
                        endif;
                    echo '</p>';
                echo '</div>';
            endif; /* if ($this->getDisplayMinimalPrice() && $_minimalPrice): */
        }
    }
    public function getQuoteCart(){
        return $this->_cart->getQuote();
    }
    public function getAvailablePcapAmount(){
        $resourceConnection = $this->_objectManager->get('\Magento\Framework\App\ResourceConnection');
        $connection = $resourceConnection->getConnection(\Magento\Framework\App\ResourceConnection::DEFAULT_CONNECTION);
        $customer = $this->_customerSession->getCustomer();
        //Search entity_id from rra_emp_benefits
        
        $select 	=	$connection->select()->from('rra_emp_benefits', array('*'))
        ->where('entity_id=?',$customer->getId())->where('is_active=?',1);
        $benefits_items 	=	$connection->fetchRow($select);
        
        $limit 				= $benefits_items['purchase_cap_limit'];
        $consumed 			= $benefits_items['consumed'];
        $extension 	= $benefits_items['extension'];
       	$available  = 0;
        $available 	= $benefits_items['available'];
        $available += $extension;
        return $available;
    }
    public function getCustomerSessionData(){
        return $this->_customerSession;
    }
    public function getPricelistData($param=[]){
        $pricelist = $this->_ruleFactory->create()->getCollection();
        $pricelist->getSelect()
            ->joinLeft(
                ['ccg' => 'catalogrule_customer_group'],
                 'main_table.rule_id = ccg.rule_id',
                ['ccg.customer_group_id']
            )->joinLeft(
                ['cgw' => 'catalogrule_website'],
                 'main_table.rule_id = cgw.rule_id',
                ['cgw.website_id']
            )->joinLeft(
                ['pp' => 'rra_pricelistproduct'],
                 'main_table.name = pp.pricelist_id',
                ['pp.pricelist_id', 'pp.product_sku','pp.visibility']
            )->where(
                'pp.product_sku="'.@$param['product_sku'].'"'
            )->where(
                'ccg.customer_group_id="'.@$param['company_id'].'"'
            )->where(
                'cgw.website_id="'.@$param['website_id'].'"'
            );
            // echo "<pre>";
            //     print_r($pricelist->getSelect()->__toString());
            // echo "</pre>";
        return $pricelist;
    }
}
