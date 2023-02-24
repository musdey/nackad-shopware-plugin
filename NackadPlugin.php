<?php

namespace NackadPlugin;

use Enlight_Controller_ActionEventArgs;
use Shopware\Components\Plugin;
use Shopware\Components\Plugin\Context\InstallContext;

class NackadPlugin extends Plugin
{

    public function install(InstallContext $context)
    {
        $service = $this->container->get('shopware_attribute.crud_service');
        $service->update('s_order_attributes', 'delivery_day', 'string',[
            'label' => 'Liefertag',
            'displayInBackend' => true
        ]);
        $service->update('s_order_attributes', 'delivery_hour', 'string',[
            'label' => 'Zeitslot',
            'displayInBackend' => true
        ]);
        // Rebuild attribute models
        /** @var ModelManager $modelManager */
        $modelManager = $this->container->get('models');
    }

    public static function getSubscribedEvents(): array
    {
        return [
            'Enlight_Controller_Action_Frontend_Checkout_Finish' => 'onPostDispatchCheckout',
            'Shopware_Modules_Order_SaveOrder_FilterParams' => 'saveOrderFilterParams',
        ];
    }

    public function onPostDispatchOrder(\Enlight_Controller_ActionEventArgs $args)
    {

        $controller = $args->getSubject();
        $request = $controller->Request();
        $view = $controller->View();

        $view->addTemplateDir(__DIR__ . '/Resources/views/');
        if ($request->getActionName() == 'load') {
            $view->extendsTemplate('backend/order/view/list.js');
        }
    }

    public function onPostDispatchCheckout(Enlight_Controller_ActionEventArgs $args){

        /** @var $enlightController Enlight_Controller_Action */
        $enlightController = $args->getSubject();

        /** @var $request Enlight_Controller_Request_RequestHttp */
        $request = $args->getRequest();

        /** @var $response Enlight_Controller_Response_ResponseHttp */
        $response = $args->getResponse();

        $sessionData = Shopware()->Session();
        $deliverySlot = Shopware()->Session()->deliverySlot;
        $orderNumber = Shopware()->Modules()->Order()->sGetOrderNumber();
        $userComment = Shopware()->Session()->sComment;
        $order = Shopware()->Modules()->Order();

/*        if(!$orderComment){
            $orderComment = $sessionData
        }*/

/*        $deliverySlot = $enlightController->Request()->getParam('deliverySlot', '');
          $userComment = $enlightController->Request()->getParam('sComment','');*/

        $deliveryValues = explode("x",$deliverySlot);
        $deliveryDay = $deliveryValues[0];
        $slotHours = $deliveryValues[1];

        $sessionData["deliveryDay"] = $deliveryDay;
        $sessionData["slotHours"] = $slotHours;
        $sessionData["userComment"] = $userComment;
        $sessionData["orderNumber"] = $orderNumber;
        $_SESSION["deliveryDay"] = $deliveryDay;
        $_SESSION["slotHours"] = $slotHours;
        $_SESSION["userComment"] = $userComment;
        $_SESSION["orderNumber"] = $orderNumber;

        if (!empty($order)) {
            $order->orderAttributes['delivery_day'] = $deliveryDay ;
            $order->orderAttributes['delivery_hour'] = $slotHours ;
        }
        $this->httpPost('https://app.nackad.at/api/v1/webhooks/new-rexeat-order', $sessionData);
    }

    /**
     *
     * @param \Enlight_Event_EventArgs $args
     * @Enlight\Event Shopware_Modules_Order_SaveOrder_FilterParams
     */
    public function saveOrderFilterParams(\Enlight_Event_EventArgs $args)
    {
        $order = $args->getSubject();
        $deliverySlot = Shopware()->Session()->deliverySlot;
        $deliveryValues = explode("x",$deliverySlot);
        $deliveryDay = $deliveryValues[0];
        $slotHours = $deliveryValues[1];
        $order->orderAttributes['delivery_day'] = $deliveryDay ;
        $order->orderAttributes['delivery_hour'] = $slotHours ;
    }

    public function httpPost($url, $data)
    {
        // Convert the data array into a JSON string
        $jsonData = json_encode($_SESSION, JSON_PRETTY_PRINT) ;
        // Set up the cURL request
        $ch = curl_init($url);

        // Tell cURL that this is a POST request
        curl_setopt($ch, CURLOPT_POST, true);

        // Attach the JSON data to the request
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);

        // Set the content type to application/json
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json','x-webhook-key:2x+rDeG%C@aZ3Xnu5g6C&sg85dYPDDqn']);

        // Execute the request
        $response = curl_exec($ch);

        // Close the connection
        curl_close($ch);
    }
}