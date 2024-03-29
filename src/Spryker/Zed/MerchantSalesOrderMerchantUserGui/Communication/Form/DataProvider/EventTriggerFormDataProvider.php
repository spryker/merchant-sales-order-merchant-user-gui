<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Spryker Marketplace License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\MerchantSalesOrderMerchantUserGui\Communication\Form\DataProvider;

use Spryker\Zed\MerchantSalesOrderMerchantUserGui\Communication\Form\EventTriggerForm;

class EventTriggerFormDataProvider
{
    /**
     * @var string
     */
    protected const SUBMIT_BUTTON_CLASS = 'btn btn-primary btn-sm trigger-event';

    /**
     * @uses \Spryker\Zed\MerchantSalesOrderMerchantUserGui\Communication\Controller\OmsTriggerController::URL_PARAM_ID_MERCHANT_SALES_ORDER
     *
     * @var string
     */
    protected const URL_PARAM_ID_MERCHANT_SALES_ORDER = 'id-merchant-sales-order';

    /**
     * @uses \Spryker\Zed\MerchantSalesOrderMerchantUserGui\Communication\Controller\OmsTriggerController::URL_PARAM_REDIRECT
     *
     * @var string
     */
    protected const URL_PARAM_REDIRECT = 'redirect';

    /**
     * @uses \Spryker\Zed\MerchantSalesOrderMerchantUserGui\Communication\Controller\OmsTriggerController::URL_PARAM_EVENT
     *
     * @var string
     */
    protected const URL_PARAM_EVENT = 'event';

    /**
     * @param int $idMerchantSalesOrder
     * @param string $event
     * @param string $redirect
     *
     * @return array<string, mixed>
     */
    public function getOptions(
        int $idMerchantSalesOrder,
        string $event,
        string $redirect
    ): array {
        return [
            EventTriggerForm::OPTION_EVENT => $event,
            EventTriggerForm::OPTION_ACTION_QUERY_PARAMETERS => [
                static::URL_PARAM_ID_MERCHANT_SALES_ORDER => $idMerchantSalesOrder,
                static::URL_PARAM_EVENT => $event,
                static::URL_PARAM_REDIRECT => $redirect,
            ],
            EventTriggerForm::OPTION_SUBMIT_BUTTON_CLASS => static::SUBMIT_BUTTON_CLASS,
        ];
    }
}
