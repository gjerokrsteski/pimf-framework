A class that generates RFC 4122 UUIDs. This specification defines a Uniform Resource Name namespace for UUIDs (Universally Unique IDentifier),
also known as GUIDs (Globally Unique IDentifier). A UUID is 128 bits long, and requires no central registration process.

Generating a UUID

```php
$uuid = Pimf\Util\Uuid::generate();
```

Yes, it is not the same UUID as generated before :-)

```php
Pimf\Util\Uuid::generate() !== Pimf\Util\Uuid::generate()
```