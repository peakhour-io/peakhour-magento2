<?php

namespace Peakhour\Cdn\Model\System\Config\Source;

use Magento\PageCache\Model\System\Config\Source\Application;
use Peakhour\Cdn\Model\Config;

class ApplicationPlugin
{
    public function afterToOptionArray(Application $application, $optionArray)
    {
        return array_merge($optionArray, [['value' => Config::PEAKHOUR, 'label' => 'Peakhour.io']]);
    }

    public function afterToArray(Application $application, $optionArray)
    {
        $optionArray[Config::PEAKHOUR] = 'Peakhour.io';
        return $optionArray;
    }
}
