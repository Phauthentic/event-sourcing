# Phauthentic Event Sourcing Library

A framework-agnostic event sourcing system.

The library tries to be as unopinionated as possible, it provides the basic building blocks to implement event sourcing in your application. The library will allow you to keep 3rd party dependencies to a minimum and will not force you to use a specific framework or library. Your aggregates can be free of third party dependencies if you want to go for that style. 

It features different ways of extracting information from your aggregates, pick your flavor: Via Attributes (recommended), Interfaces or Reflection. Using a reflection based extractor will allow you to keep your aggregates free of any dependency to this library.

## Installation

```sh
composer require phauthentic/event-sourcing
```

## Documentation

* [What is Event Sourcing?](docs/What-is-Event-Sourcing)
* [The Architecture of this Library](docs/Architecture.md)
* [Make your Aggregates Using Event Sourcing](docs/Make-your-Aggregate-using-Event-Sourcing.md)
* [Example](docs/Example.md)
* [Running Tests](docs/Running-Tests.md)
* [Other Event Sourcing Libraries](docs/Other-Event-Sourcing-Libraries.md)

## License

Copyright Florian Krämer

Licensed under the [MIT license](LICENSE).
