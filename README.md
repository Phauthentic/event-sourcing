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

* [What is Event Sourcing?](docs/Event-Sourcing.md)
* [The Architecture of this Library](docs/Architecture.md)
* [Make your Aggregates Using Event Sourcing](docs/Make-your-Aggregate-using-Event-Sourcing.md)
* [Example](docs/Example.md)
* [Other Event Sourcing Libraries](docs/Other-Event-Sourcing-Libraries.md)

## License

Copyright Florian Krämer

Licensed under the [MIT license](license.txt).
