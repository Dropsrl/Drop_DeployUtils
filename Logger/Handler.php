<?php

namespace Drop\DeployUtils\Logger;

/**
 * Handler class
 */
class Handler extends \Magento\Framework\Logger\Handler\Base
{
    /**
     * @var int
     */
    protected $loggerType = \Monolog\Logger::INFO;

    /**
     * @var string
     */
    protected $fileName = '/var/log/deploy_utils.log';
}
