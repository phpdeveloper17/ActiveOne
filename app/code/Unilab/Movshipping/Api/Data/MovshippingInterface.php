<?php
/**
 * @category  Unilab
 * @package   Unilab_Movshipping
 * @author    Kristian Claridad
 */
namespace Unilab\Movshipping\Api\Data;

interface MovshippingInterface
{
    /**
     * Constants for keys of data array. Identical to the name of the getter in snake case.
     */
    const ID = 'id';
    const GROUP_NAME = 'group_name';
    const LIST_OF_CITIES = 'listofcities';
    const GREATER_EQUAL_MOV = 'greaterequal_mov';
    const LESSTHAN_MOV = 'lessthan_mov';
   /**
    * Get EntityId.
    *
    * @return int
    */
    public function getMovId();
    public function setMovId($mov_id);

    public function getMovGroup();
    public function setMovGroup($mov_group);

    public function getMovListOfCities();
    public function setMovListOfCities($listofcities);

    public function getGreaterEqualMOV();
    public function setGreaterEqualMOV($greaterEqualMOV);

    public function getLessthanMOV();
    public function setLessthanMOV($lessthan_MOV);
}
