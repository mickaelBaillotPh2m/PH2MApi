<?php
/***
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Ph2m\EnhancedApi\Controller\Index;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NotFoundException;
use Magento\Framework\View\Page\Config as PageConfig;
use Magento\Framework\View\Result\PageFactory as PageFactory;
use Magento\Swagger\Model\Config;
use Magento\Framework\App\CsrfAwareActionInterface;
use Magento\Framework\App\Request\InvalidRequestException;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Message\ManagerInterface as MessageManagerInterface;
use Magento\Customer\Model\Session;
use Ph2m\EnhancedApi\Model\Swagger\IsAllowed;

class Login extends Action implements HttpGetActionInterface, HttpPostActionInterface, CsrfAwareActionInterface
{
    /**
     * @var PageConfig
     */
    private $pageConfig;

    /**
     * @var PageFactory
     */
    private $pageFactory;

    /**
     * @var Config
     */
    private $config;

    private ScopeConfigInterface $scopeConfig;

    protected $customerSession;
    protected IsAllowed $isAllowed;

    /**
     * @param Context $context
     * @param PageConfig $pageConfig
     * @param PageFactory $pageFactory
     * @param Config|null $config
     */
    public function __construct(
        Context $context,
        PageConfig $pageConfig,
        PageFactory $pageFactory,
        ScopeConfigInterface $scopeConfig,
        Session $customerSession,
        IsAllowed $isAllowed,
        ?Config $config = null
    ) {
        parent::__construct($context);
        $this->pageConfig = $pageConfig;
        $this->pageFactory = $pageFactory;
        $this->scopeConfig = $scopeConfig;
        $this->customerSession = $customerSession;
        $this->isAllowed = $isAllowed;
        $this->config = $config ?? ObjectManager::getInstance()
                ->get(Config::class);
    }

    /**
     * @inheritDoc
     */
    public function execute()
    {
        $post = $this->getRequest()->getPostValue();

        if (isset($post['login']) && isset($post['password'])) {
            if (!$this->isAllowed->checkCredentials($post['login'], $post['password'])) {
                $this->pageConfig->addBodyClass('swagger-section');
                $this->messageManager->addErrorMessage(__('Bad credentials'));
                return $this->pageFactory->create();
            }

            $this->customerSession->setSwaggerAuthorized(true);

            $redirect = $this->resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_REDIRECT);
            $redirect->setUrl('/swagger/index');

            return $redirect;
        }

        if ($this->isAllowed->isAllowedToAccessSwagger()) {
            $redirect = $this->resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_REDIRECT);
            $redirect->setUrl('/swagger/index');

            return $redirect;
        }

        $this->pageConfig->addBodyClass('swagger-section');
        $page = $this->pageFactory->create();
        //We are using HTTP headers to control various page caches (varnish, fastly, built-in php cache)
        $page->setHeader('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0', true);

        return $page;
    }

    /**
     * @inheritDoc
     */
    public function dispatch(RequestInterface $request)
    {
        if (!$this->config->isEnabled()) {
            throw new NotFoundException(__('Page not found.'));
        }

        return parent::dispatch($request);
    }

    public function createCsrfValidationException(RequestInterface $request): ? InvalidRequestException
    {
        return null;
    }

    public function validateForCsrf(RequestInterface $request): ?bool
    {
        return true;
    }
}
