# Working with Pimf\Util\Xml

An XML util for converting XML to DOMDocument or SimpleXMLElement or to Array.

Convert string to SimpleXml instance

```php
$string    = file_get_contents('path/to/samp.xml');
$simpleXml = Pimf\Util\Xml::toSimpleXMLElement($string);
```

Convert file to SimpleXml instance

```php
$file      = 'path/to/samp.xml';
$simpleXml = Pimf\Util\Xml::toSimpleXMLElement($file);
```

Convert string to DOMDocument instance

```php
$string = file_get_contents('path/to/samp.xml');
$dom    = Pimf\Util\Xml::toDOMDocument($string);
```

Convert file to DOMDocument instance

```php
$file = 'path/to/samp.xml';
$dom  = Pimf\Util\Xml::toDOMDocument($file);
```

Convert SimpleXml instance to array

```php
$file      = 'path/to/samp.xml';
$simpleXml = Pimf\Util\Xml::toSimpleXMLElement($file);
$result    = Pimf\Util\Xml::toArray($simpleXml);
```

Convert SimpleXml to array using namespace

```php
$namespace = 'pimf'; //The XML namespace that should be fetched.
$file      = 'path/to/samp-with-namespace.xml';
$simpleXml = Pimf\Util\Xml::toSimpleXMLElement($file);
$result    = Pimf\Util\Xml::toArray($simpleXml, $namespace);
```