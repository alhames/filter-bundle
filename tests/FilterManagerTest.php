<?php

namespace Alhames\FilterBundle\Tests;

use Alhames\FilterBundle\Exception\FilterRequestException;
use Alhames\FilterBundle\FilterManager;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Yaml\Yaml;

class FilterManagerTest extends WebTestCase
{
    private const URI = 'localhost';

    public function testClass(): void
    {
        $manager = $this->getManager();
        $this->assertInstanceOf(FilterManager::class, $manager);
    }

    /**
     * @dataProvider provideFilterRequest
     */
    public function testFilterRequest(array $expected, Request $request, array $config): void
    {
        $manager = $this->getManager();
        $result = $manager->filterRequest($request, $config);
        $this->assertSame($expected, $result);
    }

    public function provideFilterRequest(): array
    {
        return $this->getFixtures(__DIR__.'/fixtures/filter_request.yaml');
    }

    /**
     * @dataProvider provideFilterRequestException
     */
    public function testFilterRequestException(array $expected, Request $request, array $config): void
    {
        $manager = $this->getManager();
        $errors = [];
        try {
            $manager->filterRequest($request, $config);
        } catch (FilterRequestException $exception) {
            foreach ($exception->getErrors() as $key => $error) {
                $errors[$key] = $error->getMessage();
            }
        }
        $this->assertSame($expected, $errors);
    }

    public function provideFilterRequestException(): array
    {
        return $this->getFixtures(__DIR__.'/fixtures/filter_request_exception.yaml');
    }

    

    private function getManager(): FilterManager
    {
        return self::getContainer()->get(FilterManager::class);
    }

    private function getFixtures(string $file): array
    {
        $fixtures = Yaml::parseFile($file);
        $data = [];
        foreach ($fixtures as $key => $item) {
            $data[$key] = [
                'expected' => $item['expected'],
                'request' => Request::create(self::URI, $item['request']['method'], $item['request']['parameters']),
                'config' => $item['config'],
            ];
        }

        return $data;
    }
}
