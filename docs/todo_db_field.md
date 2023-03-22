
```php
    public function getDbField(array $config = []): string;
```

```php
    /**
     * @dataProvider provideGetDbField
     */
    public function testGetDbField(mixed $expected, array $config = []): void
    {
        $filter = $this->getFilter();
        $this->assertSame($expected, $filter->getDbField($config));
    }

    abstract protected function provideGetDbField(): array;
```

## Boolean

```php
    public function getDbField(array $config = []): string
    {
        $default = $this->getDefaultValue($config);
        $config = array_merge($this->config, $config);
        $field = 'TINYINT(1) UNSIGNED';
        if ($config['required']) {
            $field .= ' NOT NULL';
        } elseif (null === $default) {
            $field .= ' NULL DEFAULT NULL';
        } else {
            $field .= sprintf(" NOT NULL DEFAULT '%d'", $default);
        }

        return $field;
    }
```
```php
    protected function provideGetDbField(): array
    {
        return [
            ['expected' => 'TINYINT(1) UNSIGNED NULL DEFAULT NULL'],
            [
                'expected' => 'TINYINT(1) UNSIGNED NOT NULL',
                'config' => ['required' => true],
            ],
            [
                'expected' => 'TINYINT(1) UNSIGNED NOT NULL DEFAULT 0',
                'config' => ['default' => false],
            ],
        ];
    }
```

## Float

```php
    public function getDbField(array $config = []): string
    {
        $default = $this->getDefaultValue($config);
        $config = array_merge($this->config, $config);
        $signed = $config['min'] < 0;

        if ('integer' === $config['format']) {
            $field = '';
        } else {
            // FLOAT[(M,D)] [SIGNED | UNSIGNED | ZEROFILL]
            // DOUBLE[(M,D)] [SIGNED | UNSIGNED | ZEROFILL]
            $max = $signed ? self::DB_MAX_SIGNED_FLOAT : self::DB_MAX_UNSIGNED_FLOAT;
            $field = $config['max'] > $max ? 'DOUBLE' : 'FLOAT';
            $field .= $signed ? ' SIGNED' : ' UNSIGNED';
        }

        if ($config['required']) {
            $field .= ' NOT NULL';
        } elseif (null === $default) {
            $field .= ' NULL DEFAULT NULL';
        } else {
            $field .= sprintf(" NOT NULL DEFAULT '%s'", $default);
        }

        return $field;
    }
```

## IP
```php
    public function getDbField(array $config): string
    {
        $config = array_merge($this->config, $config);
        $field = 'INT(10) UNSIGNED';
        if ($config['required']) {
            $field .= ' NOT NULL';
        } elseif (null === $config['default']) {
            $field .=  ' NULL DEFAULT NULL';
        } else {
            $field .=  sprintf(" NOT NULL DEFAULT '%d'", $config['default']);
        }

        return $field;
    }
```
```php
    protected function provideGetDbField(): array
    {
        return [
            ['expected' => 'INT(10) UNSIGNED NULL DEFAULT NULL'],
        ];
    }
```
