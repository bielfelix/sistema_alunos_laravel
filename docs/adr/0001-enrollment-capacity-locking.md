# ADR 0001: Serialize enrollment capacity checks with a row lock

Status: Accepted

Date: 2026-09-19

## Context

A course may define a finite enrollment capacity.

Checking the current number of active enrollments and then inserting a new enrollment in separate, unlocked operations creates a race condition:

1. two requests read the same remaining seat;
2. both conclude that capacity is available;
3. both insert a new enrollment;
4. the configured capacity is exceeded.

Application-level validation alone is not enough to prevent this under concurrency.

## Decision

Enrollment creation runs inside a database transaction.

Before counting active enrollments, the application acquires a `FOR UPDATE` lock on the target course row.

While that transaction holds the lock, another transaction attempting to enroll a student in the same course must wait before evaluating capacity.

The database also keeps a unique constraint for the student/course pair.

These two controls solve different integrity problems:

- the course row lock protects capacity checks under concurrency;
- the unique constraint prevents duplicate enrollment records for the same student and course.

## Consequences

### Positive

- course capacity remains consistent under concurrent enrollment requests;
- the integrity rule does not depend only on HTTP-level validation;
- duplicate student/course enrollment remains independently protected by the database;
- the implementation stays simple enough for the current domain.

### Trade-offs

- enrollment requests for the same course are serialized while the transaction is active;
- long-running work must not be added inside the locked transaction;
- this approach is appropriate for the current workload but would need reassessment if enrollment throughput became very high or the domain were distributed across multiple services.

## Alternatives considered

### Count without locking

Rejected because concurrent requests can observe the same remaining capacity.

### Application mutex

Rejected because it would introduce an additional coordination dependency while PostgreSQL already provides the required transaction semantics.

### Optimistic retry around a separate capacity counter

Not selected for the current implementation because it adds state and retry complexity without a demonstrated throughput requirement.

## Verification

The implementation is located in:

`app/Services/EnrollmentService.php`

Feature tests cover:

- capacity rejection;
- duplicate enrollment rejection;
- cancellation;
- capacity becoming available after cancellation.
