<?php

namespace Alhames\FilterBundle\Filter;

use Alhames\FilterBundle\Exception\FilterValueException;
use Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileFilter extends AbstractFilter
{
    protected array $config = [
        'default' => null,
        'required' => false,
        'type' => 'file',
        'dir' => null,
        'max_file_size' => 100_000_000, // 100 Mb
    ];

    public static function getType(): string
    {
        return 'file';
    }

    public function isFile(array $config = []): bool
    {
        return true;
    }

    public function getDefaultValue(array $config = []): ?File
    {
        $config = array_merge($this->config, $config);
        if ($this->isNull($config['default']) || !is_string($config['default'])) {
            return null;
        }

        return new File($this->addDir($config['default'], $config['dir']), false);
    }

    public function convertToResponse(mixed $value, array $config = []): ?string
    {
        return $this->convertToDb($value, $config);
    }

    public function filterRequest(mixed $value, array $config = []): mixed
    {
        $config = array_merge($this->config, $config);
        if ($this->isNull($value)) {
            if ($config['required']) {
                throw FilterValueException::create(FilterValueException::TYPE_REQUIRED, $value, $config);
            }
            return $this->getDefaultValue($config);
        }

        if (!$value instanceof UploadedFile) {
            throw FilterValueException::create(FilterValueException::TYPE_TYPE, $value, $config);
        }
        if (!$value->isValid() || 0 === $value->getSize()) {
            throw FilterValueException::create(FilterValueException::TYPE_UPLOAD, $value, $config);
        }

        return $this->runFilters($value, $config);
    }

    /**
     * @param File $value
     */
    protected function serialize(mixed $value, array $config): ?string
    {
        $path = $value->getPathname();
        if (null !== $config['dir']) {
            $dir = rtrim($config['dir'], '/').'/';
            $path = preg_replace('#^'.preg_quote($dir).'#', '', $path);
        }

        return $path;
    }

    protected function unserialize(mixed $value, array $config): ?File
    {
        if (!is_string($value)) {
            return null;
        }

        return new File($this->addDir($value, $config['dir']), false);
    }

    protected function normalize(mixed $value): ?File
    {
        if ($value instanceof File) {
            return $value;
        }
        if ($value instanceof \SplFileInfo) {
            return new File($value->getPathname(), false);
        }
        if (is_string($value) && '' !== $value) {
            return new File($value, false);
        }

        return null;
    }

    protected function filterType(mixed $value, array $config): File
    {
        if ($value instanceof File) {
            if (!$value->isFile()) {
                throw FilterValueException::create(FilterValueException::TYPE_FILE, $value, $config);
            }
            return $value;
        }

        try {
            if ($value instanceof \SplFileInfo) {
                return new File($value->getPathname());
            }
            if (is_string($value)) {
                return new File($this->addDir($value, $config['dir']));
            }
        } catch (FileNotFoundException $exception) {
            throw FilterValueException::create(FilterValueException::TYPE_FILE, $value, $config, $exception);
        }

        throw FilterValueException::create(FilterValueException::TYPE_TYPE, $value, $config);
    }

    protected function filterMaxFileSize(File $value, array $config): File
    {
        if ($config['max_file_size'] < $value->getSize()) {
            throw FilterValueException::create(FilterValueException::TYPE_MAX_FILE_SIZE, $value, $config);
        }

        return $value;
    }

    protected function addDir(string $value, ?string $dir): string
    {
        if (null === $dir || '/' === $value[0]) {
            return $value;
        }

        return rtrim($dir, '/').'/'.$value;
    }
}
