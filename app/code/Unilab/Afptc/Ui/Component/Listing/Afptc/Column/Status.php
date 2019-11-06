<?php
/**
 * Afptc Ui Component Action.
 * @category  Unilab
 * @package   Unilab_Afptc
 * @author    Unilab
 */
namespace Unilab\Afptc\Ui\Component\Listing\Afptc\Column;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Framework\App\ResourceConnection;

class Status extends Column{

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
       $status = '';
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$items) {
                if($items['status'] == 1){
                    $status = 'Enabled';
                }else{
                    $status = 'Disabled';
                }
                $items['status'] = $status;
            }
        }
        return $dataSource;
    }
   
}