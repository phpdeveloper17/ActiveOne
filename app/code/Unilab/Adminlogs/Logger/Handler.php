<?php
/**
 * Adminlogs handler php.
 * @category  Unilab
 * @package   Unilab_Adminlogs
 * @author    Kristian Claridad
 */
namespace Unilab\Adminlogs\Logger;

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
    public $fileName = '/var/log/Adminlogs.log';
}
