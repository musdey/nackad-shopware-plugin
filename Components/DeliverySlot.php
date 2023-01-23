<?php

namespace NackadPlugin\Components;

use Doctrine\DBAL\Driver\Exception;

class DeliverySlot
{
    public function getDeliveryPostalCode() {
        try {
            $userData = Shopware()->Modules()->Admin()->sGetUserData();
            $postal = $userData["shippingaddress"]["zipcode"];
            return $postal;
        }catch(Exception $exception){
            // execptions
        }
    }
    public function getDeliverySlots(){

        // Set up the cURL request
        $ch = curl_init("https://app.nackad.at/api/v1/deliveryslots/rexeat");

        // Tell cURL that this is a POST request
        curl_setopt($ch, CURLOPT_GET, true);

        // Set option to populate response with data
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // Set the content type to application/json
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));

        // Execute the request
        $response = curl_exec($ch);

        // Close the connection
        curl_close($ch);

        // $data = json_decode($response);
/*      dump($data);
        die(PHP_EOL . '<br>die: ' . __FUNCTION__ . ' / ' . __FILE__ . ' / ' . __LINE__);*/

        return $response;
    }
}