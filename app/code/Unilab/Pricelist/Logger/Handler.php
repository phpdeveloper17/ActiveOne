<?php
/**
 * Pricelist handler php.
 * @category  Unilab
 * @package   Unilab_Pricelist
 * @author    Kristian Claridad
 */
namespace Unilab\Pricelist\Logger;

class Handler extends \Magento\Framework\Logger\Handler\Base
{
    /**
     * Logging level.
     *
     * @var int
     */
    public $loggerType = Logger::INFO;

    /**
     * File name.
     *
     * @var string
     */
    public $fileName = '/var/log/pricelist.log';
}
