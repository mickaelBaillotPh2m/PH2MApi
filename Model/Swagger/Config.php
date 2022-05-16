<?php declare(strict_types=1);
/**
* Copyright © PH2M SARL. All rights reserved.
* See COPYING.txt for license details.
*/
namespace PH2M\CommonApi\Model\Swagger;

use Magento\Framework\App\State;
use Magento\Framework\App\Config\ScopeConfigInterface;

class Config extends \Magento\Swagger\Model\Config
{
    public const ENABLE_SWAGGER_XML_PATH = "webapi/ph2m_api/enable_swagger";
    protected ScopeConfigInterface $scopeConfig;

    /**
     * @param State $state
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(State $state, ScopeConfigInterface $scopeConfig)
    {
        $this->scopeConfig = $scopeConfig;

        parent::__construct($state, (bool)$this->scopeConfig->getValue(
            self::ENABLE_SWAGGER_XML_PATH
        ));
    }
}
