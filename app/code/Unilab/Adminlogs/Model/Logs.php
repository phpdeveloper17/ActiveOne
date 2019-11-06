<?php
/**
 * Adminlogs View XML.
 * @category  Unilab
 * @package   Unilab_Adminlogs
 * @author    Kristian Claridad Modified by Reyson Aquino 
 * RRA/Adminlogsapi/Model/Apisuccess.php -> Handler
 */
namespace Unilab\Adminlogs\Model;

class Logs extends \Magento\Framework\Model\AbstractModel {
    
    protected $connection;
    protected $resource;
    protected $authSession;
    protected $_logger;
    

    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Unilab\Adminlogs\Logger\Logger $loggerInteface,
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\Backend\Model\Auth\Session $authSession

    ) {
        $this->_objectManager = $objectManager;
        $this->_resource = $resource;
        $this->authSession = $authSession;
        $this->_logger = $loggerInteface;
    }

    public function getCurrentUser()
    {
        return $this->authSession->getUser();
    }
    
    public function createLogs($activity, $status, $username = null)
	{
		date_default_timezone_set('Asia/Taipei');
        $date = date("Y-m-d H:i:s");
        
        $this->getConnection()->beginTransaction();

        $fields 			    = array();
        $fields['logdate']		= $date;
        $fields['username']     = $username;
        $fields['fullname']     = '';
        if(!$username) {
            $sessiondata = $this->getCurrentUser();
            $fields['username']		= $sessiondata['username'];
		    $fields['fullname']		= $sessiondata['firstname'] . " " . $sessiondata['lastname'];
        }

		$fields['ipaddress']	= $_SERVER['REMOTE_ADDR'];			
		$fields['activity']		= $activity; 	
		$fields['status']		= $status;	
		
		$this->getConnection()->insert('unilab_adminlogs', $fields);
		$this->getConnection()->commit();	
        
        $this->_logger->error('createLogs');
        file_put_contents('./Adminlogs.log', print_r($fields,1).PHP_EOL,FILE_APPEND);

    }
    protected function getConnection()
    {
        if (!$this->connection) {
            $this->connection = $this->_resource->getConnection('core_write');
        }
        return $this->connection;
    }
}