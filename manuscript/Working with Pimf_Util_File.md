## File

Gives a methods to act in a secure way with a file/s in the file system and uses SplFileInfo a high-level object
oriented interface to information for an individual file.

Create a file instance.

```php
  $file = new \Pimf\Util\File('/path/to/your/file.jpg');
```

Gives you the extension of the file.

```php
  $file->getExtension();
```

Moves the file to a new location.

```php
  $file->move('/path/to/destination/');
```

Other helpful methods that are delivered by SplFileInfo like information for an individual file, please
find at [php.net/manual/en/class.splfileinfo.php](http://php.net/manual/en/class.splfileinfo.php) page.