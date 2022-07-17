# Ping
Lightweight framework built around dependency injection container and php8 attributes.
Basic port of java spring framework in PHP.


# Inversion of Control (IoC) and Dependency Injection (DI)
Inversion of Control is a principle in software engineering which transfers the control of objects or portions of a program to a container or framework. We most often use it in the context of object-oriented programming.

In contrast with traditional programming, in which our custom code makes calls to a library, IoC enables a framework to take control of the flow of a program and make calls to our custom code. To enable this, frameworks use abstractions with additional behavior built in. If we want to add our own behavior, we need to extend the classes of the framework or plugin our own classes.

The advantages of this architecture are:

- decoupling the execution of a task from its implementation
- making it easier to switch between different implementations
- greater modularity of a program
- greater ease in testing a program by isolating a component or mocking its dependencies, and allowing components to communicate through contracts

We can achieve Inversion of Control through various mechanisms such as: Strategy design pattern, Service Locator pattern, Factory pattern, and Dependency Injection (DI).

# Dependency Container (DC)
Ping implements very specific PSR-11 dependency container. 
Unlike the classical dependency container it doesn't collect dependencies definitions first then resolves it on demand,
it stores only resolved services without definitions, because the runtime is split on 2 stages:
1. init stage - when application is starting, building container, initializing services 
2. either request listening stage (in web application) - when application is already initialized and just reacting on requests
3. or execution stage (in console application) - when application also is already initialized and executing command

# Attributes 
The PHP Programming language provided support for Annotations/Attributes from PHP 8.0. 

Prior to annotations, the behavior of the Ping Framework was largely controlled through Dependency Container configuration. 
Today, the use of annotations provide us tremendous capabilities in how we configure the behaviors of the Ping Framework.


## #[Service]
This annotation is used on classes to indicate a Ping service. The #[Service] annotation marks the PHP class as a service so that the attribute-scanning mechanism of Ping can add into the dependency container.

## #[Inject]
This annotation is used at the field, constructor parameter, and method parameter level. The #[Inject] annotation indicates a default value expression for the field or parameter to initialize the property with.

## #[Variadic]
This annotation is used on classes to indicate a variadic dependency. The #[Variadic] annotation marks the PHP class as a service which should be passed into target service constructor as a variadic dependency.

## #[Autowired]
This annotation is applied on classes and methods. 
The #[Autowired] annotation marks the PHP class as a service and resolves it automatically right after container is built.

When you use #[Autowired] on method, the method is calling automatically right after class is instantiated. 

## DependencyContainerConfigurator
This annotation is used on classes to indicate a dependency container configurator. 

The class must implement a DependencyContainerConfigurator interface.

The static method "configureDependencyContainer" will be called automatically by container builder
to make it possible to put already resolved dependencies into container. 
That's how you can override automatically defined services.

# Utils
Ping framework provides a bunch of common utils classes/helpers, such as: json encoder and decoder, environment reader/writer, strings, arrays with streams, etc...

## Arrays Getter Helper
```php
$value1 = Arrays::mustGetInt(['k1' => 1], 'k1');
$null = Arrays::mayGetInt(['k1' => 1], 'k2');
```

## Arrays stream
Wraps the array with FluentTraversable.

[FluentTraversable](https://github.com/psliwa/fluent-traversable) is small tool that adds a bit of functional programming to php, especially for arrays and collections. This library is inspired by java8 stream framework, guava FluentIterable and Scala functional features.

### Quick Example:
```php
$books = array(/* some books */);
    
$emails = Arrays::stream($books)
    ->filter(is::lt('releaseDate', 2007))
    ->flatMap(get::value('authors'))
    ->filter(is::eq('sex', 'male'))
    ->map(get::value('email'))
    ->filter(is::notNull())
    ->toArray();      
```

## Object Mapper
Serialize and deserialize json string or array to PHP object abd back based on annotations/attributes.

## Optional
A container object which may or may not contain a non-null value. 
If a value is present, isPresent() will return true and get() will return the value.

Additional methods that depend on the presence or absence of a contained value are provided, 
such as orElse() (return a default value if value not present) 
and ifPresent() (execute a block of code if the value is present).