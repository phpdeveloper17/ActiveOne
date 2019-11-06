<?php
/**
 * Movshipping handler php.
 * @category  Unilab
 * @package   Unilab_Movshipping
 * @author    Kristian Claridad
 */
namespace Unilab\Movshipping\Logger;

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
    public $fileName = '/var/log/movshipping.log';
}
