

<?php

/**
 * Class Shopware_Controllers_Frontend_Nackadajax
 */
class Shopware_Controllers_Frontend_Nackadajax extends Enlight_Controller_Action
{

    public function getWhitelistedCSRFActions()
    {
        return [
            'saveslotinsession'
        ];
    }

    public function preDispatch()
    {
        if(in_array($this->Request()->getActionName(), array('index','saveslotinsession'))) {
            $this->container->get('front')->Plugins()->ViewRenderer()->setNoRender();
        }
    }

    public function indexAction(){
        //Diese Action soll kein Template laden, sondern direkt Json formatierte Daten zurückgeben
        echo 'Index Action';
    }

    public function saveslotinsessionAction(){
        $deliverySlotPlain = $this->Request()->getContent();
        $myJSONArray = json_decode( $deliverySlotPlain, true );
        $deliverySlot= $myJSONArray["deliverySlot"];
        Shopware()->Session()->deliverySlot = '';
        Shopware()->Session()->deliverySlot = $deliverySlot;
        $_SESSION['deliverySlot']=$deliverySlot;
        echo $deliverySlot;
    }
}