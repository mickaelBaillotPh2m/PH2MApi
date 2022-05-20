<?php declare(strict_types=1);

namespace PH2M\EnhancedApi\Model\Swagger;

use Magento\Framework\App\State;
use Magento\Framework\App\Config\ScopeConfigInterface;

class Config extends \Magento\Swagger\Model\Config
{
    public const ENABLE_SWAGGER_XML_PATH = "webapi/swagger/enable_swagger";
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
