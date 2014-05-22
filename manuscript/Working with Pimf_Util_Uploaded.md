Gives a methods to act in a secure way with a file/s uploaded through a form.

Create a file instance.

```php
  $uploaded = new Pimf\Util\Uploaded(
    $_FILES['tmp_name'], $_FILES['name'], $_FILES['type'], $_FILES['size'], $_FILES['error']
  );
```

Create an instance using the factory-class for more security.

```php
  $uploaded = Pimf\Util\Uploaded\Factory::get($_FILES);
```

Let Pimf\Util\Uploaded work for you.

```php
  if ($uploaded instanceof Pimf\Util\Uploaded) {

    if(in_array($upload->getClientMimeType(), array('image/gif', 'image/png', 'image/jpg'))) {

      try {

        $uploaded->move('path/to/your/images/dir', $upload->getClientOriginalName());

      } catch (RuntimeException $e) {
        //error!!!
      }
    }
  }
```
