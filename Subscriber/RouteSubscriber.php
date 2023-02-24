<?php
namespace NackadPlugin\Subscriber;

use Enlight\Event\SubscriberInterface;
use NackadPlugin\Components\DeliverySlot;
use Shopware\Components\Plugin\ConfigReader;

class RouteSubscriber implements SubscriberInterface
{
    private $pluginDirectory;
    private $deliverySlot;
    private $config;

    public static function getSubscribedEvents()
    {
        return [
            'Enlight_Controller_Action_PostDispatchSecure_Frontend_Checkout' => 'onPostDispatch',
        ];
    }
    public function __construct($pluginName, $pluginDirectory, DeliverySlot $deliverySlot, ConfigReader $configReader)
    {
        $this->pluginDirectory = $pluginDirectory;
        $this->deliverySlot = $deliverySlot;
        $this->config = $configReader->getByPluginName($pluginName);
    }

    public function onPostDispatch(\Enlight_Controller_ActionEventArgs $args)
    {
        /** @var \Enlight_Controller_Action $controller */
        $controller = $args->get('subject');
        $view = $controller->View();

        $view->addTemplateDir($this->pluginDirectory . '/Resources/views/');

        if (!$this->config['nackadContent']) {
            $view->assign('postal', $this->deliverySlot->getDeliveryPostalCode());
            $view->assign('deliverySlots', $this->deliverySlot->getDeliverySlots());
        }
    }
}