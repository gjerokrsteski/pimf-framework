## Json

In PHP we have some nifty functions named **json_encode()** and **json_decode()** and PIMF makes great use of these. Why?
Because JSON is very easy to read and convenient for storing arrays and objects with values as strings.

Encode

```php
$json = \Pimf\Util\Json::encode($item);
```

Decode

```php
$item = \Pimf\Util\Json::decode($json);
```

### What can I put into JSON?
You can use any of the following data types in your JSON:

* Double
* Float
* String
* Boolean
* Array
* Object
* Null
