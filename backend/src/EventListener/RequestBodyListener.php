<?php


namespace App\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class RequestBodyListener implements EventSubscriberInterface
{
    public function onKernelController(ControllerEvent $event): void
    {
        $request = $event->getRequest();

        if ($request->getContentType() !== 'json') {
            return;
        }


        $content = $request->getContent();
        if (!$content) {
            return;
        }

        $data = json_decode($content, true, 512, JSON_THROW_ON_ERROR);
        $request->request->replace(is_array($data) ? $data : []);
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::CONTROLLER => ['onKernelController', 1],
        ];
    }
}