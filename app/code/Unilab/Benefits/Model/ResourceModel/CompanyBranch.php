<?php
namespace Unilab\Benefits\Model\ResourceModel;


class CompanyBranch extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
	
	protected $_idFieldName = 'id';
    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $_date;
    protected $_resourceConnection;

	public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
		\Magento\Framework\App\ResourceConnection $resourceConnection,
		\Magento\Framework\Session\SessionManagerInterface $coreSession,
        \Magento\Framework\App\Filesystem\DirectoryList $directorylist,
        \Psr\Log\LoggerInterface $logger,
        $resourcePrefix = null
    ) {
        parent::__construct($context, $resourcePrefix);
        $this->_date = $date;
		$this->_resourceConnection = $resourceConnection;
		$this->_coreSession = $coreSession;
		$this->_directorylist = $directorylist;
		$this->_logger = $logger;
    }
	
	protected function _construct()
	{
		$this->_init('rra_company_branches', 'id');
	}
	
}