<?php

namespace Unilab\Catalog\Model\Adminhtml;

class Addproducts extends \Magento\Framework\Model\AbstractModel
{

    protected $resourceConnection;
    protected $userSession;
    protected $messageManager;
    
    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Unilab\Grid\Model\customerGroupFactory $customerGroupFactory
     */
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectmanager,
        \Magento\Framework\App\ResourceConnection $resourceConnection,
        \Magento\Backend\Model\Auth\Session $userSession,
		\Magento\Framework\Message\ManagerInterface $messageManager,
		\Magento\Framework\Session\SessionManagerInterface $coreSession,
        \Magento\Framework\App\Filesystem\DirectoryList $directorylist
    ) {
        $this->_objectManager = $objectmanager;
        $this->resourceConnection = $resourceConnection;
        $this->userSession = $userSession;
		$this->messageManager = $messageManager;
		$this->_coreSession = $coreSession;
        $this->_directorylist = $directorylist;
    }

    public function processData()
    {
        ini_set("memory_limit",-1);
		ini_set('max_execution_time', '0');
        // try {
            // echo "test";
            $csv  = $this->getData('csv');
            $head = $this->getData('head');

            foreach ($head as $key => $value):
				if(strtolower($value) == 'id'):		  
					$value = 'price_id';			
				endif;
				
				$key 			= strtolower(str_replace(' ', '_', $key));		  
				$head[$key] 	= strtolower(str_replace(' ', '_', $value));
				
				if(!empty($head[$key])):
					$fieldName[] = $head[$key];
				endif;
				
            endforeach;
            
            $csvResult 		 = array_map("array_combine", array_fill(0, count($csv), $head), $csv);

            $fieldName 		 = implode(",", $fieldName);

			$saveTempProduct = $this->_saveTemp($csvResult, $fieldName);
        // }catch (\Exception $e){
        // $this->messageManager->addError($e->getMessage());
        // }
        // return $saveTempProduct;
    }
    protected function _saveTemp($csvResult, $fieldName)
	{
        $dataSave = null;
		$attribute_set 		= null;
		$product_type 		= null;
		$name 				= null;
		$generic_name 		= null;
		$description 		= null;
		$short_description 	= null;
		$sku 				= null;
		$weight 			= null;
		$status 			= null;
		$visibility			= null;

		$unilab_rx			= null;
		$unilab_type		= null;
		$unilab_format		= null;
		$unilab_benefit		= null;
		$unilab_segment		= null;
		$unilab_size		= null;
		$unit_price			= null;
		$moq				= null;
		$price				= null;
		$tax_class			= null;
		$qty			    = null;

		$base_image			= null;
		$small_image		= null;
		$thumbnail			= null;
		$stock_availability	= null;
		$websites			= null;
		$category			= null;
		$uom				= null;
		$intial_quantity	= null;
		$id					= null;

		$fieldName 			= array();
		$fieldValue 		= array();

		$DataResponse 		= false;
		//end
		$count 			    = 0;
		$countSave		    = 0;
		$countBreak 	    = 10;	
		$alreadysave	    = 0;
		$getData 		    = array();
		$resData		    = array();
		// $tablename 		= self::TABLE_NAME_VALUE;	
		$lastIncrement	= null;
        
        $coreSession = $this->_objectManager->get('\Magento\Framework\Session\SessionManagerInterface');
		$coreSession->unsRecords();
		$records		= count($csvResult);
	
		$filecount = $this->_directorylist->getPath('var'). DS. 'cache'. DS .'mage--csv' . DS. 'productscount';
        $SaveCount = file_get_contents($filecount);
        if(empty($SaveCount)):
			$SaveCount = 0;
        endif;

		$counter = 0;
		foreach($csvResult as $_key=>$_value):
		
			$counter++;
			$fieldValue 						= null;
            $productData  						= null;
            $productData['description']			= null;
            $productData['short_description']	= null;
            $productData['price']				= 0;
            $productData['unit_price']			= 0;
            $productData['moq']					= 0;
            $productData['weight']				= null;
            $productData['intial_quantity']		= 0;
            $productData['uom']					= 0;			
            $attribute_set						= "Default";
            $productData['unilab_rx'] 			= 0;
            $productData['status'] 				= 1;

			if($count >= $SaveCount):
				
				foreach($_value as $key=>$value):
				
					if ($key == 'sku'):
						$productData['sku'] = $value;
						$sku = $value;
					elseif ($key == 'description'):
						$productData['description'] = $this->cleanString($value);
					elseif ($key == 'short_description'):
						$productData['short_description'] = $this->cleanString($value);
					elseif ($key == 'name'):
						$productData['name'] = $value;
					elseif ($key == 'price'):
						$productData['price'] = $value;
					elseif ($key == 'unit_price'):
						$productData['unit_price'] = $value;
					elseif ($key == 'moq'):
						$productData['moq'] = $value;
					elseif ($key == 'attribute_set'):
						$attribute_set = $value;
					elseif ($key == 'tax_class'):
						$tax_class = $value;
					elseif ($key == 'unilab_type'):
						$unilab_type = $value;
					elseif ($key == 'unilab_format'):
						$unilab_format = $value;
					elseif ($key == 'unilab_benefit'):
						$unilab_benefit = $value;
					elseif ($key == 'unilab_segment'):
						$unilab_segment = $value;
					elseif ($key == 'unilab_size'):
						$unilab_size = $value;
					elseif ($key == 'unilab_rx'):
						if(strtolower($value) == 'yes'):
							$productData['unilab_rx'] = 1;
						else:
							$productData['unilab_rx'] = 0;
						endif;
					elseif ($key == 'status'):
						if(strtolower($value) == 'enabled'):
							$productData['status'] = 1;
						else:
							$productData['status'] = 2;
						endif;
					elseif ($key == 'category'):
						$category = $value;
					elseif ($key == 'generic_name'):
						$generic_name = $value;
					elseif ($key == 'uom'):
						$uom = $value;
					elseif ($key == 'intial_quantity'):
						$productData['intial_quantity'] = $value;
					elseif ($key == 'weight'):
						$productData['weight'] = $value;
					endif;

					$fieldValue[] = "'".$value."'";	
					
                endforeach;
               
				if(!empty($fieldValue)):
					$dataSave = true;
					$countSave++;
                    $currentnumber = $count + 1;
                    
					if ($count >= $SaveCount):
								
                        if($this->_isskuChecker($sku) == false):
                            
                            $productData['generic_name'] 	= $this->_getgenericID($generic_name);
                            $productData['uom'] 			= $this->_getuomId($uom);
                            $productData['category'] 		= $this->_getcatId($category);
                            $productData['tax_class'] 		= $this->_getTaxId($tax_class);
                            $productData['attribute_set'] 	= $this->_getattribId($attribute_set);
                            $productData['unilab_type'] 	= $this->_getunilabType($unilab_type);
                            $productData['unilab_format'] 	= $this->_getunilabformat($unilab_format);
                            $productData['unilab_benefit'] 	= $this->_getunilabbenefit($unilab_benefit);
                            $productData['unilab_segment'] 	= $this->_getunilabsegment($unilab_segment);
                            $productData['unilab_size'] 	= $this->_getunilabsize($unilab_size);
                        
							$response = $this->createProduct($productData);
							if($response){
								$resData[] = $currentnumber. '. '. $productData['sku'] .' : '.$productData['name'].' - <span style="color:green;">Success!</span>';
								$coreSession->setStatussave(1);
							}else{
								$resData[] = $currentnumber. '. '. $productData['sku'] .' : '.$productData['name'].' - <span style="color:red;">Failed!</span>';
								$coreSession->setStatussave(0);
							}
							
						else:
							$resData[] = $currentnumber. '. '. $productData['sku'] .' : '.$productData['name'].' - <span style="color:red;">Exist!</span>';
							$coreSession->setStatussave(0);
						endif;
					endif;
					
					$coreSession->setRecordsave($resData);
				endif;
			endif;
			$count++;					
			$remainingRec  				= array();
			$remainingRec['Allrecords']	= $records;
			$remainingRec['Savecount']	= $count;		
			$coreSession->setRecords($remainingRec);							
			if($dataSave == true && $countSave == $countBreak):
				$countSave = 0;
				break;
			endif;
			
		endforeach;	
		// echo "<pre>";
		// 	print_r($coreSession->getData());
		// echo "</pre>";
		// exit();
		return $this;
	}
    protected function _getConnection()
    {
		$connection = $this->resourceConnection->getConnection(\Magento\Framework\App\ResourceConnection::DEFAULT_CONNECTION);
        return $connection;
    }
    protected function _getgenericID($generic_name)
	{	
		$unilabTypeSql 		= "SELECT option_id FROM eav_attribute_option_value WHERE value LIKE '$generic_name'";						
		$unilabResult 	= $this->_getConnection()->fetchRow($unilabTypeSql);	
		
		return $unilabResult['option_id'];		
	}	
	
	
	protected function _getcatId($category)
	{	
		$unilabTypeSql 		= "SELECT entity_id FROM catalog_category_entity_varchar WHERE value LIKE '%$category%'";						
		$unilabResult 	= $this->_getConnection()->fetchRow($unilabTypeSql);	
		
		return $unilabResult['entity_id'];		
	}	
	

	protected function _getuomId($uom)
	{	
		$unilabTypeSql 		= "SELECT option_id FROM eav_attribute_option_value WHERE value LIKE '$uom'";						
		$unilabResult 	= $this->_getConnection()->fetchRow($unilabTypeSql);	
		
		return $unilabResult['option_id'];		
	}
	
	protected function _getunilabsize($unilab_size)
	{	
		$unilabTypeSql 		= "SELECT option_id FROM eav_attribute_option_value WHERE value LIKE '%$unilab_size%'";						
		$unilabResult 	= $this->_getConnection()->fetchRow($unilabTypeSql);	
		
		return $unilabResult['option_id'];		
	}
	
	
	protected function _getunilabsegment($unilab_segment)
	{	
		$unilabTypeSql 		= "SELECT option_id FROM eav_attribute_option_value WHERE value LIKE '%$unilab_segment%'";						
		$unilabResult 	= $this->_getConnection()->fetchRow($unilabTypeSql);	
		
		return $unilabResult['option_id'];		
	}	
	
	
	
	protected function _getunilabbenefit($unilab_benefit)
	{	
		$unilabTypeSql 		= "SELECT option_id FROM eav_attribute_option_value WHERE value LIKE '%$unilab_benefit%'";						
		$unilabResult 	= $this->_getConnection()->fetchRow($unilabTypeSql);	
		
		return $unilabResult['option_id'];		
	}	
	
	protected function _getunilabformat($unilab_format)
	{	
		$unilabTypeSql 		= "SELECT option_id FROM eav_attribute_option_value WHERE value LIKE '%$unilab_format%'";						
		$unilabResult 	= $this->_getConnection()->fetchRow($unilabTypeSql);	
		
		return $unilabResult['option_id'];		
	}
		
	
	protected function _getunilabType($unilab_type)
	{	
		$unilabTypeSql 		= "SELECT option_id FROM eav_attribute_option_value WHERE value LIKE '%$unilab_type%'";$unilabResult 	= $this->_getConnection()->fetchRow($unilabTypeSql);	
		return $unilabResult['option_id'];		
	}
	
	
	protected function _getattribId($attribute_set)
	{
		$AttribSql 		= "SELECT attribute_set_id FROM eav_attribute_set WHERE attribute_set_name LIKE '%$attribute_set%'";						
		$AttribResult 	= $this->_getConnection()->fetchRow($AttribSql);
		return $AttribResult['attribute_set_id'];		
	}
	
	protected function _getTaxId($tax_class)
	{
		$TaxClssSql 	= "SELECT class_id FROM tax_class WHERE class_name LIKE '%$tax_class%'";
		$TaxClssResult 	= $this->_getConnection()->fetchRow($TaxClssSql);	
		if(empty($TaxClssResult['class_id'])):
			$TaxClssResult['class_id'] = 0;
		endif;
		return $TaxClssResult['class_id'];
	}
    protected function _isskuChecker($sku)
	{
		$id = $this->_objectManager->create('\Magento\Catalog\Model\Product')->getIdBySku($sku);
		if ($id){
			$response = true;	
		}
		else{
			$response = false;
		}	
		
		return $response;
    }
    protected function createProduct($productData)
	{
        
		try {
		
			$this->_objectManager->create("\Magento\Store\Model\StoreManagerInterface")->setCurrentStore(0);
			
			$product = $this->_objectManager->create('\Magento\Catalog\Model\Product');
			
			$product
				->setWebsiteIds(array(1))
				->setAttributeSetId($productData['attribute_set'])
				->setTypeId('simple')
				->setCreatedAt(strtotime('now')) 
				->setSku($productData['sku']) 
				->setName($productData['name']) 
				->setWeight($productData['weight'])
				->setStatus($productData['status'])
				->setTaxClassId($productData['tax_class'])
				->setVisibility(\Magento\Catalog\Model\Product\Visibility::VISIBILITY_BOTH)
				//->setManufacturer(28)
				//->setColor(24)
				//->setNewsFromDate(strtotime('now'))
				//->setNewsToDate(strtotime('now'))
				->setgeneric_name($productData['generic_name'])
				->setunilab_unit_price($productData['unit_price'])
				->setunilab_moq($productData['moq'])
				->setunilab_rx($productData['unilab_rx'])
				->setunilab_type($productData['unilab_type'])
				->setunilab_uom($productData['uom'])
				->setunilab_format($productData['unilab_format'])
				->setunilab_benefit($productData['unilab_benefit'])
				->setunilab_segment($productData['unilab_segment'])
				->setunilab_size($productData['unilab_size'])
				->setCountryOfManufacture('PH') 
				->setPrice($productData['unit_price']) 
				->setCost($productData['price'])
				->setDescription($productData['description'])
				->setShortDescription($productData['short_description'])
				
				->setMediaGallery (array('images'=>array (), 'values'=>array ())) //media gallery initialization
				
				->setStockData(array(
								   'use_config_manage_stock' => 0, //'Use config settings' checkbox
								   'manage_stock'=>1, //manage stock
								   'min_sale_qty'=>1, //Minimum Qty Allowed in Shopping Cart
								  // 'max_sale_qty'=>2, //Maximum Qty Allowed in Shopping Cart
								   'is_in_stock' => 1, //Stock Availability
								   'qty' => $productData['intial_quantity']
							   ))->setCategoryIds(array(2, $productData['category']));
					
				$product->save();
				$response = true;
			
			}catch(\Exception $e){
				$this->messageManager->addError($e->getMessage());
				$response = false;
			}	
			return $response;
	}
    /**
     * @return bool
     */
    function cleanString($str) {
		$utf8='';
        $str = (string)$str;
           if( is_null($utf8) ) {
               if( !function_exists('mb_detect_encoding') ) {
                   $utf8 = (strtolower($str)=='utf-8');
               } else {
                   $length = strlen($str);
                   $utf8 = true;
                   for ($i=0; $i < $length; $i++) {
                       $c = ord($str[$i]);
                       if ($c < 0x80) $n = 0; # 0bbbbbbb
                       elseif (($c & 0xE0) == 0xC0) $n=1; # 110bbbbb
                       elseif (($c & 0xF0) == 0xE0) $n=2; # 1110bbbb
                       elseif (($c & 0xF8) == 0xF0) $n=3; # 11110bbb
                       elseif (($c & 0xFC) == 0xF8) $n=4; # 111110bb
                       elseif (($c & 0xFE) == 0xFC) $n=5; # 1111110b
                       else return false; # Does not match any model
                       for ($j=0; $j<$n; $j++) { # n bytes matching 10bbbbbb follow ?
                           if ((++$i == $length)
                               || ((ord($str[$i]) & 0xC0) != 0x80)) {
                               $utf8 = false;
                               break;
                           }

                       }
                   }
               }

           }

           if(!$utf8)
               
               $str = utf8_encode($str);

           $transliteration = array(
           'Ĳ' => 'I', 'Ö' => 'O','Œ' => 'O','Ü' => 'U','ä' => 'a','æ' => 'a',
           'ĳ' => 'i','ö' => 'o','œ' => 'o','ü' => 'u','ß' => 's','ſ' => 's',
           'À' => 'A','Á' => 'A','Â' => 'A','Ã' => 'A','Ä' => 'A','Å' => 'A',
           'Æ' => 'A','Ā' => 'A','Ą' => 'A','Ă' => 'A','Ç' => 'C','Ć' => 'C',
           'Č' => 'C','Ĉ' => 'C','Ċ' => 'C','Ď' => 'D','Đ' => 'D','È' => 'E',
           'É' => 'E','Ê' => 'E','Ë' => 'E','Ē' => 'E','Ę' => 'E','Ě' => 'E',
           'Ĕ' => 'E','Ė' => 'E','Ĝ' => 'G','Ğ' => 'G','Ġ' => 'G','Ģ' => 'G',
           'Ĥ' => 'H','Ħ' => 'H','Ì' => 'I','Í' => 'I','Î' => 'I','Ï' => 'I',
           'Ī' => 'I','Ĩ' => 'I','Ĭ' => 'I','Į' => 'I','İ' => 'I','Ĵ' => 'J',
           'Ķ' => 'K','Ľ' => 'K','Ĺ' => 'K','Ļ' => 'K','Ŀ' => 'K','Ł' => 'L',
           'Ñ' => 'N','Ń' => 'N','Ň' => 'N','Ņ' => 'N','Ŋ' => 'N','Ò' => 'O',
           'Ó' => 'O','Ô' => 'O','Õ' => 'O','Ø' => 'O','Ō' => 'O','Ő' => 'O',
           'Ŏ' => 'O','Ŕ' => 'R','Ř' => 'R','Ŗ' => 'R','Ś' => 'S','Ş' => 'S',
           'Ŝ' => 'S','Ș' => 'S','Š' => 'S','Ť' => 'T','Ţ' => 'T','Ŧ' => 'T',
           'Ț' => 'T','Ù' => 'U','Ú' => 'U','Û' => 'U','Ū' => 'U','Ů' => 'U',
           'Ű' => 'U','Ŭ' => 'U','Ũ' => 'U','Ų' => 'U','Ŵ' => 'W','Ŷ' => 'Y',
           'Ÿ' => 'Y','Ý' => 'Y','Ź' => 'Z','Ż' => 'Z','Ž' => 'Z','à' => 'a',
           'á' => 'a','â' => 'a','ã' => 'a','ā' => 'a','ą' => 'a','ă' => 'a',
           'å' => 'a','ç' => 'c','ć' => 'c','č' => 'c','ĉ' => 'c','ċ' => 'c',
           'ď' => 'd','đ' => 'd','è' => 'e','é' => 'e','ê' => 'e','ë' => 'e',
           'ē' => 'e','ę' => 'e','ě' => 'e','ĕ' => 'e','ė' => 'e','ƒ' => 'f',
           'ĝ' => 'g','ğ' => 'g','ġ' => 'g','ģ' => 'g','ĥ' => 'h','ħ' => 'h',
           'ì' => 'i','í' => 'i','î' => 'i','ï' => 'i','ī' => 'i','ĩ' => 'i',
           'ĭ' => 'i','į' => 'i','ı' => 'i','ĵ' => 'j','ķ' => 'k','ĸ' => 'k',
           'ł' => 'l','ľ' => 'l','ĺ' => 'l','ļ' => 'l','ŀ' => 'l','ñ' => 'n',
           'ń' => 'n','ň' => 'n','ņ' => 'n','ŉ' => 'n','ŋ' => 'n','ò' => 'o',
           'ó' => 'o','ô' => 'o','õ' => 'o','ø' => 'o','ō' => 'o','ő' => 'o',
           'ŏ' => 'o','ŕ' => 'r','ř' => 'r','ŗ' => 'r','ś' => 's','š' => 's',
           'ť' => 't','ù' => 'u','ú' => 'u','û' => 'u','ū' => 'u','ů' => 'u',
           'ű' => 'u','ŭ' => 'u','ũ' => 'u','ų' => 'u','ŵ' => 'w','ÿ' => 'y',
           'ý' => 'y','ŷ' => 'y','ż' => 'z','ź' => 'z','ž' => 'z','Α' => 'A',
           'Ά' => 'A','Ἀ' => 'A','Ἁ' => 'A','Ἂ' => 'A','Ἃ' => 'A','Ἄ' => 'A',
           'Ἅ' => 'A','Ἆ' => 'A','Ἇ' => 'A','ᾈ' => 'A','ᾉ' => 'A','ᾊ' => 'A',
           'ᾋ' => 'A','ᾌ' => 'A','ᾍ' => 'A','ᾎ' => 'A','ᾏ' => 'A','Ᾰ' => 'A',
           'Ᾱ' => 'A','Ὰ' => 'A','ᾼ' => 'A','Β' => 'B','Γ' => 'G','Δ' => 'D',
           'Ε' => 'E','Έ' => 'E','Ἐ' => 'E','Ἑ' => 'E','Ἒ' => 'E','Ἓ' => 'E',
           'Ἔ' => 'E','Ἕ' => 'E','Ὲ' => 'E','Ζ' => 'Z','Η' => 'I','Ή' => 'I',
           'Ἠ' => 'I','Ἡ' => 'I','Ἢ' => 'I','Ἣ' => 'I','Ἤ' => 'I','Ἥ' => 'I',
           'Ἦ' => 'I','Ἧ' => 'I','ᾘ' => 'I','ᾙ' => 'I','ᾚ' => 'I','ᾛ' => 'I',
           'ᾜ' => 'I','ᾝ' => 'I','ᾞ' => 'I','ᾟ' => 'I','Ὴ' => 'I','ῌ' => 'I',
           'Θ' => 'T','Ι' => 'I','Ί' => 'I','Ϊ' => 'I','Ἰ' => 'I','Ἱ' => 'I',
           'Ἲ' => 'I','Ἳ' => 'I','Ἴ' => 'I','Ἵ' => 'I','Ἶ' => 'I','Ἷ' => 'I',
           'Ῐ' => 'I','Ῑ' => 'I','Ὶ' => 'I','Κ' => 'K','Λ' => 'L','Μ' => 'M',
           'Ν' => 'N','Ξ' => 'K','Ο' => 'O','Ό' => 'O','Ὀ' => 'O','Ὁ' => 'O',
           'Ὂ' => 'O','Ὃ' => 'O','Ὄ' => 'O','Ὅ' => 'O','Ὸ' => 'O','Π' => 'P',
           'Ρ' => 'R','Ῥ' => 'R','Σ' => 'S','Τ' => 'T','Υ' => 'Y','Ύ' => 'Y',
           'Ϋ' => 'Y','Ὑ' => 'Y','Ὓ' => 'Y','Ὕ' => 'Y','Ὗ' => 'Y','Ῠ' => 'Y',
           'Ῡ' => 'Y','Ὺ' => 'Y','Φ' => 'F','Χ' => 'X','Ψ' => 'P','Ω' => 'O',
           'Ώ' => 'O','Ὠ' => 'O','Ὡ' => 'O','Ὢ' => 'O','Ὣ' => 'O','Ὤ' => 'O',
           'Ὥ' => 'O','Ὦ' => 'O','Ὧ' => 'O','ᾨ' => 'O','ᾩ' => 'O','ᾪ' => 'O',
           'ᾫ' => 'O','ᾬ' => 'O','ᾭ' => 'O','ᾮ' => 'O','ᾯ' => 'O','Ὼ' => 'O',
           'ῼ' => 'O','α' => 'a','ά' => 'a','ἀ' => 'a','ἁ' => 'a','ἂ' => 'a',
           'ἃ' => 'a','ἄ' => 'a','ἅ' => 'a','ἆ' => 'a','ἇ' => 'a','ᾀ' => 'a',
           'ᾁ' => 'a','ᾂ' => 'a','ᾃ' => 'a','ᾄ' => 'a','ᾅ' => 'a','ᾆ' => 'a',
           'ᾇ' => 'a','ὰ' => 'a','ᾰ' => 'a','ᾱ' => 'a','ᾲ' => 'a','ᾳ' => 'a',
           'ᾴ' => 'a','ᾶ' => 'a','ᾷ' => 'a','β' => 'b','γ' => 'g','δ' => 'd',
           'ε' => 'e','έ' => 'e','ἐ' => 'e','ἑ' => 'e','ἒ' => 'e','ἓ' => 'e',
           'ἔ' => 'e','ἕ' => 'e','ὲ' => 'e','ζ' => 'z','η' => 'i','ή' => 'i',
           'ἠ' => 'i','ἡ' => 'i','ἢ' => 'i','ἣ' => 'i','ἤ' => 'i','ἥ' => 'i',
           'ἦ' => 'i','ἧ' => 'i','ᾐ' => 'i','ᾑ' => 'i','ᾒ' => 'i','ᾓ' => 'i',
           'ᾔ' => 'i','ᾕ' => 'i','ᾖ' => 'i','ᾗ' => 'i','ὴ' => 'i','ῂ' => 'i',
           'ῃ' => 'i','ῄ' => 'i','ῆ' => 'i','ῇ' => 'i','θ' => 't','ι' => 'i',
           'ί' => 'i','ϊ' => 'i','ΐ' => 'i','ἰ' => 'i','ἱ' => 'i','ἲ' => 'i',
           'ἳ' => 'i','ἴ' => 'i','ἵ' => 'i','ἶ' => 'i','ἷ' => 'i','ὶ' => 'i',
           'ῐ' => 'i','ῑ' => 'i','ῒ' => 'i','ῖ' => 'i','ῗ' => 'i','κ' => 'k',
           'λ' => 'l','μ' => 'm','ν' => 'n','ξ' => 'k','ο' => 'o','ό' => 'o',
           'ὀ' => 'o','ὁ' => 'o','ὂ' => 'o','ὃ' => 'o','ὄ' => 'o','ὅ' => 'o',
           'ὸ' => 'o','π' => 'p','ρ' => 'r','ῤ' => 'r','ῥ' => 'r','σ' => 's',
           'ς' => 's','τ' => 't','υ' => 'y','ύ' => 'y','ϋ' => 'y','ΰ' => 'y',
           'ὐ' => 'y','ὑ' => 'y','ὒ' => 'y','ὓ' => 'y','ὔ' => 'y','ὕ' => 'y',
           'ὖ' => 'y','ὗ' => 'y','ὺ' => 'y','ῠ' => 'y','ῡ' => 'y','ῢ' => 'y',
           'ῦ' => 'y','ῧ' => 'y','φ' => 'f','χ' => 'x','ψ' => 'p','ω' => 'o',
           'ώ' => 'o','ὠ' => 'o','ὡ' => 'o','ὢ' => 'o','ὣ' => 'o','ὤ' => 'o',
           'ὥ' => 'o','ὦ' => 'o','ὧ' => 'o','ᾠ' => 'o','ᾡ' => 'o','ᾢ' => 'o',
           'ᾣ' => 'o','ᾤ' => 'o','ᾥ' => 'o','ᾦ' => 'o','ᾧ' => 'o','ὼ' => 'o',
           'ῲ' => 'o','ῳ' => 'o','ῴ' => 'o','ῶ' => 'o','ῷ' => 'o','А' => 'A',
           'Б' => 'B','В' => 'V','Г' => 'G','Д' => 'D','Е' => 'E','Ё' => 'E',
           'Ж' => 'Z','З' => 'Z','И' => 'I','Й' => 'I','К' => 'K','Л' => 'L',
           'М' => 'M','Н' => 'N','О' => 'O','П' => 'P','Р' => 'R','С' => 'S',
           'Т' => 'T','У' => 'U','Ф' => 'F','Х' => 'K','Ц' => 'T','Ч' => 'C',
           'Ш' => 'S','Щ' => 'S','Ы' => 'Y','Э' => 'E','Ю' => 'Y','Я' => 'Y',
           'а' => 'A','б' => 'B','в' => 'V','г' => 'G','д' => 'D','е' => 'E',
           'ё' => 'E','ж' => 'Z','з' => 'Z','и' => 'I','й' => 'I','к' => 'K',
           'л' => 'L','м' => 'M','н' => 'N','о' => 'O','п' => 'P','р' => 'R',
           'с' => 'S','т' => 'T','у' => 'U','ф' => 'F','х' => 'K','ц' => 'T',
           'ч' => 'C','ш' => 'S','щ' => 'S','ы' => 'Y','э' => 'E','ю' => 'Y',
           'я' => 'Y','ð' => 'd','Ð' => 'D','þ' => 't','Þ' => 'T','ა' => 'a',
           'ბ' => 'b','გ' => 'g','დ' => 'd','ე' => 'e','ვ' => 'v','ზ' => 'z',
           'თ' => 't','ი' => 'i','კ' => 'k','ლ' => 'l','მ' => 'm','ნ' => 'n',
           'ო' => 'o','პ' => 'p','ჟ' => 'z','რ' => 'r','ს' => 's','ტ' => 't',
           'უ' => 'u','ფ' => 'p','ქ' => 'k','ღ' => 'g','ყ' => 'q','შ' => 's',
           'ჩ' => 'c','ც' => 't','ძ' => 'd','წ' => 't','ჭ' => 'c','ხ' => 'k',
           'ჯ' => 'j','ჰ' => 'h'
           );
           $str = str_replace( array_keys( $transliteration ),
                               array_values( $transliteration ),
                               $str);
           return $str;
        
   }
    public function _isAllowed()
    {
        return true;
    }

}