<?php
/**
 * Healthcredits Logger
 * @category  Unilab
 * @package   Unilab_Healthcredits
 * @author    Kristian Claridad
 */
namespace Unilab\Healthcredits\Logger;

use Monolog\Logger;

class Handler extends \Magento\Framework\Logger\Handler\Base
{
    /**
     * Logging level
     * @var int
     */
    protected $loggerType = Logger::INFO;

    /**
     * File name
     * @var string
     */
    protected $fileName = '/var/log/healthcredits.log';
}