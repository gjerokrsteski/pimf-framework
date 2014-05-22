# Working with Pimf\Util\Message

Pimf\Util\Message is responsible for general message formatting, used for message flashing or in combination with your translator.

**Common usage**

```php
    $message = new Pimf\Util\Message(
       'Hello %your_name my name is %my_name! '
        .'I am %my_age, how old are you? I like %object!'
     );
     
     $message->bind('your_name', 'Ben')
             ->bind('my_name', 'Matt')
             ->bind('my_age', '21')
             ->bind('object', 'food');
    
     print $message;
    
      // .. or ..
    
     $msg = $message->format();
     
    // .. output will be..
    // "Hello Ben my name is Matt! I am 21, how old are you? I like food!"
```

**Custom prefixed delimiter for the tokens**

```php
     $message = new Efs_Util\Message(
      'Hello :your_name my name is :my_name! '
        .'I am :my_age, how old are you? I like :object!'
       );

    $message->setDelimiter(':')  <<------------------- !!!
              ->bind('your_name', 'Ben')
              ->bind('my_name', 'Matt')
              ->bind('my_age', '21')
              ->bind('object', 'food');
```

**Bind tokens at the initialisation**

```php
    $message = new Efs_Util\Message(
      'Hello %your_name my name is %my_name! '
        .'I am %my_age, how old are you? I like %object!',
      array(
        'your_name' => 'Ben',
        'my_name' => 'Matt',
        'my_age' => '21',
        'object' => 'food'
      )
    );
```