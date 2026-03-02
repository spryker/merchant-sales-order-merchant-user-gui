<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Spryker Marketplace License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\MerchantSalesOrderMerchantUserGui\Dependency\Facade;

use Generated\Shared\Transfer\AddressesTransfer;
use Generated\Shared\Transfer\AddressTransfer;
use Generated\Shared\Transfer\CustomerTransfer;

interface MerchantSalesOrderMerchantUserGuiToCustomerFacadeInterface
{
    /**
     * @param string $customerReference
     *
     * @return \Generated\Shared\Transfer\CustomerTransfer|null
     */
    public function findByReference($customerReference): ?CustomerTransfer;

    public function findCustomerAddressByAddressData(AddressTransfer $addressTransfer): ?AddressTransfer;

    public function getAddresses(CustomerTransfer $customerTransfer): AddressesTransfer;

    /**
     * @return array<mixed>
     */
    public function getAllSalutations(): array;
}
