<?php declare(strict_types=1);
/**
* Copyright © PH2M SARL. All rights reserved.
* See COPYING.txt for license details.
*/
namespace PH2M\CommonApi\Model\Swagger;

use Magento\Framework\App\State;

class Config extends \Magento\Swagger\Model\Config
{
    /**
     * @param State $state
     * @param bool $enabledInProduction
     */
    public function __construct(State $state, bool $enabledInProduction = true)
    {
        parent::__construct($state, $enabledInProduction);
    }
}
