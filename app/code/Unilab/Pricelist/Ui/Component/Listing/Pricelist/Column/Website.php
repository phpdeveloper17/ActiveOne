<?php
/**
 * Pricelist Ui Component Action.
 * @category  Unilab
 * @package   Unilab_Pricelist
 * @author    Unilab
 */
namespace Unilab\Pricelist\Ui\Component\Listing\Pricelist\Column;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Framework\App\ResourceConnection;

class Website extends Column{

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
        $website  = $this->_objectManager->get("\Magento\Store\Model\StoreManagerInterface");
       
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$items) {
                $items['website'] = $website->getWebsite()->getName();
            }
        }
        return $dataSource;
    }
    
    protected function getConnection()
    {
        if (!$this->connection) {
            $this->connection = $this->_resource->getConnection('core_write');
        }
        return $this->connection;
    }
}