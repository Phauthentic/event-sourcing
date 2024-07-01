# Make your Aggregates Using Event Sourcing

## Prerequisites

### For recording the state of an Aggregate

Persistence is done via so-called aggregate extractors. Extractors get the information required to enabled event sourcing from your aggregate. The library provides different extractors that you can use. An aggregate **must** provide its ID, the aggregate version (an incrementing integer) and a list of domain events. Optionally you can provide the aggregate type.

From the perspective of the library it doesn't matter much how you implement the recording of the events. But the events must be somehow recorded, ideally in a property of the aggregate. The library provides different ways of extracting the information from the aggregate.

### For restoring the State of an Aggregate

An aggregate **must** provide a way to restore its state from a list of events.

The library provides an abstract class [AbstractEventSourcedAggregate](../src/Aggregate/AbstractEventSourcedAggregate.php) that you can extend to implement the event sourcing in your aggregate.

The class has a method `applyEventsFromHistory()` that takes the events and will reconstitute the state of the aggregate from them by calling a different methods per event type. By default it is prefixed with `when` and followed by the event type. For example `UserCreated` becomes `whenUserCreated`. It will throw an exception if your aggregate is missing such a method and tell you which one is missing.

## Using Reflections only - the most pure flavor

As mentioned in the prerequisites, there are only three things required: Aggregate ID, version and domain events. You don't have to modify your aggregate beyond that if you want to use the reflection based extractor.

You just have to configure the extractor to read the right properties from your aggregate. The default values are shown below. If you name your properties like that you don't have to configure the extractor.

 - aggregateEvents
 - aggregateId
 - aggregateVersion

```php
class MyAggregate 
{
    private string $id;
    private int $aggregateVersion = 0;
    private array $domainEvents = [];
    private string $aggregateType = 'my-aggregate';
    
    /* ... */
}
```

## Using the EventSourcedAggregate Attribute

By attribute, we talk about PHPs attributes. If you are not familiar with them, they are a way to add metadata to classes, methods, and properties. They are an official way to add annotations to your code. Check [the official documentation](https://www.php.net/manual/en/language.attributes.overview.php) for attributes.

The `EventSourcedAggregate` attribute is used to mark a class as an event sourced aggregate. Add it to any class that you want to use as an event sourced aggregate.

You can specify the properties, which are usually private, in the attribute. The event sourcing library will use reflections to extract the data from those properties. The passed values shown below are the default values. You can omit them if you want to use the default values.

Your aggregate must have at least three properties. How you name them is up to you, you can configure the names of them. The properties are:

- `identifierProperty` - The identifier of the aggregate.
- `aggregateVersion` - The version of the aggregate.
- `domainEvents` - The list of domain events that have been applied to the aggregate.
- `aggregateType` - **(OPTIONAL)** The type of the aggregate. If not provided, the class name will be used.

Note that you **MUST** use the [AttributeExtractor](../src/Repository/AggregateExtractor/AttributeBasedExtractor.php) to extract the data from the aggregate.

```php
use Phauthentic\EventSourcing\Aggregate\Attribute\EventSourcedAggregate;

#[EventSourcedAggregate(
    versionProperty: 'aggregateVersion',
    identifierProperty: 'id',
    domainEventProperty: 'domainEvents',
    aggregateType: 'aggregateType'
)]
class MyAggregate 
{
    private string $id;
    private int $aggregateVersion = 0;
    private array $domainEvents = [];
    private string $aggregateType = 'my-aggregate';
    
    /* ... */
}
```

## Interface Based

Implement the interfaces `EventSourcedAggregateInterface` and `TypeProvidingAggregateInterface` to make your aggregate event sourced.

```php
use Phauthentic\EventSourcing\Aggregate\EventSourcedAggregateInterface;
use Phauthentic\EventSourcing\Aggregate\TypeProvidingAggregateInterface;

class MyAggregate implements EventSourcedAggregateInterface, TypeProvidingAggregateInterface
{
    /*...*/
}
```

## Restoring your Aggregate

The state of an aggregate is restored via an aggregate factory that encapsulates the logic to restore the state of an aggregate from a list of events.

The library comes with a `ReflectionFactory` that you can use to restore your aggregates.

You can also implement your own factory if you want by implementing the [AggregateFactoryInterface](../src/Repository/AggregateFactory/AggregateFactoryInterface.php).
