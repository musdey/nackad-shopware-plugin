<?php

namespace NackadPlugin\Subscriber;

use Enlight\Event\SubscriberInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class AddJs
 * @package NackadPlugin_migra\Subscriber
 */
class AddJs implements SubscriberInterface
{

    /** @var ContainerInterface */
    private $container;
    private $pluginDirectory;


    /**
     * AddJs constructor.
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container,$pluginDirectory)
    {
        $this->container = $container;
        $this->pluginDirectory = $pluginDirectory;
    }

    /**
     * @inheritdoc
     */
    public static function getSubscribedEvents()
    {
        return [
            'Enlight_Controller_Action_PostDispatch' => 'addJavascriptFiles'
        ];
    }

    private function path(){
        return __DIR__ ;
    }

    /**
     * Provides an ArrayCollection for js compressing
     * @param Enlight_Event_EventArgs $args
     *
     * @return ArrayCollection
     */
    public function addJavascriptFiles(\Enlight_Event_EventArgs $args)
    {
        $jsFiles = array($this->pluginDirectory . '/Resources/views/frontend/_public/src/js');
        return new ArrayCollection($jsFiles);
    }
}