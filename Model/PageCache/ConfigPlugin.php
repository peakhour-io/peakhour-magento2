<?php

namespace Peakhour\Cdn\Model\PageCache;

use \Magento\PageCache\Model\Config;

class ConfigPlugin
{
    protected $_scopeConfig;

    /**
     * ConfigPlugin constructor.
     *
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     */
    public function __construct(\Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig)
    {
        $this->_scopeConfig = $scopeConfig;
    }
    
    public function afterGetType(Config $config, $result)
    {
        if (!($config instanceof \Peakhour\Cdn\Model\Config)) {
            if ($result == \Peakhour\Cdn\Model\Config::PEAKHOUR) {
                return Config::VARNISH;
            }
        }
        return $result;
    }

    /**
     * Change return value from string to int, 'peakhour' is old value
     *
     * @param Config $config
     * @param callable $proceed
     * @return string|int
     */
    public function aroundGetType(Config $config, callable $proceed)
    {
        if ($this->_scopeConfig->getValue(Config::XML_PAGECACHE_TYPE) === 'peakhour') {
            $result = \Peakhour\Cdn\Model\Config::PEAKHOUR;
        } else {
            $result = (int)$proceed();
        }
        return $result;
    }
}
