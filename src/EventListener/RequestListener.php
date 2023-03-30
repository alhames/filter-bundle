<?php

namespace Alhames\FilterBundle\EventListener;

use Alhames\FilterBundle\Exception\FilterRequestException;
use Alhames\FilterBundle\FilterManager;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;

class RequestListener implements EventSubscriberInterface
{
    private FilterManager $manager;

    public function __construct(FilterManager $manager)
    {
        $this->manager = $manager;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            RequestEvent::class => ['onKernelRequest', 10],
        ];
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $request = $event->getRequest();
        $key = $this->manager->getConfig('request_parameter');
        if (!$request->attributes->has($key)) {
            return;
        }

        $config = $request->attributes->get($key);
        $query = $this->manager->filterRequest($request, $config);
        $request->attributes->set($this->manager->getConfig('query_parameter'), $query);

        if ($this->manager->isApi($request) && !$query->isValid()) {
            throw new FilterRequestException($query);
        }
    }
}
