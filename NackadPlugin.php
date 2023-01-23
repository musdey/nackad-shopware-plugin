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
    }

    public static function getSubscribedEvents(): array
    {
        return [
            'Enlight_Controller_Action_Frontend_Checkout_Finish' => 'onPostDispatchCheckout'
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

        $orderNumber = Shopware()->Modules()->Order()->sGetOrderNumber();
        $deliverySlot = $enlightController->Request()->getParam('deliverySlot', '');
        $userComment = $enlightController->Request()->getParam('sComment','');

        $deliveryValues = explode("x",$deliverySlot);
        $deliveryDay = $deliveryValues[0];
        $slotHours = $deliveryValues[1];
        $order = Shopware()->Modules()->Order();
        if (!empty($order)) {
            $order->orderAttributes['delivery_day'] = $deliveryDay ;
            $order->orderAttributes['delivery_hour'] = $slotHours ;
        }

        $sessionData = $_SESSION;
        $sessionData["deliveryDay"] = $deliveryDay;
        $sessionData["slotHours"] = $slotHours;
        $sessionData["userComment"] = $userComment;
        $sessionData["orderNumber"] = $orderNumber;

        $this->httpPost('https://app.nackad.at/api/v1/webhooks/new-rexeat-order', $sessionData);
    }

    public function httpPost($url, $data)
    {
        $data["Shopware"]["sOrderVariables"]["sUserData"]["additional"]["user"]["password"] = "XXXX";
        // Convert the data array into a JSON string
        $jsonData = json_encode($data,JSON_PRETTY_PRINT) ;

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