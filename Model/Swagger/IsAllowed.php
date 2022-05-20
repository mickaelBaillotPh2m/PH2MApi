<?php declare(strict_types=1);

namespace PH2M\EnhancedApi\Model\Swagger;

use Magento\Framework\App\State;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Customer\Model\Session;

class IsAllowed extends \Magento\Swagger\Model\Config
{
    public const ENABLE_SWAGGER_XML_PATH = "webapi/swagger/enable_swagger";
    protected ScopeConfigInterface $scopeConfig;
    protected $customerSession;

    /**
     * @param State $state
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        State $state,
        ScopeConfigInterface $scopeConfig,
        Session $customerSession
    )
    {
        $this->scopeConfig = $scopeConfig;
        $this->customerSession = $customerSession;

        parent::__construct($state, (bool)$this->scopeConfig->getValue(
            self::ENABLE_SWAGGER_XML_PATH
        ));
    }


    public function isAllowedToAccessSwagger(): ?bool
    {
        return $this->customerSession->getSwaggerAuthorized();
    }

    public function checkCredentials(string $login, string $password): bool
    {
        if ($login === $this->scopeConfig->getValue('webapi/swagger/swagger_login')
            && $password === $this->scopeConfig->getValue('webapi/swagger/swagger_password')) {
            return true;
        }

        return false;
    }
}
