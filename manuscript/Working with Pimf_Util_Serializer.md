# Working with Pimf-Util-Serializer

Due to PHP Bug #39736 - serialize() consumes insane amount of RAM. Now PIMF can put objects, strings, integers or arrays.
Even instances of SimpleXMLElement can be put too! If **igbinary** a ultra-fast PHP extention compiled than PIMF uses **igbinary_serialize** or
**igbinary_unserialize**. Igbinary is a drop in replacement for the standard php serializer. Instead of time and space consuming textual
representation, igbinary stores php data structures in compact binary form.

Serialize

```php
$serializedItem = Pimf\Util\Serializer::serialize($item);
```

Unserialize

```php
$unserializedItem = Pimf\Util\Serializer::unserialize($serializedItem);
```
