<?php
/**
 * @category  Unilab
 * @package   Unilab_Movshipping
 * @author    Kristian Claridad   
 */
namespace Unilab\Movshipping\Block\Adminhtml\Shipping\Edit;
 
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
     * @param \Unilab\Shipping\Model\Status $options,
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Unilab\Movshipping\Model\Status $options,
        // \Unilab\City\Model\CityFactory $cityFactory,
        array $data = []
    ) {
        $this->_options = $options;
        // $this->_cityFactory = $cityFactory;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Prepare form.
     *
     * @return $this
     */
    protected function _prepareForm()
    {

        $city_list = array();

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $city_collection = $objectManager->create('\Unilab\City\Model\ResourceModel\City\Grid\Collection');
        // default value
        $city_list[''] = array( 'title'   => 'Please Select',
                                                  'value'   => '',
                                                  'label'   => 'Please Select'
                                            );

        foreach($city_collection->getData() as $city){ 

            $city_list[$city['city_id']] = array( 'title'	=> $city['name'],
                                                  'value'	=> $city['city_id'],
                                                  'label'	=> $city['name']
                                            );
          } 
        

        // $dateFormat = $this->_localeDate->getDateFormat(\IntlDateFormatter::SHORT);
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

        $form->setHtmlIdPrefix('movshipping_');
        if ($model->getMovId()) {
            $fieldset = $form->addFieldset(
                'base_fieldset',
                ['legend' => __('Edit Group'), 'class' => 'fieldset-wide']
            );
            $fieldset->addField('id', 'hidden', ['name' => 'id']);
        } else {
            $fieldset = $form->addFieldset(
                'base_fieldset',
                ['legend' => __('Add Group'), 'class' => 'fieldset-wide']
            );
        }

        
        $fieldset->addField(
            'group_name',
            'text',
            [
                'name' => 'group_name',
                'label' => __('Group'),
                'id' => 'group_name',
                'title' => __('Group'),
                'class' => 'required-entry',
                'required' => true,
            ]
        );

        $fieldset->addField(
        'listofcities',
            'multiselect',
            [
                    'name'      => 'listofcities[]',
                    'label'     => __('List of Cities'),
                    'title'     => __('List of Cities'),
                    'required'  => true,
                    'style'     =>'width:100%',
                    'size'      =>10,
                    'values'    => $city_list,
                    'disabled'  => false

            ]
        );

        $fieldset->addField(
            'greaterequal_mov',
            'text',
            [
                'name' => 'greaterequal_mov',
                'label' => __('Greater Equal MOV'),
                'id' => 'greaterequal_mov',
                'title' => __('Greater Equal MOV'),
                'class' => 'required-entry',
                'required' => true,
            ]
        );
        $fieldset->addField(
            'lessthan_mov',
            'text',
            [
                'name' => 'lessthan_mov',
                'label' => __('Less than MOV'),
                'id' => 'lessthan_mov',
                'title' => __('Less than MOV'),
                'class' => 'required-entry',
                'required' => true,
            ]
        );

        $form->setValues($model->getData());
        $form->setUseContainer(true);
        $this->setForm($form);

        return parent::_prepareForm();
    }
}
