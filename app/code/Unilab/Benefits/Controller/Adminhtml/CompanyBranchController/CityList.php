<?php 

namespace Unilab\Benefits\Controller\Adminhtml\CompanyBranchController;

class CityList extends \Magento\Backend\App\Action
{
    protected $resultJsonFactory;

    /**
     * @param Context $context
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
    )
    {
        $this->resultJsonFactory = $resultJsonFactory;
        parent::__construct($context);
    }

    /**
     * Collect relations data
     *
     * @return \Magento\Framework\Controller\Result\Json
     */
    public function execute()
    {
        $regionId = $this->getRequest()->getParam('region_id');
        $branch_city = strtolower(trim($this->getRequest()->getParam('branch_city')));
        $datacollection = $this->_objectManager->create("Unilab\City\Model\City")->getCollection()->addFieldToFilter('secondTable.region_id', $regionId)->load();
        /** @var \Magento\Framework\Controller\Result\Json $result */
        $option = '';
        foreach($datacollection as $data){
            $branch_city_source = strtolower(trim($data->getName()));
            if($branch_city_source == $branch_city){
                $option .= '<option value="'.$data->getName().'" selected="selected">'.$data->getName().'</option>';
            }else{
                $option .= '<option value="'.$data->getName().'">'.$data->getName().'</option>';
            }
            
        }
        $resultJson = $this->resultJsonFactory->create();
        
        return $resultJson->setData($option);
    }


}