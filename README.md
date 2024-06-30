# Phauthentic Event Sourcing Library

**⚠ Do not use it in production! This is still in development! ⚠**

A framework-agnostic event sourcing system.

Consider this library a framework to create your own flavor of event sourcing within your application. The library tries to provide different styles and flavors that you can freely combine to implement event sourcing in your application.

We consider the repository the main element in the system that connects the extraction of events from the actual aggregate the persistence of the aggregate taking snapshots and emitting events.

It features different ways of extracting information from your aggregates, pick your flavor:

* Via Attributes
* Via Interfaces
* Via Reflection

## Installation

```sh
composer require phauthentic/event-sourcing
```

## Documentation

Please start by reading [the docs folder](/docs/Index.md) in this repository.

## Other Event Sourcing Libraries

If you don't like this library, you might get happy with one of those. We think that different libraries for the same purpose approach the same problem from different angles and probably for different skill levels and audiences. Therefor we are happy to provide you with a list of alternatives.

It would be nice if you could tell us why you preferred another library, thank you!

### [Event Sauce](https://eventsauce.io/)     

A wide used an well know library.

### [Prooph Event Sourcing](https://github.com/prooph/event-sourcing) 

A very well engineered library, but it seems to be unmaintained. Some might even call it over-engineered, however it is a very good library.

### [Patchlevel Event Sourcing](https://github.com/patchlevel/event-sourcing)

A library that is very tightly coupled with Symfony and Doctrine. It has a lot of dependencies and is not as flexible as the other libraries.

## License

Copyright Florian Krämer

Licensed under the [MIT license](license.txt).
