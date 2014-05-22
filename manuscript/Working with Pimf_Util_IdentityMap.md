The identity map pattern is a database access design pattern used to improve performance by providing a context-specific, in-memory
cache to prevent duplicate retrieval of the same object data from the database.

```php
$blogEntry = new AnyModelObject();
$id        = 'some-unique-id-here';

$identityMap = new Pimf\Util\IdentityMap();

if (true === $identityMap->hasId($id)) {
  return $identityMap->getObject($id);
}
```

Setting objects/instances inti the identity map.

```php
$identityMap->set($id, $blogEntry);

if (true === $identityMap->hasObject($blogEntry)) {
  throw new RuntimeException('Object has an ID, cannot insert.');
}
```

Please find more here: https://github.com/gjerokrsteski/php-identity-map
