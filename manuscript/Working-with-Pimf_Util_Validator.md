Almost every interactive web application needs to validate data. For instance, a registration form probably requires
the password to be confirmed. Maybe the e-mail address must be unique. Validations allow you to ensure that only valid
data is saved in the database.

### Common usage:
Validating incoming data to make sure data is in accordance to business logic rules is easy to do in PIMF.

```php
    // first we check the input-parameters which are send with GET http method.
    $validator = new Pimf\Util\Validator($this->request->fromGet());

    if (!$validator->digit('id') || !$validator->value('id', '>', 0)) {
      throw new Pimf\Controller\Exception('not valid entry for "id"');
    }
```

### Or factorise a validator by set of rules:
Even create a set of rules that will be used to validate each attribute of our model.
In PIMF rules are defined in an array format. Let’s take a look:

```php
     $attributes = array(
      'fname'    => 'conan',
      'age'      => 33,
      'birth'    => '12-12-2040',
      'monitor'  => 'sonyT2000',
    );

    $rules = array(
      'fname'   => 'alpha|length[>,0]|lengthBetween[1,9]',
      'age'     => 'digit|value[>,18]|value[==,33]',
      'birth'   => 'length[>,0]|date[mm-dd-yyyy]',
      'monitor' => 'alphaNumeric'
    );

    $validator = Pimf\Util\Validator\Factory::get($attributes, $rules);

     //...
    $validator->getErrors();
    $validator->getErrorMessages();
```
