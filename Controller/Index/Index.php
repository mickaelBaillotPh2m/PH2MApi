<?php
/***
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Enhanced\Api\Controller\Index;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NotFoundException;
use Magento\Framework\View\Page\Config as PageConfig;
use Magento\Framework\View\Result\PageFactory as PageFactory;
use Magento\Swagger\Model\Config;
use Magento\Customer\Model\Session;
use Enhanced\Api\Model\Swagger\IsAllowed;

class Index extends Action implements HttpGetActionInterface
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
        Session $customerSession,
        IsAllowed $isAllowed,
        ?Config $config = null
    ) {
        parent::__construct($context);
        $this->pageConfig = $pageConfig;
        $this->pageFactory = $pageFactory;
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
        if ($this->isAllowed->isAllowedToAccessSwagger() !== true) {
            $redirect = $this->resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_REDIRECT);
            $redirect->setUrl('/swagger/index/login');

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
}
