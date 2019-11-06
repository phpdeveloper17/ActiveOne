<?php

namespace Unilab\Prescription\Model;

use Magento\Framework\Model\AbstractModel;
use Magento\Framework\DataObject\IdentityInterface;

class Prescription extends AbstractModel implements IdentityInterface
{
    /**
     * Define resource model
     */

    const CACHE_TAG = 'unilab_prescription';

    protected $_cacheTag = 'unilab_prescription';

    protected $_eventPrefix = 'unilab_prescription';

    const TYPE_NEW			= 'NEW';
	const TYPE_PHOTO		= 'PHOTO';
	const TYPE_EXISTING		= 'EXISTING';
	const TYPE_NONE			= 'NONE';
	
	const STATUS_PENDING    = 'PENDING_APPROVAL';
	const STATUS_VALID      = 'VALID';
	const STATUS_INVALID    = 'INVALID';
	
	const DEFAULT_RX_IMG_SIZE = 2;

    protected function _construct()
    {
        $this->_init('Unilab\Prescription\Model\ResourceModel\Prescription');
    }

    public function getPrescriptionId(){
        return $this->getData('prescription_id');
    }

    public function setPrescriptionId($prescription_id){
        return $this->setData('prescription_id', $prescription_id);
    }

    public function getCustomerId(){
        return $this->getData('customer_id');
    }

    public function setCustomerId($customer_id){
        return $this->setData('customer_id', $customer_id);
    }

    public function getDatePrescribed(){
        return $this->getData('date_prescribed');
    }

    public function setDatePrescribed($date_prescribed){
        return $this->setData('date_prescribed', $date_prescribed);
    }

    public function getPatientName(){
        return $this->getData('patient_name');
    }

    public function setPatientName($patient_name){
        return $this->setData('patient_name', $patient_name);
    }

    public function getPtrNo(){
        return $this->getData('ptr_no');
    }

    public function setPtrNo($ptr_no){
        return $this->setData('ptr_no', $ptr_no);
    }

    public function getDoctor(){
        return $this->getData('doctor');
    }

    public function setDoctor($doctor){
        return $this->setData('doctor', $doctor);
    }

    public function getClinic(){
        return $this->getData('clinic');
    }

    public function setClinic($clinic){
        return $this->setData('clinic', $clinic);
    }

    public function getClinicAddress(){
        return $this->getData('clinic_address');
    }

    public function setClinicAddress($clinic_address){
        return $this->setData('clinic_address', $clinic_address);
    }

    public function getContactNumber(){
        return $this->getData('contact_number');
    }

    public function setContactNumber($contact_number){
        return $this->setData('contact_number', $contact_number);
    }

    public function getExpiryDate(){
        return $this->getData('expiry_date');
    }

    public function setExpiryDate($expiry_date){
        return $this->setData('expiry_date', $expiry_date);
    }

    public function getConsumed(){
        return $this->getData('consumed');
    }

    public function setConsumed($consumed){
        return $this->setData('consumed', $consumed);
    }

    public function getStatus(){
        return $this->getData('status');
    }

    public function setStatus($status){
        return $this->setData('status', $status);
    }

    public function getRemarks(){
        return $this->getData('remarks');
    }

    public function setRemarks($remarks){
        return $this->setData('remarks', $remarks);
    }

    public function getScannedRx(){
        return $this->getData('scanned_rx');
    }

    public function setScannedRx($scanned_rx){
        return $this->setData('scanned_rx', $scanned_rx);
    }

    public function getOriginalFilename(){
        return $this->getData('original_filename');
    }

    public function setOriginalFilename($original_filename){
        return $this->setData('original_filename', $original_filename);
    }

    public function getIdentities()
	{
		return [self::CACHE_TAG . '_' . $this->getId()];
	}
}