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
        if (!isset($this->filters[$type])) {
            throw new \LogicException(sprintf('Unknown filter type "%s".', $type)); // todo
        }

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
            $values = $request->query->all();
        } elseif ($request->isMethod(Request::METHOD_POST)) {
            if ('json' === $request->getContentTypeFormat()) {
                $json = $request->getContent();
                $values = '' !== $json ? \json_decode($json, true) : [];
            } else {
                $values = $request->request->all();
            }
        } else {
            throw new MethodNotAllowedHttpException([Request::METHOD_GET, Request::METHOD_POST], sprintf('Method "%s" is not supported.', $request->getMethod()));
        }

        $data = [];
        $errors = [];
        foreach ($config as $key => $itemConfig) {
            $filter = $this->getFilter($itemConfig['type']);
            $value = $filter->isFile($itemConfig) ? $request->files->get($key) : $values[$key];

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

    public function convertFromDb(array $data, array $config): array
    {
        $result = [];
        foreach ($config as $key => $itemConfig) {
            if ($itemConfig['external'] ?? false) {
                continue;
            }

            $filter = $this->getFilter($itemConfig['type']);
            if (array_key_exists($key, $data)) {
                $result[$key] = $filter->convertFromDb($data[$key], $itemConfig);
            } else {
                $result[$key] = $filter->getDefaultValue($itemConfig);
            }
        }

        return $result;
    }

    public function convertToDb(array $data, array $config): array
    {
        $result = [];
        foreach ($data as $key => $value) {
            if (!isset($config[$key])) {
                continue;
            }

            $itemConfig = $config[$key];
            if ($itemConfig['external'] ?? false) {
                continue;
            }

            $result[$key] = $this->getFilter($itemConfig['type'])->convertToDb($value, $itemConfig);
        }

        return $result;
    }
}
