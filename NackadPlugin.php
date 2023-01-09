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
            'Enlight_Controller_Action_Frontend_Checkout_Finish' => 'onPostDispatchCheckout',
        ];
    }

    public function onPostDispatchCheckout(Enlight_Controller_ActionEventArgs $args){

        /** @var $enlightController Enlight_Controller_Action */
        $enlightController = $args->getSubject();

        /** @var $request Enlight_Controller_Request_RequestHttp */
        $request = $args->getRequest();

        /** @var $response Enlight_Controller_Response_ResponseHttp */
        $response = $args->getResponse();

        $deliverySlot = $enlightController->Request()->getParam('deliverySlot', '');

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

        $this->httpPost('http://localhost:3000/api/v1/webhooks/new-rexeat-order', $sessionData);
    }

    public function httpPost($url, $data)
    {
        // Convert the data array into a JSON string
        $jsonData = json_encode($data,JSON_PRETTY_PRINT) ;
        dump($data);
        //dump($data);
        //die(PHP_EOL . '<br>die: ' . __FUNCTION__ . ' / ' . __FILE__ . ' / ' . __LINE__);

        // Set up the cURL request
        $ch = curl_init($url);

        // Tell cURL that this is a POST request
        curl_setopt($ch, CURLOPT_POST, true);

        // Attach the JSON data to the request
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);

        // Set the content type to application/json
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));

        // Execute the request
        $response = curl_exec($ch);

        // Close the connection
        curl_close($ch);
    }
}