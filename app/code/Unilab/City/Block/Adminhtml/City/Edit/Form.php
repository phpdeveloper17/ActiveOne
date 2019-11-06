<?php
/**
 * @category  Unilab
 * @package   Unilab_City
 * @author    Ron Mark Peroso Rudas   
 */
namespace Unilab\City\Block\Adminhtml\City\Edit;
 
class Form extends \Magento\Backend\Block\Widget\Form\Generic
{
    /**
     * @var \Magento\Store\Model\System\Store
     */
    protected $_systemStore;

    /**
     * @param \Magento\Backend\Block\Template\Context $context,
     * @param \Magento\Framework\Registry $registry,
     * @param \Magento\Framework\Data\FormFactory $formFactory,
     * @param \Magento\Cms\Model\Wysiwyg\Config $wysiwygConfig,
     * @param \Unilab\City\Model\Status $options,
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Unilab\City\Model\Status $options,
        \Unilab\City\Model\RegionFactory $regionFactory,
        array $data = []
    ) {
        $this->_options = $options;
        $this->_regionFactory = $regionFactory;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Prepare form.
     *
     * @return $this
     */
    protected function _prepareForm()
    {
        $regions 	    = array(); 
        $region_lists = array();

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $region_collection = $objectManager->create('\Unilab\City\Model\ResourceModel\Region\Collection');

        foreach($region_collection->getData() as $region){ 

            $region_lists[$region['region_id']] = array('title'	=> $region['default_name'],
                                                          'value'	=> $region['region_id'],
                                                          'label'	=> $region['default_name']);
          } 
        

        $dateFormat = $this->_localeDate->getDateFormat(\IntlDateFormatter::SHORT);
        $model = $this->_coreRegistry->registry('row_data');
        $form = $this->_formFactory->create(
            ['data' => [
                            'id' => 'edit_form',
                            'enctype' => 'multipart/form-data',
                            'action' => $this->getData('action'),
                            'method' => 'post'
                        ]
            ]
        );

        $form->setHtmlIdPrefix('city_');
        if ($model->getCityId()) {
            $fieldset = $form->addFieldset(
                'base_fieldset',
                ['legend' => __('Edit City'), 'class' => 'fieldset-wide']
            );
            $fieldset->addField('city_id', 'hidden', ['name' => 'city_id']);
        } else {
            $fieldset = $form->addFieldset(
                'base_fieldset',
                ['legend' => __('Add New City'), 'class' => 'fieldset-wide']
            );
        }

        $fieldset->addField(
            'country_id', 'select', array(
                'label'              => 'Country',
                'name'               => 'country_id',
                'class'             => 'required-entry',
                'required'          => true,
                'note'               => '',
                'style'              => '',
                'values'=> array( 'PH'=>'Philippines')
            )
        );
        $fieldset->addField(
            'name',
            'text',
            [
                'name' => 'name',
                'label' => __('City Name'),
                'id' => 'name',
                'title' => __('City Name'),
                'class' => 'required-entry',
                'required' => true,
            ]
        );

        $fieldset->addField(
            'region_id', 'select', array(
                'label'              => 'Region',
                'name'               => 'region_id',
                'class'             => 'required-entry',
                'required'          => true,
                'note'               => '',
                'style'              => '',
                'values'=> $region_lists
            )
        );

        $form->setValues($model->getData());
        $form->setUseContainer(true);
        $this->setForm($form);

        return parent::_prepareForm();
    }
}
