<?php

namespace NackadPlugin\Components;

class DeliverySlot
{
    public function getDeliveryPostalCode() {
        $postal = $_SESSION["Shopware"]["sOrderVariables"]["sUserData"]["shippingaddress"]["zipcode"];
        if($postal == null) {
            $postal = $_SESSION["Shopware"]["sOrderVariables"]["sUserData"]["billingaddress"]["zipcode"];
        }
        return $postal;
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
/*        dump($data);
        die(PHP_EOL . '<br>die: ' . __FUNCTION__ . ' / ' . __FILE__ . ' / ' . __LINE__);*/

        return $response;
    }
}