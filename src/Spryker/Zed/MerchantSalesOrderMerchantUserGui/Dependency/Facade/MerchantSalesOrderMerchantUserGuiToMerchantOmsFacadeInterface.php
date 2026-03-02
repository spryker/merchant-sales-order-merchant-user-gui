<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Spryker Marketplace License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\MerchantSalesOrderMerchantUserGui\Dependency\Facade;

use Generated\Shared\Transfer\MerchantOmsTriggerRequestTransfer;
use Generated\Shared\Transfer\MerchantOmsTriggerResponseTransfer;
use Generated\Shared\Transfer\MerchantOrderItemCollectionTransfer;

interface MerchantSalesOrderMerchantUserGuiToMerchantOmsFacadeInterface
{
    public function expandMerchantOrderItemsWithManualEvents(
        MerchantOrderItemCollectionTransfer $merchantOrderItemCollectionTransfer
    ): MerchantOrderItemCollectionTransfer;

    /**
     * @param array<int> $merchantOrderItemIds
     *
     * @return array<int, array<\Generated\Shared\Transfer\StateMachineItemTransfer>>
     */
    public function getMerchantOrderItemsStateHistory(array $merchantOrderItemIds): array;

    public function triggerEventForMerchantOrderItems(MerchantOmsTriggerRequestTransfer $merchantOmsTriggerRequestTransfer): int;

    public function triggerEventForMerchantOrderItem(
        MerchantOmsTriggerRequestTransfer $merchantOmsTriggerRequestTransfer
    ): MerchantOmsTriggerResponseTransfer;
}
