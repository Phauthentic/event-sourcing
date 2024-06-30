## What is Event Sourcing?

Event sourcing is a software architecture pattern that emphasizes capturing and persisting the state of an application as a sequence of events rather than storing the current state directly. In event sourcing, every state-changing operation, or event, is stored in an append-only log. The current state of an entity is reconstructed by replaying these events in sequence.

This approach provides a comprehensive audit trail of all changes, enabling traceability, versioning, and the ability to rebuild the system's state at any point in time. Event sourcing promotes a decentralized and scalable model, facilitating event-driven architectures and supporting the evolution of domain models over time, making it particularly suitable for complex business domains and systems where temporal aspects and historical data are crucial.

## When to NOT use it

Event sourcing comes with additional complexity. You should NOT use event sourcing when you don't need it. It is a powerful tool, but it is not a silver bullet. It is not a one-size-fits-all solution. Event sourcing is a good solution for scenarious like audit logging, undo/redo functionality, and complex business rules.

If you have no good reason to use it, then don't.
