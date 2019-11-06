<?php
/**
 * Pricelevel Ui Component Action.
 * @category  Unilab
 * @package   Unilab_Benefits->Pricelevel
 * @author    Unilab
 */
namespace Unilab\Benefits\Ui\Component\Listing\EmployeeBenefits\Column;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Framework\App\ResourceConnection;

class refreshDate extends Column{

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
				echo "<pre>";
					print_r($items);
				echo "</pre>";
                if($items['refresh_period']== 'None'){
                    $items['refresh_date'] = 'test';
                }
            }
        }
		exit();
        return $dataSource;
    }
    
}