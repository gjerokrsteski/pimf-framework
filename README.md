Php Is My Framework
===================
Have you ever wished a PHP framework that perfectly adapts to your projects needs, your programming experience and your customers budget? A thin PHP framewrok with less implementing rools and easy to learn how to use it? PIMF is about to satisfy your demands!

[![Build Status](https://secure.travis-ci.org/gjerokrsteski/pimf.png)](http://travis-ci.org/gjerokrsteski/pimf)

PIMF (Php Is My Framework) is a micro framework for PHP that emphasises minimalism and simplicity. It is based on proven design patterns and a fast object relational mapping mechanism, and is designed to be easily updated without having to rewrite your projects. It includes mechanisms for easily coupling controllers to ExtJs and Dojo.

Usecases and documentation
--------------------------
Please read here: http://gjerokrsteski.github.com/pimf/

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
 - Validation: fast an secure validators for the common data types.
 - Filtering: tainted data filtering.
