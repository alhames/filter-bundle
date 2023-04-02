<?php

namespace Alhames\FilterBundle\Tests\FilterTests;

use Alhames\FilterBundle\Exception\FilterValueException;
use Alhames\FilterBundle\Filter\FilterInterface;
use Alhames\FilterBundle\FilterManager;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

abstract class AbstractFilterTestCase extends WebTestCase
{
    protected const EXCEPTION = 'Error "%s" in "%s".';

    public function testClass(): void
    {
        $class = $this->getClass();
        $filter = $this->getFilter();
        $this->assertInstanceOf(FilterInterface::class, $filter);
        $this->assertInstanceOf($class, $filter);
    }

    abstract protected function getClass(): string;

    public function testGetType(): void
    {
        /** @var FilterInterface $class */
        $class = $this->getClass();
        $this->assertSame($this->getType(), $class::getType());
    }

    abstract protected function getType(): string;

    /**
     * @dataProvider provideIsFile
     */
    public function testIsFile(mixed $expected, array $config = []): void
    {
        $filter = $this->getFilter();
        $this->assertSame($expected, $filter->isFile($config));
    }

    abstract protected function provideIsFile(): array;

    /**
     * @dataProvider provideGetDefaultValue
     */
    public function testGetDefaultValue(mixed $expected, array $config = []): void
    {
        $filter = $this->getFilter();
        $this->assertSame($expected, $filter->getDefaultValue($config));
    }

    abstract protected function provideGetDefaultValue(): array;

    /**
     * @dataProvider provideConvertToDb
     */
    public function testConvertToDb(mixed $expected, mixed $value, array $config = []): void
    {
        $filter = $this->getFilter();
        $this->assertSame($expected, $filter->convertToDb($value, $config));
    }

    abstract protected function provideConvertToDb(): array;

    /**
     * @dataProvider provideConvertFromDb
     */
    public function testConvertFromDb(mixed $expected, mixed $value, array $config = []): void
    {
        $filter = $this->getFilter();
        $this->assertSame($expected, $filter->convertFromDb($value, $config));
    }

    abstract protected function provideConvertFromDb(): array;

    /**
     * @dataProvider provideConvertToResponse
     */
    public function testConvertToResponse(mixed $expected, mixed $value, array $config = []): void
    {
        $filter = $this->getFilter();
        $this->assertSame($expected, $filter->convertToResponse($value, $config));
    }

    abstract protected function provideConvertToResponse(): array;

    /**
     * @dataProvider provideFilter
     */
    public function testFilter(mixed $expected, mixed $value, array $config = []): void
    {
        $filter = $this->getFilter();
        $this->assertSame($expected, $filter->filter($value, $config));
    }

    abstract protected function provideFilter(): array;

    /**
     * @dataProvider provideFilterRequest
     */
    public function testFilterRequest(mixed $expected, mixed $value, array $config = []): void
    {
        $filter = $this->getFilter();
        $this->assertSame($expected, $filter->filterRequest($value, $config));
    }

    protected function provideFilterRequest(): array
    {
        return $this->provideFilter();
    }

    /**
     * @dataProvider provideFilterException
     */
    public function testFilterException(string $exceptionType, mixed $value, array $config = [], string $property = null): void
    {
        $this->expectException(FilterValueException::class);
        $this->expectExceptionMessage(sprintf(static::EXCEPTION, $exceptionType, $property ?? 'unknown'));

        $filter = $this->getFilter();
        $filter->filter($value, $config);
    }

    abstract protected function provideFilterException(): array;

    protected function getFilter(): FilterInterface
    {
        /** @var FilterManager $manager */
        $manager = static::getContainer()->get(FilterManager::class);

        return $manager->getFilter($this->getType());
    }
}
