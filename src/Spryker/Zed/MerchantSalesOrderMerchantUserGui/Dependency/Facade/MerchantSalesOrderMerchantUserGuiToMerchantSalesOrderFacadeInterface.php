<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Spryker Marketplace License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\MerchantSalesOrderMerchantUserGui\Dependency\Facade;

use Generated\Shared\Transfer\MerchantOrderCriteriaTransfer;
use Generated\Shared\Transfer\MerchantOrderTransfer;

interface MerchantSalesOrderMerchantUserGuiToMerchantSalesOrderFacadeInterface
{
    public function findMerchantOrder(MerchantOrderCriteriaTransfer $merchantCriteriaTransfer): ?MerchantOrderTransfer;

    public function getMerchantOrdersCount(MerchantOrderCriteriaTransfer $merchantOrderCriteriaTransfer): int;
}
