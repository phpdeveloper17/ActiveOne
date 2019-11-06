<?php
namespace Unilab\DragonPay\Model;
class Info
{

    /**
     * DragonPay payment status possible values
     *
     * @var string
     */

    // Appendix 3 – Status Codes
    const STATUS_SUCCESS = "S";
    const STATUS_FAILURE = "F";
    const STATUS_PENDING = "P";
    const STATUS_UNKNOWN = "U";
    const STATUS_REFUND = "R";
    const STATUS_CHARGEBACK = "K";
    const STATUS_VOID = "V";
    const STATUS_AUTHORIZED = "A";

    const LBL_STATUS_SUCCESS = "success";
    const LBL_STATUS_FAILURE = "failure";
    const LBL_STATUS_PENDING = "pending";
    const LBL_STATUS_UNKNOWN = "unknown";
    const LBL_STATUS_REFUND = "refund";
    const LBL_STATUS_CHARGEBACK = "charge_back";
    const LBL_STATUS_VOID = "void";
    const LBL_STATUS_AUTHORIZED = "authorized";


    //Appendix 2 – Error Codes
    protected $_errorCodes = array(
                    '000' => 'Success',
                    '101' => 'Invalid payment gateway id',
                    '102' => 'Incorrect secret key',
                    '103' => 'Invalid reference number',
                    '104' => 'Unauthorized access',
                    '105' => 'Invalid token',
                    '106' => 'Currency not supported',
                    '107' => 'Transaction cancelled',
                    '108' => 'Insufficient funds',
                    '109' => 'Transaction limit exceeded',
                    '110' => 'Error in operation',
                    '111' => 'Invalid parameters',
                    '201' => 'Invalid Merchant Id',
                    '202' => 'Invalid Merchant Password'
    );




    /**
     * Filter payment status from NVP into dragonpay/info format
     *
     * @param string $ipnPaymentStatus
     * @return string
     */
    public function getPaymentStatusLabel($ipnPaymentStatus)
    {
        switch ($ipnPaymentStatus) {
            case self::STATUS_SUCCESS   :   return self::LBL_STATUS_SUCCESS;
            case self::STATUS_UNKNOWN   :   return self::LBL_STATUS_UNKNOWN;
            case self::STATUS_FAILURE   :   return self::LBL_STATUS_FAILURE;
            case self::STATUS_PENDING   :   return self::LBL_STATUS_PENDING;
            case self::STATUS_REFUND    :   return self::LBL_STATUS_REFUND;
            case self::STATUS_CHARGEBACK:   return self::LBL_STATUS_CHARGEBACK;
            case self::STATUS_VOID      :   return self::LBL_STATUS_VOID;
            case self::STATUS_AUTHORIZED:   return self::LBL_STATUS_AUTHORIZED;
        }
        return '';
    }

}