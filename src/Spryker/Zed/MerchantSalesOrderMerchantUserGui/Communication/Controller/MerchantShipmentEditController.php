<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Spryker Marketplace License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\MerchantSalesOrderMerchantUserGui\Communication\Controller;

use Spryker\Service\UtilText\Model\Url\Url;
use Spryker\Zed\MerchantSalesOrderMerchantUserGui\Communication\Controller\MerchantShipment\AbstractMerchantShipmentController;
use Symfony\Component\HttpFoundation\Request;

/**
 * @method \Spryker\Zed\MerchantSalesOrderMerchantUserGui\Communication\MerchantSalesOrderMerchantUserGuiCommunicationFactory getFactory()
 */
class MerchantShipmentEditController extends AbstractMerchantShipmentController
{
    /**
     * @var string
     */
    protected const PARAM_ID_SHIPMENT = 'id-shipment';

    /**
     * @var string
     */
    protected const MESSAGE_SHIPMENT_UPDATE_SUCCESS = 'Shipment has been successfully updated.';

    /**
     * @var string
     */
    protected const MESSAGE_SHIPMENT_UPDATE_FAIL = 'Shipment has not been updated.';

    /**
     * @var string
     */
    protected const MESSAGE_SHIPMENT_NOT_FOUND_ERROR = 'Shipment #%d not found.';

    /**
     * @var string
     */
    protected const MESSAGE_MERCHANT_REFERENCE_NOT_FOUND = 'Merchant reference not found.';

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|array<string, mixed>
     */
    public function indexAction(Request $request)
    {
        $idMerchantSalesOrder = $this->castId($request->query->get(static::PARAM_ID_MERCHANT_SALES_ORDER));
        $merchantOrderTransfer = $this->findMerchantOrder($idMerchantSalesOrder);

        if (!$merchantOrderTransfer) {
            $this->addErrorMessage(static::MESSAGE_ORDER_NOT_FOUND_ERROR, ['%d' => $idMerchantSalesOrder]);
            $redirectUrl = Url::generate(static::REDIRECT_URL_DEFAULT)->build();

            return $this->redirectResponse($redirectUrl);
        }

        $idShipment = $this->castId($request->query->get(static::PARAM_ID_SHIPMENT));
        $merchantReference = $merchantOrderTransfer->getMerchantReference();

        if (!$merchantReference) {
            $this->addErrorMessage(static::MESSAGE_MERCHANT_REFERENCE_NOT_FOUND);
            $redirectUrl = Url::generate(static::REDIRECT_URL_DEFAULT)->build();

            return $this->redirectResponse($redirectUrl);
        }

        $shipmentTransfer = $this->findShipment($merchantReference, $idShipment);

        if (!$shipmentTransfer) {
            $this->addErrorMessage(static::MESSAGE_SHIPMENT_NOT_FOUND_ERROR, ['%d' => $idShipment]);
            $redirectUrl = Url::generate(static::REDIRECT_URL_DEFAULT)->build();

            return $this->redirectResponse($redirectUrl);
        }

        $dataProvider = $this->getFactory()->createMerchantShipmentGroupFormDataProvider();
        $form = $this->getFactory()
            ->createMerchantShipmentGroupForm(
                $dataProvider->getData($merchantOrderTransfer, $shipmentTransfer),
                $dataProvider->getOptions($merchantOrderTransfer, $shipmentTransfer),
            )
            ->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $responseTransfer = $this->saveMerchantOrderShipment($form, $merchantOrderTransfer);

            if ($responseTransfer->getIsSuccessful()) {
                $this->addSuccessMessage(static::MESSAGE_SHIPMENT_UPDATE_SUCCESS);

                $redirectUrl = Url::generate(
                    static::REDIRECT_URL_DEFAULT,
                    [static::PARAM_ID_MERCHANT_SALES_ORDER => $merchantOrderTransfer->getIdMerchantOrder()],
                )->build();

                return $this->redirectResponse($redirectUrl);
            }

            $this->addErrorMessage(static::MESSAGE_SHIPMENT_UPDATE_FAIL);
        }

        return $this->viewResponse([
            'idMerchantSalesOrder' => $idMerchantSalesOrder,
            'merchantOrder' => $merchantOrderTransfer,
            'groupedMerchantOrderItems' => $this->groupMerchantOrderItemsByIdSalesOrderItem($merchantOrderTransfer),
            'form' => $form->createView(),
        ]);
    }
}
