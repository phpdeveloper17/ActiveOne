<?php
/**
 * @category Magento 2 Module
 * @package  Overdosedigital\Frontendflow
 * @author   Don Nuwinda
 */
namespace Unilab\Inquiry\Controller\Adminhtml\Inquiry;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Directory\WriteInterface;
use Magento\Ui\Component\MassAction\Filter;
use Magento\Ui\Model\Export\ConvertToCsv;
use Magento\Framework\App\Response\Http\FileFactory;
use Unilab\Inquiry\Model\ResourceModel\Inquiry\CollectionFactory;

class ExportCsv extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Backend\Model\View\Result\ForwardFactory
     */
    protected $resultForwardFactory;

    /**
     * Massactions filter
     *
     * @var Filter
     */
    protected $filter;

    /**
     * @var MetadataProvider
     */
    protected $metadataProvider;
    /**
     * @var WriteInterface
     */
    protected $directory;
    /**
     * @var ConvertToCsv
     */
    protected $converter;
    /**
     * @var FileFactory
     */
    protected $fileFactory;



    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory,
        Filter $filter,
        Filesystem $filesystem,
        ConvertToCsv $converter,
        FileFactory $fileFactory,
        \Magento\Ui\Model\Export\MetadataProvider $metadataProvider,
        \Unilab\Inquiry\Model\ResourceModel\Inquiry $resource,
        CollectionFactory $collectionFactory
        // \Magento\Store\Model\StoreManagerInterface $storeManager
        ) {
            $this->resources = $resource;
            $this->filter = $filter;
            $this->_connection = $this->resources->getConnection();
            $this->directory = $filesystem->getDirectoryWrite(DirectoryList::VAR_DIR);
            $this->metadataProvider = $metadataProvider;
            $this->converter = $converter;
            $this->fileFactory = $fileFactory;
            $this->resultForwardFactory = $resultForwardFactory;
            $this->collectionFactory = $collectionFactory;
            parent::__construct($context);
    }

    /**
     * export.
     *
     * @return \Magento\Backend\Model\View\Result\Forward
     */
    public function execute()
    {
        $collection = $this->filter->getCollection($this->collectionFactory->create());

        $component = $this->filter->getComponent();
        $this->filter->prepareComponent($component);
        $dataProvider = $component->getContext()->getDataProvider();
        $dataProvider->setLimit(0, false);
        $ids = [];

        foreach ($collection as $document) {
            $ids[] = (int)$document->getId();
        }

        $searchResult = $component->getContext()->getDataProvider()->getSearchResult();
        $fields = $this->metadataProvider->getFields($component);
        $options = $this->metadataProvider->getOptions();
        $name = md5(microtime());
        $file = 'export/'. $component->getName() . $name . '.csv';
        $this->directory->create('export');
        $stream = $this->directory->openFile($file, 'w+');
        $stream->lock();
        $stream->writeCsv($this->metadataProvider->getHeaders($component));
        foreach ($searchResult->getItems() as $document) {
            if( in_array( $document->getId(), $ids ) ) {
                $this->metadataProvider->convertDate($document, $component->getName());
                $stream->writeCsv($this->metadataProvider->getRowData($document, $fields, $options));
            }
        }
        $stream->unlock();
        $stream->close();
        return $this->fileFactory->create('export.csv', [
            'type' => 'filename',
            'value' => $file,
            'rm' => true  // can delete file after use
        ], 'var');
    }
}
