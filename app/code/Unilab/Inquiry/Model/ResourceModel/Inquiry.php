<?php
namespace Unilab\Inquiry\Model\ResourceModel;


class Inquiry extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{

	protected $_idFieldName = 'inquiry_id';
    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $_date;

	public function __construct(
		\Magento\Framework\Model\ResourceModel\Db\Context $context,
		\Magento\Framework\Stdlib\DateTime\DateTime $date,
        $resourcePrefix = null
	)
	{
		parent::__construct($context, $resourcePrefix);
        $this->_date = $date;
	}

	protected function _construct()
	{
		$this->_init('unilab_inquiry', 'inquiry_id');
	}

}
