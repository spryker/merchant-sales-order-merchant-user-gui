<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Spryker Marketplace License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\MerchantSalesOrderMerchantUserGui\Communication\Controller;

use Generated\Shared\Transfer\MerchantOrderCriteriaTransfer;
use Generated\Shared\Transfer\MerchantOrderTransfer;
use Generated\Shared\Transfer\ShipmentGroupResponseTransfer;
use Spryker\Service\UtilText\Model\Url\Url;
use Spryker\Zed\Kernel\Communication\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

/**
 * @method \Spryker\Zed\MerchantSalesOrderMerchantUserGui\Communication\MerchantSalesOrderMerchantUserGuiCommunicationFactory getFactory()
 * @method \Spryker\Zed\MerchantSalesOrderMerchantUserGui\Business\MerchantSalesOrderMerchantUserGuiFacadeInterface getFacade()
 */
class MerchantShipmentEditController extends AbstractController
{
    protected const PARAM_ID_MERCHANT_SALES_ORDER = 'id-merchant-sales-order';
    protected const PARAM_ID_SHIPMENT = 'id-shipment';

    protected const REDIRECT_URL_DEFAULT = '/merchant-sales-order-merchant-user-gui/detail';

    protected const MESSAGE_SHIPMENT_UPDATE_SUCCESS = 'Shipment has been successfully updated.';
    protected const MESSAGE_SHIPMENT_UPDATE_FAIL = 'Shipment has not been updated.';
    protected const MESSAGE_ORDER_NOT_FOUND_ERROR = 'Merchant sales order #%d not found.';
    protected const MESSAGE_SHIPMENT_NOT_FOUND_ERROR = 'Shipment #%d not found.';

    /**
     * @uses \Spryker\Zed\MerchantSalesOrderMerchantUserGui\Communication\Form\Shipment\MerchantShipmentGroupFormType::FIELD_SALES_ORDER_ITEMS_FORM
     */
    protected const FIELD_SALES_ORDER_ITEMS_FORM = 'items';

    /**
     * @uses \Spryker\Zed\ShipmentGui\Communication\Form\Item\ItemFormType::FIELD_IS_UPDATED
     */
    protected const FIELD_IS_UPDATED = 'is_updated';

    /**
     * @phpstan-return array<mixed>|\Symfony\Component\HttpFoundation\RedirectResponse
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @throws \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException
     *
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function indexAction(Request $request)
    {
        $idMerchantSalesOrder = $this->castId($request->query->get(static::PARAM_ID_MERCHANT_SALES_ORDER));
        $idShipment = $this->castId($request->query->get(static::PARAM_ID_SHIPMENT));
        $merchantUserTransfer = $this->getFactory()->getMerchantUserFacade()->getCurrentMerchantUser();
        $merchantOrderTransfer = $this->findMerchantOrder($idMerchantSalesOrder);

        if (!$merchantOrderTransfer) {
            $this->addErrorMessage(static::MESSAGE_ORDER_NOT_FOUND_ERROR, ['%d' => $idMerchantSalesOrder]);
            $redirectUrl = Url::generate(static::REDIRECT_URL_DEFAULT)->build();

            return $this->redirectResponse($redirectUrl);
        }

        if ($merchantUserTransfer->getMerchant()->getMerchantReference() !== $merchantOrderTransfer->getMerchantReference()) {
            throw new AccessDeniedHttpException('Access denied');
        }

        $shipmentTransfer = $this->getFactory()->getShipmentFacade()->findShipmentById($idShipment);

        if (!$shipmentTransfer) {
            $this->addErrorMessage(static::MESSAGE_SHIPMENT_NOT_FOUND_ERROR, ['%d' => $idShipment]);
            $redirectUrl = Url::generate(static::REDIRECT_URL_DEFAULT)->build();

            return $this->redirectResponse($redirectUrl);
        }

        if (!$this->getFacade()->isMerchantOrderShipment($merchantOrderTransfer, $shipmentTransfer)) {
            throw new AccessDeniedHttpException('Access denied');
        }

        $merchantOrderTransfer = $this->getFactory()
            ->getMerchantOmsFacade()
            ->expandMerchantOrderItemsWithStateHistory($merchantOrderTransfer);

        $dataProvider = $this->getFactory()->createMerchantShipmentGroupFormDataProvider();
        $form = $this->getFactory()
            ->createMerchantShipmentGroupForm(
                $dataProvider->getData($merchantOrderTransfer, $shipmentTransfer),
                $dataProvider->getOptions($merchantOrderTransfer, $shipmentTransfer)
            )
            ->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->saveMerchantOrderShipment($form, $merchantOrderTransfer);

            $redirectUrl = Url::generate(
                static::REDIRECT_URL_DEFAULT,
                [static::PARAM_ID_MERCHANT_SALES_ORDER => $merchantOrderTransfer->getIdMerchantOrder()]
            )->build();

            return $this->redirectResponse($redirectUrl);
        }

        $groupedMerchantOrderItems = [];
        foreach ($merchantOrderTransfer->getMerchantOrderItems() as $merchantOrderItem) {
            $groupedMerchantOrderItems[$merchantOrderItem->getOrderItem()->getIdSalesOrderItem()] = $merchantOrderItem;
        }

        return $this->viewResponse([
            'idMerchantSalesOrder' => $idMerchantSalesOrder,
            'merchantOrder' => $merchantOrderTransfer,
            'groupedMerchantOrderItems' => $groupedMerchantOrderItems,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param int $idMerchantSalesOrder
     *
     * @return \Generated\Shared\Transfer\MerchantOrderTransfer|null
     */
    protected function findMerchantOrder(int $idMerchantSalesOrder): ?MerchantOrderTransfer
    {
        $merchantOrderCriteriaTransfer = (new MerchantOrderCriteriaTransfer())
            ->setIdMerchantOrder($idMerchantSalesOrder)
            ->setWithItems(true)
            ->setWithOrder(true);

        $merchantOrderTransfer = $this->getFactory()
            ->getMerchantSalesOrderFacade()
            ->findMerchantOrder($merchantOrderCriteriaTransfer);

        return $merchantOrderTransfer;
    }

    /**
     * @phpstan-param \Symfony\Component\Form\FormInterface<mixed> $form
     *
     * @param \Symfony\Component\Form\FormInterface $form
     * @param \Generated\Shared\Transfer\MerchantOrderTransfer $merchantOrderTransfer
     *
     * @return void
     */
    protected function saveMerchantOrderShipment(FormInterface $form, MerchantOrderTransfer $merchantOrderTransfer): void
    {
        $shipmentGroupTransfer = $this->getFactory()
            ->getShipmentFacade()
            ->createShipmentGroupTransferWithListedItems($form->getData(), $this->getItemListUpdatedStatus($form));

        $responseTransfer = $this->getFactory()
            ->getShipmentFacade()
            ->saveShipment($shipmentGroupTransfer, $merchantOrderTransfer->getOrder());

        $this->addStatusMessage($responseTransfer);
    }

    /**
     * @phpstan-param \Symfony\Component\Form\FormInterface<mixed> $form
     *
     * @param \Symfony\Component\Form\FormInterface $form
     *
     * @return bool[]
     */
    protected function getItemListUpdatedStatus(FormInterface $form): array
    {
        if (!$form->offsetExists(static::FIELD_SALES_ORDER_ITEMS_FORM)) {
            return [];
        }

        $itemFormTypeCollection = $form->get(static::FIELD_SALES_ORDER_ITEMS_FORM);
        $requestedItems = [];
        foreach ($itemFormTypeCollection as $itemFormType) {
            $itemTransfer = $itemFormType->getData();
            /** @var \Generated\Shared\Transfer\ItemTransfer $itemTransfer */
            $requestedItems[$itemTransfer->getIdSalesOrderItem()] = $itemFormType->get(static::FIELD_IS_UPDATED)->getData();
        }

        return $requestedItems;
    }

    /**
     * @param \Generated\Shared\Transfer\ShipmentGroupResponseTransfer $responseTransfer
     *
     * @return void
     */
    protected function addStatusMessage(ShipmentGroupResponseTransfer $responseTransfer): void
    {
        if ($responseTransfer->getIsSuccessful()) {
            $this->addSuccessMessage(static::MESSAGE_SHIPMENT_UPDATE_SUCCESS);

            return;
        }

        $this->addErrorMessage(static::MESSAGE_SHIPMENT_UPDATE_FAIL);
    }
}