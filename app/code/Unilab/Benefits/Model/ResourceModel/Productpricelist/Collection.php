<?php
/**
 * @category  Unilab
 * @package   Unilab_Benefits->Productpricelist
 * @author    Kristian Claridad
 */
namespace Unilab\Benefits\Model\ResourceModel\Productpricelist;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magento\Framework\Stdlib\DateTime;
class Collection extends AbstractCollection
{
    /**
     * Define model & resource model
     */
    const YOUR_TABLE = 'rra_pricelistproduct';

    protected $_idFieldName = 'id'; //this field id used to delete [id]=> of maintable[wspi_pricelist]

    public function __construct(
        \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\DB\Adapter\AdapterInterface $connection = null,
        \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource = null
    ) {
        $this->_init(
            'Unilab\Benefits\Model\Productpricelist',
            'Unilab\Benefits\Model\ResourceModel\Productpricelist'
        );
        parent::__construct(
            $entityFactory, $logger, $fetchStrategy, $eventManager, $connection,
            $resource
        );
        $this->storeManager = $storeManager;
        
    }
    protected function _initSelect()
    {
        parent::_initSelect();
        
    }
    protected function _renderFiltersBefore()
    {
        $filters = @$_REQUEST['filters'];
        if(!empty(@$_REQUEST['filters']['webstore_id']) || !empty(@$_REQUEST['filters']['website_id'])){
            //remove filter where clause when webstore_id or website_id is not empty then compose custom  filter where
            $this->getSelect()->reset(\Magento\Framework\DB\Select::WHERE);
            $this->clear()->getSelect()->reset('where');

            foreach($filters as $key => $val){
                if($key == 'placeholder') continue;
                // if($key == 'website_id'){
                //     $key = $key.'s';
                // }
                if(is_array($val)){//check filter if array like from and to value
                    if(!empty($val['from']) || !empty($val['to'])){
                        if($this->isDate(@$val['from']) || $this->isDate(@$val['to'])){//check value if date format
                            $dateFrom = date('Y-m-d 00:00:00',strtotime(@$val['from']));
                            $dateTo = date('Y-m-d 00:00:00',strtotime(@$val['to']));
                            $this->addFieldToFilter('main_table.'.$key,['from' => $dateFrom,'to' => $dateTo]);
                        }else{//from and to like id >= 1 and id <= 1 between
                            $this->addFieldToFilter('main_table.'.$key,['from' => $val['from'],'to' => $val['to']]);
                        }
                    }else{
                        $wherein = array_map('intval', $val);// convert array value string into integer
                        $this->addFieldToFilter('main_table.'.$key,['in' => $wherein]);
                    }
                    
                }else{
                    $this->addFieldToFilter('main_table.'.$key,['like' => '%'.$val.'%']);
                }
            }
        }
        return parent::_renderFiltersBefore();
    }
    function isDate($value) 
    {
        if (!$value) {
            return false;
        }

        try {
            new \DateTime($value);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}
?>