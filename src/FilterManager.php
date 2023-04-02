<?php

namespace Alhames\FilterBundle;

use Alhames\FilterBundle\Exception\FilterRequestException;
use Alhames\FilterBundle\Exception\FilterValueException;
use Alhames\FilterBundle\Filter\FilterInterface;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;

class FilterManager
{
    /** @var FilterInterface[] */
    private array $filters = [];
    private array $config = [
        'api_parameter' => '_api',
        'request_parameter' => '_request',
        'response_parameter' => '_response',
        'query_parameter' => '_query',
    ];

    public function getFilter(string $type): FilterInterface
    {
        return $this->filters[$type];
    }

    public function setFilter(string $type, FilterInterface $filter): void
    {
        $this->filters[$type] = $filter;
    }

    public function getConfig(string $key): mixed
    {
        return $this->config[$key];
    }

    public function isApi(Request $request): bool
    {
        return $request->attributes->getBoolean($this->config['api_parameter']);
    }

    /**
     * @throws MethodNotAllowedHttpException
     * @throws FilterRequestException
     */
    public function filterRequest(Request $request, array $config): array
    {
        if ($request->isMethod(Request::METHOD_GET)) {
            $queryBag = $request->query;
        } elseif ($request->isMethod(Request::METHOD_POST)) {
            if ('json' === $request->getContentTypeFormat()) {
                $json = $request->getContent();
                $queryBag = new ParameterBag('' !== $json ? \json_decode($json, true) : []);
            } else {
                $queryBag = $request->request;
            }
        } else {
            throw new MethodNotAllowedHttpException([Request::METHOD_GET, Request::METHOD_POST], sprintf('Method "%s" is not supported.', $request->getMethod()));
        }

        $data = [];
        $errors = [];
        foreach ($config as $key => $itemConfig) {
            $filter = $this->filters[$itemConfig['type']];
            $value = $filter->isFile($itemConfig) ? $request->files->get($key) : $queryBag->get($key);

            try {
                $data[$key] = $filter->filterRequest($value, $itemConfig);
            } catch (FilterValueException $e) {
                $errors[$key] = $e->prependConfigPath($key);
            }
        }

        if (!empty($errors)) {
            throw new FilterRequestException($errors);
        }

        return $data;
    }
}
