<?php

namespace Alhames\FilterBundle\Tests\FilterTests;

use Alhames\FilterBundle\Exception\FilterValueException;
use Alhames\FilterBundle\Filter\FileFilter;
use Symfony\Component\HttpFoundation\File\File;

class FileFilterTest extends AbstractFilterTestCase
{
    protected function getClass(): string
    {
        return FileFilter::class;
    }

    protected function getType(): string
    {
        return 'file';
    }

    protected function provideIsFile(): array
    {
        return [
            ['expected' => true],
        ];
    }

    protected function provideGetDefaultValue(): array
    {

        return [
            ['expected' => null],
            ['expected' => null, 'config' => ['default' => null]],
            ['expected' => null, 'config' => ['default' => '']],
        ];
    }

    public function testGetDefaultValueExtra(): void
    {
        $path = $this->getFilePath();
        $filter = $this->getFilter();
        $dir = dirname($path);

        $file1 = $filter->getDefaultValue(['default' => $path]);
        $this->assertInstanceOf(File::class, $file1);
        $this->assertSame($path, $file1->getPathname());

        $file2 = $filter->getDefaultValue(['default' => 'test.txt', 'dir' => $dir]);
        $this->assertInstanceOf(File::class, $file2);
        $this->assertSame($path, $file2->getPathname());

        $file3 = $filter->getDefaultValue(['default' => '/tmp/test.txt', 'dir' => $dir]);
        $this->assertInstanceOf(File::class, $file3);
        $this->assertSame('/tmp/test.txt', $file3->getPathname());
    }

    protected function provideConvertToDb(): array
    {
        $path = $this->getFilePath();
        $file = new File($path, false);
        $dir = dirname($path, 2);

        return [
            ['expected' => null, 'value' => null],
            ['expected' => null, 'value' => ''],
            ['expected' => $path, 'value' => $file],
            ['expected' => $path, 'value' => $file, 'config' => ['dir' => '/tmp']],
            ['expected' => 'resources/test.txt', 'value' => $file, 'config' => ['dir' => $dir]],
        ];
    }

    protected function provideConvertFromDb(): array
    {
        return [
            ['expected' => null, 'value' => null],
            ['expected' => null, 'value' => ''],
        ];
    }

    public function testConvertFromDbExtra(): void
    {
        $path = $this->getFilePath();
        $filter = $this->getFilter();
        $dir = dirname($path);

        $file1 = $filter->convertFromDb($path);
        $this->assertInstanceOf(File::class, $file1);
        $this->assertSame($path, $file1->getPathname());

        $file2 = $filter->convertFromDb('test.txt', ['dir' => $dir]);
        $this->assertInstanceOf(File::class, $file2);
        $this->assertSame($path, $file2->getPathname());

        $file3 = $filter->convertFromDb('/tmp/test.txt', ['dir' => $dir]);
        $this->assertInstanceOf(File::class, $file3);
        $this->assertSame('/tmp/test.txt', $file3->getPathname());
    }

    protected function provideConvertToResponse(): array
    {
        $path = $this->getFilePath();
        $file = new File($path, false);
        $dir = dirname($path, 2);

        return [
            ['expected' => null, 'value' => null],
            ['expected' => null, 'value' => ''],
            ['expected' => $path, 'value' => $file],
            ['expected' => $path, 'value' => $file, 'config' => ['dir' => '/tmp']],
            ['expected' => 'resources/test.txt', 'value' => $file, 'config' => ['dir' => $dir]],
        ];
    }

    protected function provideFilter(): array
    {
        return [
            'null'          => ['expected' => null, 'value' => null],
            'empty_string'  => ['expected' => null, 'value' => ''],
        ];
    }

    public function testFilterExtra(): void
    {
        $path = $this->getFilePath();
        $filter = $this->getFilter();
        $dir = dirname($path);

        $file1 = $filter->filter($path);
        $this->assertInstanceOf(File::class, $file1);
        $this->assertSame($path, $file1->getPathname());

        $file2 = $filter->filter('test.txt', ['dir' => $dir]);
        $this->assertInstanceOf(File::class, $file2);
        $this->assertSame($path, $file2->getPathname());

        $file3 = $filter->filter($path, ['max_file_size' => 1000]); // 1 Kb
        $this->assertInstanceOf(File::class, $file3);
        $this->assertSame($path, $file3->getPathname());

        $file = new File($path, false);
        $file4 = $filter->filter($file, ['dir' => '/tmp']);
        $this->assertInstanceOf(File::class, $file4);
        $this->assertSame($file, $file4);

        $splFile = new \SplFileInfo($path);
        $file5 = $filter->filter($splFile, ['dir' => '/tmp']);
        $this->assertInstanceOf(File::class, $file5);
        $this->assertSame($path, $file5->getPathname());
    }

    protected function provideFilterException(): array
    {
        $path = $this->getFilePath();
        $dir = dirname($path);

        return [
            'type_stdclass_exception' => ['exception_type' => FilterValueException::TYPE_TYPE, 'value' => new \stdClass()],
            'type_datetime_exception' => ['exception_type' => FilterValueException::TYPE_TYPE, 'value' => new \DateTime()],

            'required_null_exception' => [
                'exception_type' => FilterValueException::TYPE_REQUIRED,
                'value' => null,
                'config' => ['required' => true],
            ],
            'required_string_exception' => [
                'exception_type' => FilterValueException::TYPE_REQUIRED,
                'value' => '',
                'config' => ['required' => true],
            ],

            'file_string_exception' => ['exception_type' => FilterValueException::TYPE_FILE, 'value' => 'string'],
            'file_string1_exception' => [
                'exception_type' => FilterValueException::TYPE_FILE,
                'value' => '/tmp/test.txt',
                'config' => ['dir' => $dir],
            ],
            'file_string2_exception' => [
                'exception_type' => FilterValueException::TYPE_FILE,
                'value' => 'test1.txt',
                'config' => ['dir' => $dir],
            ],
            'file_file_exception' => [
                'exception_type' => FilterValueException::TYPE_FILE,
                'value' => new File('/tmp/test.txt', false),
                'config' => ['dir' => $dir],
            ],
            'file_splfile_exception' => [
                'exception_type' => FilterValueException::TYPE_FILE,
                'value' => new \SplFileInfo('/tmp/test.txt'),
                'config' => ['dir' => $dir],
            ],

            'max_file_size_exception' => [
                'exception_type' => FilterValueException::TYPE_MAX_FILE_SIZE,
                'value' => $path,
                'config' => ['max_file_size' => 5], // 5 bytes
            ],

            // todo check dir
        ];
    }

    private function getFilePath(): string
    {
        return realpath(__DIR__.'/../App/resources/test.txt');
    }
}
