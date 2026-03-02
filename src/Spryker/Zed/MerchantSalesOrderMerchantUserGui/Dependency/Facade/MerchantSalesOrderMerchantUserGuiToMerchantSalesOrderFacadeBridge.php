<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Spryker Marketplace License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\MerchantSalesOrderMerchantUserGui\Dependency\Facade;

use Generated\Shared\Transfer\MerchantOrderCriteriaTransfer;
use Generated\Shared\Transfer\MerchantOrderTransfer;

class MerchantSalesOrderMerchantUserGuiToMerchantSalesOrderFacadeBridge implements MerchantSalesOrderMerchantUserGuiToMerchantSalesOrderFacadeInterface
{
    /**
     * @var \Spryker\Zed\MerchantSalesOrder\Business\MerchantSalesOrderFacadeInterface
     */
    protected $merchantSalesOrderFacade;

    /**
     * @param \Spryker\Zed\MerchantSalesOrder\Business\MerchantSalesOrderFacadeInterface $merchantSalesOrderFacade
     */
    public function __construct($merchantSalesOrderFacade)
    {
        $this->merchantSalesOrderFacade = $merchantSalesOrderFacade;
    }

    public function findMerchantOrder(MerchantOrderCriteriaTransfer $merchantCriteriaTransfer): ?MerchantOrderTransfer
    {
        $merchantOrderTransfer = $this->merchantSalesOrderFacade->findMerchantOrder($merchantCriteriaTransfer);

        return $merchantOrderTransfer;
    }

    public function getMerchantOrdersCount(MerchantOrderCriteriaTransfer $merchantOrderCriteriaTransfer): int
    {
        return $this->merchantSalesOrderFacade->getMerchantOrdersCount($merchantOrderCriteriaTransfer);
    }
}
