# Working with Pimf-Util-Header

The Pimf\Util\Header class is a container of static methods for HTTP headers. The Headers container will statically load Header objects
as to reduce the overhead of header specific parsing. There are several implementations for the various types of Headers that one
might encounter during the typical HTTP request.

Sometimes you will need a little more control over the response sent to the browser. For example, you may need to set a custom header
on the response, or change the HTTP status code. Here's how it can be used:

## Removes previously set headers

```php
Pimf\Util\Header::clear();
```

## Send JSON data

```php
Pimf\Util\Header::clear();
Pimf\Util\Header::contentTypeJson();

die(Pimf\Util\Json::encode(array('name'=>'Rob')));
```

## Send file

Sends file as header through any firewall and browser - IE6, IE7, IE8, IE9, FF3.6, FF11, Safari, Chrome, Opera.

```php
$fileOrString = file_get_contents('path/to/samp.txt');
$fileName     = 'a-cool-new-file-name.txt';
Pimf\Util\Header::sendDownloadDialog($fileOrString, $fileName);
```

## Redirect to location

```php
$url = 'https://github.com/gjerokrsteski/pimf/wiki':
Pimf\Util\Header::toLocation($url);
```

## Send a 500er internal server error

```php
Pimf\Util\Header::sendInternalServerError($msg = 'Whoops, we have problem!')
```

## Send a 404er page not found

```php
Pimf\Util\Header::sendNotFound($msg = 'Sorry, page not found!')
```
