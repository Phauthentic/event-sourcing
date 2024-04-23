# Make your Aggregates Using Event Sourcing

## Using the EventSourcedAggregate Attribute

The `EventSourcedAggregate` attribute is used to mark a class as an event sourced aggregate. Add it to any class that you want to use as an event sourced aggregate.

You can specify the properties, which are usually private, in the attribute. The event sourcing library will use reflections to extract the data from those properties. The passed values shown below are the default values. You can omit them if you want to use the default values.

Your aggregate must have at least three properties. How you name them is up to you, you can configure the names of them. The properties are:

- `id` - The identifier of the aggregate.
- `aggregateVersion` - The version of the aggregate.
- `domainEvents` - The list of domain events that have been applied to the aggregate.
- `aggregateType` - (OPTIONAL) The type of the aggregate. If not provided, the class name will be used.

```php
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
