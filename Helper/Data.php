<?php

namespace Peakhour\Cdn\Helper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Module\ModuleListInterface;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;
use Peakhour\Cdn\Model\Config;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{

    /**
     * @var ModuleListInterface
     */
    private $moduleList;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var Config $config,
     */
    private $config;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * Data constructor.
     * @param Context $context
     * @param ModuleListInterface $moduleList
     * @param StoreManagerInterface $storeManager
     * @param Config $config
     * @param LoggerInterface $logger
     */
    public function __construct(
        Context $context,
        ModuleListInterface $moduleList,
        StoreManagerInterface $storeManager,
        Config $config,
        LoggerInterface $logger
    ) {
        $this->moduleList = $moduleList;
        $this->storeManager = $storeManager;
        $this->config = $config;
        $this->logger = $logger;

        parent::__construct($context);
    }

    /**
     * @param $message
     */
    public function debug($message)
    {
        if ($this->config->isDebugEnabled()) {
            $this->logger->debug($message);
        }
    }
}