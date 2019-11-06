<?php
/**
 * Pricelevel Ui Component Action.
 * @category  Unilab
 * @package   Unilab_Benefits->Pricelevel
 * @author    Unilab
 */
namespace Unilab\Customers\Ui\Component\Listing\Group\Column;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Framework\App\ResourceConnection;

class IsActive extends Column{

    protected $connection;

    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        array $components = [],
        ResourceConnection $resource,
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);
        $this->_resource = $resource;
        $this->_objectManager = \Magento\Framework\App\ObjectManager::getInstance();
    }

    public function prepareDataSource(array $dataSource)
    {
       
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$items) {
                if($items['is_active']== 1){
                    $items['is_active'] = 'Yes';
                }else{
                    $items['is_active'] = 'No';
                }
            }
        }
        return $dataSource;
    }
    
}