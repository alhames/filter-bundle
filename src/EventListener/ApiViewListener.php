<?php

namespace Alhames\FilterBundle\EventListener;

use Alhames\FilterBundle\FilterManager;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ViewEvent;

class ApiViewListener implements EventSubscriberInterface
{
    private FilterManager $manager;

    public function __construct(FilterManager $manager)
    {
        $this->manager = $manager;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            ViewEvent::class => 'onKernelView',
        ];
    }

    public function onKernelView(ViewEvent $event): void
    {
        $request = $event->getRequest();
        if (!$this->manager->isApi($request)) {
            return;
        }

        $data = $event->getControllerResult();
        $config = $request->attributes->get($this->manager->getConfig('response_parameter'));
        if (!empty($config)) {
            $data = $this->manager->getFilter($config['type'])->convertToResponse($data, $config);
        } else {
            $data = null;
        }
        $event->setResponse(new JsonResponse($data));
    }
}
