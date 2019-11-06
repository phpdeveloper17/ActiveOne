<?php
/**
 * @category  Unilab
 * @package   Unilab_Benefits->Productpricelist
 * @author    Kristian Claridad
 */
namespace Unilab\Benefits\Controller\Adminhtml\Productpricelist;

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
            if(strtolower($_dataVal) == 'pricelist id'):
                $format_csv++;
            elseif(strtolower($_dataVal) == 'product sku'):
                $format_csv++;
            elseif(strtolower($_dataVal) == 'product name'):
                $format_csv++;
            elseif(strtolower($_dataVal) == 'qty from'):
                $format_csv++;
            elseif(strtolower($_dataVal) == 'qty to'):
                $format_csv++;
            elseif(strtolower($_dataVal) == 'unit price'):
                $format_csv++;
            elseif(strtolower($_dataVal) == 'discount in amount'):
                $format_csv++;
            elseif(strtolower($_dataVal) == 'discount in percent'):
                $format_csv++;
            elseif(strtolower($_dataVal) == 'from date'):
                $format_csv++;
            elseif(strtolower($_dataVal) == 'to date'):
                $format_csv++;
            elseif(strtolower($_dataVal) == 'uploaded by'):
                $format_csv++;
            endif;
        endforeach;

        if (strtolower($ext) == 'csv' && $format_csv < 8){
            $this->messageManager->addErrorMessage('Incorrect content format.');
            $this->_redirect('unilab_benefits/productpricelist/import');
            return;
        }elseif(strtolower($ext) == 'csv'){

            $pricelist_id       = null;
            $product_sku        = null;
            $product_name       = null;
            $qty_from           = 0;
            $qty_to             = 0;
            $unit_price         = '0.00';
            $discount_in_amount = '0.00';
            $discount_in_percent= '0.00';
            $from_date          = date('Y-m-01', strtotime(date('Y-m-d')));
            $to_date            = date('Y-m-t', strtotime(date('Y-m-d')));;
            $uploaded_by        = $userUsername;

            $duplicateData      = null;
            $error              = false;
            $errormsg           = false;
            $count              = 1;

            $fields             = array();
            foreach($csv as $_data){

                foreach($_data as $_key=>$_value){
                    if(!empty($_value)):
                        if(strtolower($_key) == 'pricelist id'):
                            $pricelist_id  = $_value;
                        elseif(strtolower($_key) == 'product sku'):
                            $product_sku  = $_value;
                        elseif(strtolower($_key) == 'product name'):
                            $product_name  = $_value;
                        elseif(strtolower($_key) == 'qty from'):
                            $qty_from  = $_value;
                        elseif(strtolower($_key) == 'qty to'):
                            $qty_to  = $_value;
                        elseif(strtolower($_key) == 'unit price'):
                            $unit_price  = $_value;
                        elseif(strtolower($_key) == 'discount in amount'):
                            $discount_in_amount  = $_value;
                        elseif(strtolower($_key) == 'discount in percent'):
                            $discount_in_percent  = $_value;
                        elseif(strtolower($_key) == 'from date'):
                            $from_date  = $_value;
                        elseif(strtolower($_key) == 'to date'):
                            $to_date  = $_value;
                        elseif(strtolower($_key) == 'uploaded by'):
                            $uploaded_by  = $_value;
                        endif;
                    endif;
                }
                if (!empty($pricelist_id)){

                    $querypl = "SELECT * FROM rra_pricelistproduct WHERE pricelist_id LIKE '$pricelist_id'
                                AND product_sku LIKE '$product_sku'
                                AND product_name LIKE '$product_name'
                                AND qty_from LIKE '$qty_from'
                                AND qty_to LIKE '$qty_to'
                                AND unit_price LIKE '$unit_price'
                                AND discount_in_amount LIKE '$discount_in_amount'
                                AND discount_in_percent LIKE '$discount_in_percent'
                                ";
                    $queryResult = $connection->fetchRow($querypl);
                    
                    if(!empty($queryResult)){
                        $msg_stat='<span style="color:red;">Existing!</span>';
                        continue;
                    }else{
                        $msg_stat='<span style="color:green;">Success!</span>';

                        $fields['pricelist_id']         = $pricelist_id;
                        $fields['product_sku']          = $product_sku;
                        $fields['product_name']         = $product_name;
                        $fields['qty_from']             = $qty_from;
                        $fields['qty_to']               = $qty_to;
                        $fields['unit_price']           = $unit_price;
                        $fields['discount_in_amount']   = $discount_in_amount;
                        $fields['discount_in_percent']  = $discount_in_percent;
                        $fields['from_date']            = $from_date;
                        $fields['to_date']              = $to_date;
                        $fields['uploaded_by']          = $uploaded_by;
                    }
                    
                    $disp.='<label style="font-family:arial; font-size:14px;">';
                    $disp.= $count .'. '.$pricelist_id.' : '. $product_sku. ' - '. $product_name. ' - '. $msg_stat.'<br/>';
                    $disp.= '</label>';
                    $connection->insert('rra_pricelistproduct', $fields);
                }
                $count++;
            }
            $this->messageManager->addSuccessMessage('All Data was successfully imported.');
            $this->_redirect('unilab_benefits/productpricelist/import');
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
