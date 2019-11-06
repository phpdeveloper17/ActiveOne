<?php
/**
 * @category  Unilab
 * @package   Unilab_Benefits
 * @author    Kristian Claridad
 */
namespace Unilab\Benefits\Controller\Adminhtml\Pricelevel;


class SubmitImport extends \Magento\Backend\App\Action
{
    /**
     * @var \Unilab\Grid\Model\GridFactory
     */
    var $gridFactory;
    protected $resourceConnection;
    protected $userSession;
    protected $messageManager;
    protected $remoteAddress;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Unilab\Grid\Model\GridFactory $gridFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Unilab\Benefits\Model\ProductpricelistFactory $gridFactory,
        \Magento\Framework\App\ResourceConnection $resourceConnection,
        \Magento\Backend\Model\Auth\Session $userSession,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Framework\HTTP\PhpEnvironment\RemoteAddress $remoteAddress
    ) {
        parent::__construct($context);
        $this->gridFactory = $gridFactory;
        $this->resourceConnection = $resourceConnection;
        $this->userSession = $userSession;
        $this->messageManager = $messageManager;
        $this->remoteAddress = $remoteAddress;
    }

    /**
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function execute()
    {
        $data = $this->getRequest()->getPostValue(); // get form key

        $visitorData = $this->remoteAddress->getRemoteAddress(true);
        $connection = $this->resourceConnection->getConnection(\Magento\Framework\App\ResourceConnection::DEFAULT_CONNECTION);
        $connection->beginTransaction();


        $fullpath = $_FILES['csv_file']['tmp_name'];
        $filename = $_FILES['csv_file']['name'];
        $size = $_FILES['csv_file']['size'];
        $ext = pathinfo($filename, PATHINFO_EXTENSION);

        //**Get User Session  
        $user                   = $this->userSession->getUser(); 
        $userId                 = $user->getUserId();
        $userUsername           = $user->getUsername();
        $msg_stat = '';
        $disp= '';
        /**
        *   Validate csv file
        */

        $csv    = array_map("str_getcsv", file($fullpath));
        $head   = array_shift($csv);
        $csv    = array_map("array_combine", array_fill(0, count($csv), $head), $csv);
        
        //Count if column is complete
        $format_csv = 0;
       
        foreach($head as $_dataVal):
            if(strtolower($_dataVal) == 'id'):
                $format_csv++;
            elseif(strtolower($_dataVal) == 'name'):
                $format_csv++;
            elseif(strtolower($_dataVal) == 'active'):
                $format_csv++;
            endif;
            
        endforeach;

        if (strtolower($ext) == 'csv' && $format_csv < 3){
            $this->messageManager->addErrorMessage('Incorrect content format.');
            $this->_redirect('unilab_benefits/pricelevel/import');
            return;
        }elseif(strtolower($ext) == 'csv'){

            $price_name         = null;
            $price_level_id     = null;
            $memo               = null;
            $prefix             = null;
            $is_active          = 0;
            
            $created_time          = date('Y-m-d');
            $update_time            = date('Y-m-d');
            $uploaded_by        = $userUsername;

            $duplicateData      = null;
            $error              = false;
            $errormsg           = false;
            $count              = 1;

            $fields             = array();
            $fields2             = array();
            foreach($csv as $_data){

                foreach($_data as $_key=>$_value){
                    if(!empty($_value)):
                        if(strtolower($_key) == 'id'):
                            $price_level_id  = $_value;
                        elseif(strtolower($_key) == 'name'):
                            $price_name  = $_value;
                        elseif(strtolower($_key) == 'active'):
                            $is_active  = $_value;
                        endif;
                    endif;
                }
                if (!empty($price_level_id)){

                    $querypl = "SELECT * FROM rra_pricelevelmaster WHERE price_level_id LIKE '$price_level_id'
                                AND price_name LIKE '$price_name'
                              
                                ";
                    $queryResult = $connection->fetchRow($querypl);
                    
                    if(!empty($queryResult)){
                        $msg_stat='<span style="color:red;">Existing!</span>';
                        continue;
                    }else{
                        $msg_stat='<span style="color:green;">Success!</span>';

                        $fields['price_level_id']       = $price_level_id;
                        $fields['price_name']           = $price_name;
                        $fields['is_active']            = ($is_active=='YES')?1:0;
                        $fields['created_time']         = $created_time;
                        $fields['update_time']          = $update_time;
                    }
                   
                    
                    $res1 = $connection->insert('rra_pricelevelmaster', $fields);

                    // if($res1){
                    //     $fields2['price_level_id']       = $price_level_id;
                    //     $fields2['name']                 = $price_name;
                    //     $fields2['active']               = $is_active;
                    //     $fields2['id']                   = $count;
                    //     $connection->insert('wspi_pricelevel', $fields2);
                    // }
                   
                    $disp.='<label style="font-family:arial; font-size:14px;">';
                    $disp.= $count .'. '.$price_level_id.' - '. $price_name. ' - '. $msg_stat.'<br/>';
                    $disp.= '</label>';

                }
                $count++;
            }
        //     echo  "<pre>";
        //     print_r($disp);
        // echo "</pre>";
            $this->messageManager->addSuccessMessage('All Data was successfully imported.');
            $this->_redirect('unilab_benefits/pricelevel/import');
        }
       
        $connection->commit();
    }
    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return true;
    }

}
