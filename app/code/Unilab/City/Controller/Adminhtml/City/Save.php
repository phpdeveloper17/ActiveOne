<?php
/**
 * @category  Unilab
 * @package   Unilab_City
 * @author    Ron Mark Peroso Rudas   
 */
namespace Unilab\City\Controller\Adminhtml\City;

class Save extends \Magento\Backend\App\Action
{
    /**
     * @var \Unilab\City\Model\CityFactory
     */
    var $cityFactory;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Unilab\City\Model\CityFactory $cityFactory,
        \Unilab\City\Model\RegionFactory $regionFactory
    ) {
        parent::__construct($context);
        $this->cityFactory = $cityFactory;
        $this->regionFactory = $regionFactory;
        
    }

    public function execute()
    {
        $data = $this->getRequest()->getPostValue();
        
        if (!$data) {
            $this->_redirect('manage_cities/city/addcity');
            return;
        }
        try {
            $cityData = $this->cityFactory->create();
            $cityData->setData($data);

            $regionData = $this->regionFactory->create();
            $regionData->load($data['region_id']);

            $region_code    = $regionData->getRegionCode();

            $cityData->setRegionCode($region_code);

            if (isset($data['city_id'])) {
                $cityData->setEntityId($data['city_id']);
              
            }
            
            $cityData->save();
            $this->messageManager->addSuccess(__('City has been successfully saved.'));
        } catch (\Exception $e) {
            $this->messageManager->addError(__($e->getMessage()));
        }
        $this->_redirect('manage_cities/city/index');
    }

    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Unilab_City::save');
    }
}
