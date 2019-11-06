<?php
/**
 * @category  Unilab
 * @package   Unilab_Adminlogs
 * @author    Kristian Claridad
 */
namespace Unilab\Adminlogs\Api\Data;

interface AdminlogsInterface
{
    /**
     * Constants for keys of data array. Identical to the name of the getter in snake case.
     */
    
    const ID = 'id';
    const LOGDATE = 'logdate';
    const USERNAME = 'username';
    const FULLNAME = 'fullname';
    const IPADDRESS = 'ipaddress';
    const ACTIVITY = 'activity';
    const STATUS = 'status';
   /**
    * Get EntityId.
    *
    * @return int
    */
    public function getId();
    public function setId($id);

    public function getLogdate();
    public function setLogdate($logdate);

    public function getUsername();
    public function setUsername($username);

    public function getFullname();
    public function setFullname($fullname);

    public function getIpaddress();
    public function setIpaddress($ipaddress);

    public function getActivity();
    public function setActivity($activity);

    public function getStatus();
    public function setStatus($status);

}
