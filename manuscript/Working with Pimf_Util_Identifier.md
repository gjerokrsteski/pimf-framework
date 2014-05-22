# Working with Pimf\Util\Identifier

Identifier util for unified resource generation. For exsample, you can create a new cache identifier based on the given parameters at the
constructor method. Integer and string but not array and objects are allowed.

Common usage

```php
$identifier = new class Pimf\Util\Identifier(1, '23', 123, 'ZZ-TOP', 'Some_Class_name');

print $identifier; // --> '1_23_123_zz_top_some_class_name'
```

Usage with custom delimiter

```php
$identifier->setDelimiter('/');

print $identifier->generate(); // --> '1/23/123/zz/top/some/class/name'
```

