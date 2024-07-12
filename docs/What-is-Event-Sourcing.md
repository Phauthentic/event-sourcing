## What is Event Sourcing?

Event sourcing is a software architecture pattern that emphasizes capturing and persisting the state of an application as a sequence of events rather than storing the current state directly. In event sourcing, every state-changing operation, or event, is stored in an append-only log. The current state of an entity is reconstructed by replaying these events in sequence.

This approach provides a comprehensive audit trail of all changes, enabling traceability, versioning, and the ability to rebuild the system's state at any point in time. Event sourcing promotes a decentralized and scalable model, facilitating event-driven architectures and supporting the evolution of domain models over time, making it particularly suitable for complex business domains and systems where temporal aspects and historical data are crucial.

## When to NOT use it

Event sourcing comes with additional complexity. You should **not** use event sourcing when you don't need it. It is a powerful tool, but it is not a silver bullet. It is not a one-size-fits-all solution. Event sourcing is a good solution for scenarious like audit logging, undo/redo functionality, and complex business rules.

| Quality Attribute         | Use Event Sourcing                                 | Don't Use Event Sourcing                             |
|---------------------------|----------------------------------------------------|------------------------------------------------------|
| Audit Trail               | Complete history required                          | Basic logging sufficient                             |
| Temporal Queries          | Frequent historical state reconstruction           | Only current state needed                            |
| Domain Complexity         | Complex domain with many state transitions         | Simple CRUD operations                               |
| Scalability               | High write scalability needed                      | Moderate scalability sufficient                      |
| Consistency               | Eventual consistency acceptable                    | Strict immediate consistency required                |
| Performance               | Read-heavy systems with async processing           | Write-heavy systems with sync requirements           |
| Data Evolution            | Frequent schema changes expected                   | Stable data model                                    |
| Concurrency               | High contention on aggregate roots                 | Low concurrency needs                                |
| Debugging/Troubleshooting | Detailed system behavior analysis needed           | Simple error logging sufficient                      |
| Undo/Redo Functionality   | Complex undo/redo operations required              | No or simple undo/redo needs                         |
| Regulatory Compliance     | Strict data lineage and audit requirements         | Basic compliance needs                               |
| System Complexity         | Willing to embrace increased initial complexity    | Keeping system simple is a priority                  |
| Team Expertise            | Team familiar with DDD and CQRS concepts           | Team more comfortable with traditional architectures |
| Integration Patterns      | Event-driven architecture planned                  | Request-response patterns preferred                  |
| Storage Requirements      | Disk space abundant                                | Storage constraints are a concern                    |
| Reporting                 | Complex, flexible reporting needs                  | Standard, simple reporting suffices                  |
| Business Intelligence     | Deep insights from historical data needed          | Basic analytics on current state sufficient          |
| Recovery Scenarios        | Advanced recovery and replay capabilities required | Simple backup and restore sufficient                 |

**If you have no good reasons to use it, then don't!**
