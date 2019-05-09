<?php


namespace Peakhour\Cdn\Controller\Adminhtml\Cdn;

use Peakhour\Cdn\Model\Config;
use Peakhour\Cdn\Model\Api;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;

class FlushWildcard extends Action
{
    /**
     * @var \Peakhour\Cdn\Model\Api
     */
    private $api;

    /**
     * @var Config
     */
    private $config;


    public function __construct(
        Context $context,
        Api $api,
        Config $config
    ) {
        $this->api = $api;
        $this->config = $config;
        
        parent::__construct($context);

    }

    /**
     * Checking service details
     *
     * @return \Magento\Framework\App\ResponseInterface
     */
    public function execute()
    {
        if ($this->config->getType() == Config::PEAKHOUR && $this->config->isEnabled()) {

            // check if url is given
            $url = $this->getRequest()->getParam('url', false);

            $response = $this->api->purgeWildcard($url);
            
            if ($response['success']) {
                $this->messageManager->addSuccessMessage(
                    $this->__($response['body'])
                );
            }
        }

        return $this->_redirect('*/cache/index');

    }
}