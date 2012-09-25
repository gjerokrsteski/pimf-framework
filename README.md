Php Is My Framework
===================
Have you ever wished a PHP framework that perfectly adapts to your projects needs, your programming experience and your customers budget? A thin PHP framewrok with less implementing rools and easy to learn how to use it? PIMF is about to satisfy your demands!

[![Build Status](https://secure.travis-ci.org/gjerokrsteski/pimf.png)](http://travis-ci.org/gjerokrsteski/pimf)

Two PIMF principles
-------------------
Most of the PHP framewroks are bigger than your problem. At all you need less than 20% of the functionality of the framework to solve you problem. Therefore we belive that the “right” 20% of the effort is the 80% of the results - and that is PIMF.

A good and robust business-logic is better that fat and complex framework.

Behind PIMF
-----------
Actually we created PIMF for personal use. The aim was to create robust and secure projects and deliver them fast. We wanted just one easy framewrok, who can be used once for all  our projects. And than - PIMF was born!

PIMFs implementation is based on well proved design patterns as well as fast object relation mapping mechanism - like famous PHP frameworks had. The architecture is designed upgrade friendly - so you can upgrade to newer versions without to override your projects. And for all of you out there, who like to create rich application interfaces with ExtJs or Dojo - we have created mechanism to couple your GUI to the controllers in a easy and fast way.

Creating new project
--------------------
- go to the "app" directory and create a new subdirectory for example "My First Blog"
<pre>
|-- app/
|   `-- MyFirstBlog/
|       |-- Controller/
</pre>

Creating new controller
------------------------
- go to "app/MyFirstBlog/" and create a new subdirectory "Controller" - the directory name is strict convention.
- create new PHP file with name for example "Index.php" into directory  "app/MyFirstBlog/Controller/"
- the file "Index.php" has to have strict class name like "MyFirstBlog_Controller_Index" and has to extend "Pimf_Controller_Abstract"
- create new method action for example "indexAction()"
- optionally you can implement the method "init()" - it will be triggered before anny action of the controller is executed.
- call the controller-action on sending the GET "index.php?controller=index&action=index" parameters to your request
<pre>
|-- app/
|   `-- MyFirstBlog/
|       |-- Controller/
|       |   '-- Index.php
</pre>


Creating new DataMapper
-----------------------
- we recommend you to get familiar with the Data-Mapper Pattern and PHP's PDO extension. You can read more here: https://github.com/gjerokrsteski/php-identity-map
- otherwise you have to go to "app/MyFirstBlog/" and create a two new subdirectories "DataMapper" and "Models" - the directories names are strict convention.
- create new model class into directory "app/MyFirstBlog/Models/" for example with name "Entry.php" and class name "MyFirstBlog_Models_Entry"
- create new data-mapper class into directory "app/MyFirstBlog/DataMapper/" for example with name "Entry.php" with class name "MyFirstBlog_DataMapper_Entry" by extending the class "Pimf_DataMapper_Abstract"
- create the CRUD methods you really need at the class "MyFirstBlog_DataMapper_Entry". For better understanding how to use PDO and how to hydrate objects, read more here: https://github.com/gjerokrsteski/pimf/blob/master/app/MyFirstBlog/DataMapper/Entry.php
<pre>
|-- app/
|   `-- MyFirstBlog/
|       |-- Controller/
|       |   '-- Index.php
|       |-- DataMapper/
|       |   '-- Entry.php
|       |-- Models/
|       |   '-- Entry.php
</pre>

Keyfeatures
-----------
- Completely unit tested in PHP version 5.3 and 5.4
- Configuration: into common way - using ini. files.
- Intelligent bootstrapping: depending on the configuration, the framework can bootstrap in testing or production mode.
- Resolver: resolves the user requests to controller and action.
- Controller and actions can be accessed from the browser and from the command line interface.
- Controller:  based on the most popular design patterns for controllers. 
 - Action naming like “public function searchAction()” convention
 - If available uses init() for preinitializing before an action is proceeded.
- Request Manager: for controlled access to the global state of the world.
 - Can manage: POST, GET, CLI params and SERVER.
 - Avoids XSS attacks.
 - Delivers methos for retrieving all server and execution environment information.
- View: a simply view for sending and rendering data
 - JSON: can send data using predefined models
 - HTML: can bind predefined template partials and render them with preasigned variables.
- Session Manager: delivers methods for save session handling.
- Entity Manager: based on PDO it is a general manager for data persistence and object relational mapping.
 - Loads the predefined data mappers and can manipulate data at the database.
 - Extends PDO for save transactions and can handle with multiple nested transactions. 
- Data Mappers: for mapping the domain models to the persistence layer.
 - Can implement a identity map. By using Data-Mapper pattern without an identity map, you can easily run into problems because you may have more than one object that references the same domain entity.
- Logger: with common logging options into a file.
 - User has to define a temporary directory for logging - 0777.
 - Can separate log messages to errors, warnings, debugging, info.
 - If php’s display errors is off, so only errors will be logged = production.
- Util Farm: a bunch of useful and proved utilities, making our programmers live easier.
 - Serializer - supports igbinary if activated, for fast serialization.
 - Property Enshurator
 - XML Convertiner - XML to DOMDocument or SimpleXMLElement or to Array.
 - Memoryusage
 - Message Formater
 - UUID Generator
 - String Util Methods
 - Identifier Generator
 - Enum Manager - gives the ability to emulate and create enumeration objects natively in PHP.
 - Validation: fast an secure validators for the common data types. Can be used as fluent interface - chaining of single validators.
 - Filtering: tainted data filtering
