<?php

namespace Alhames\FilterBundle\EventListener;

use Alhames\FilterBundle\Exception\FilterRequestException;
use Alhames\FilterBundle\Exception\FilterValueException;
use Alhames\FilterBundle\FilterManager;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Contracts\Translation\TranslatorInterface;

class ApiExceptionListener implements EventSubscriberInterface
{
    private FilterManager $manager;
    private TranslatorInterface $translator;

    public function __construct(FilterManager $manager, TranslatorInterface $translator)
    {
        $this->manager = $manager;
        $this->translator = $translator;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            ExceptionEvent::class => ['onKernelException', 8],
        ];
    }

    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();
        if (!$exception instanceof FilterRequestException) {
            return;
        }

        $request = $event->getRequest();
        if (!$this->manager->isApi($request)) {
            return;
        }

        $route = $request->attributes->get('_route');
        $errors = [];
        foreach ($exception->getErrors() as $key => $error) {
            $errors[$key] = $this->convertToResponse($error, $route);
        }
        $event->setResponse(new JsonResponse($errors, Response::HTTP_BAD_REQUEST));
    }

    private function convertToResponse(FilterValueException $exception, string $route): string
    {
        $type = $exception->getType();
        $messageId = $route.'.'.$exception->getConfigPath().'.'.$type;
        $message = $this->translator->trans($messageId, $exception->getParameters(), FilterValueException::TRANSLATION_DOMAIN);
        if ($message === $messageId) {
            $message = $this->translator->trans('_default.'.$type, $exception->getParameters(), FilterValueException::TRANSLATION_DOMAIN);
        }

        return $message;
    }
}
