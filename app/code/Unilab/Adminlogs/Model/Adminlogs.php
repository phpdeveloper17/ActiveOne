<?php
/**
 * Adminlogs View XML.
 * @category  Unilab
 * @package   Unilab_Adminlogs
 * @author    Kristian Claridad
 */
namespace Unilab\Adminlogs\Model;

use Unilab\Adminlogs\Api\Data\AdminlogsInterface;

class Adminlogs extends \Magento\Framework\Model\AbstractModel 
{

    const CACHE_TAG = 'unilab_adminlogs';

    protected $_cacheTag = 'unilab_adminlogs';

    protected $_eventPrefix = 'unilab_adminlogs';

    protected function _construct()
    {
        $this->_init('Unilab\Adminlogs\Model\ResourceModel\Adminlogs');
    }

    public function getId(){
        return $this->getData('id');
    }
    public function setId($id){
        return $this->setData('id', $id);
    }

    public function getLogdate(){
        return $this->getData('logdate');
    }
    public function setLogdate($logdate){
        return $this->setData('logdate', $logdate);
    }

    public function getUsername(){
        return $this->getData('username');
    }
    public function setUsername($username){
        return $this->getData('username');
    }

    public function getFullname(){
        return $this->getData('fullname');
    }
    public function setFullname($fullname){
        return $this->getData('fullname');
    }

    public function getIpaddress(){
        return $this->getData('ipaddress');
    }
    public function setIpaddress($ipaddress){
        return $this->setData('ipaddress', $ipaddress);
    }

    public function getActivity(){
        return $this->getData('activity');
    }
    public function setActivity($activity){
        return $this->setData('activity', $activity);
    }

    public function getStatus(){
        return $this->getData('status');
    }
    public function setStatus($status){
        return $this->setData('status', $status);
    }

}
