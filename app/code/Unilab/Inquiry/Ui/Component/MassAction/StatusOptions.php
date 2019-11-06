<?php

namespace Unilab\Inquiry\Ui\Component\MassAction;

use Magento\Framework\UrlInterface;
use Zend\Stdlib\JsonSerializable;
use Unilab\Inquiry\Model\Status;

// https://webkul.com/blog/create-dynamic-mass-action-magento-2-grid/

class StatusOptions implements JsonSerializable
{
    /**
     * @var array
     */
    protected $options;

    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * Additional options params
     *
     * @var array
     */
    protected $data;

    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * Base URL for subactions
     *
     * @var string
     */
    protected $url;

    /**
     * Param name for subactions
     *
     * @var string
     */
    protected $paramName;

    /**
     * Additional params for subactions
     *
     * @var array
     */
    protected $additionalData = [];

    /**
     * Constructor
     *
     * @param CollectionFactory $collectionFactory
     * @param UrlInterface $urlBuilder
     * @param array $data
     */
    public function __construct(
        // CollectionFactory $collectionFactory,
        UrlInterface $urlBuilder,
        Status $statusModel,
        array $data = []
    ) {
        // $this->collectionFactory = $collectionFactory;
        $this->data = $data;
        $this->urlBuilder = $urlBuilder;
        $this->_statusModel = $statusModel;
    }

    /**
     * Get action options
     *
     * @return array
     */
    public function jsonSerialize()
    {
        $i=0;
        if ($this->options === null) {
            // get the massaction data from the database table
            // $badgeColl = $this->collectionFactory->create()->addFieldToFilter('status',['eq'=>1]);
            //
            // if(!count($badgeColl)){
            //     return $this->options;
            // }

            $statusOptions = $this->_statusModel->getOptionArray();

            //make a array of massaction
            foreach ($statusOptions as $key => $status) {
                $options[$i]['value']= $status['value'];
                $options[$i]['label']= $status['label'];
                $i++;
            }

            $this->prepareData();
            foreach ($options as $optionCode) {
                $this->options[$optionCode['value']] = [
                    'type' =>  $optionCode['value'],
                    'label' => $optionCode['label'],
                ];

                if ($this->url && $this->paramName) {
                    $this->options[$optionCode['value']]['url'] = $this->urlBuilder->getUrl(
                        $this->url,
                        [$this->paramName => $optionCode['value']]
                    );
                }

                $this->options[$optionCode['value']] = array_merge_recursive(
                    $this->options[$optionCode['value']],
                    $this->additionalData
                );
            }

            // return the massaction data
            $this->options = array_values($this->options);
        }
        return $this->options;
    }

    /**
     * Prepare addition data for subactions
     *
     * @return void
     */
    protected function prepareData()
    {

        /**
        *   Change $this->urlPath to $this->url (from massaction xml)
        */

        foreach ($this->data as $key => $value) {
            switch ($key) {
                case 'url':
                    $this->url = $value;
                    break;
                case 'paramName':
                    $this->paramName = $value;
                    break;
                default:
                    $this->additionalData[$key] = $value;
                    break;
            }
        }
    }
}
