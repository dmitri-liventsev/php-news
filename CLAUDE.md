# CLAUDE.md

Quick orientation for working on this codebase. Read this before touching the backend.

## What it is

PHP 8.2 / Symfony 7.1 news app with a React 18 SPA frontend split into two bundles (client + admin). MySQL 5.7 in Docker. Auth via JWT (`lexik/jwt-authentication-bundle`).

Runtime: containers `symfony_app` (php-fpm), `symfony_nginx`, `symfony_db`. See README.md for setup.

## Backend architecture

DDD-style with two bounded contexts and a small shared kernel:

```
src/
├── Shared/                 Shared kernel. Used by every context; depends on nothing.
│   ├── Domain/
│   │   ├── Event/                  RecordsDomainEvents interface + trait; DomainEvent interface + AbstractDomainEvent base (eventId, occurredOn).
│   │   ├── Exception/              EntityNotFoundException, DomainConflictException (abstract bases).
│   │   ├── Timestamped.php         Trait: createdAt/updatedAt + initTimestamps()/touch().
│   │   └── SoftDeletable.php       Trait: deletedAt + isDeleted()/softDelete() (idempotent; auto-touches if Timestamped is also used).
│   └── Infrastructure/
│       ├── Doctrine/               DomainEventDispatcher (onFlush/postFlush), SoftDeleteFilter.
│       ├── Exception/              EntityNotFoundListener (→404), DomainConflictListener (→409).
│       ├── Http/Request/           BaseRequest + BaseRequestValueResolver + ValidationExceptionListener (→400).
│       └── Serializer/             CircularReferenceHandler, CollectionIdNormalizer.
│
├── Identity/               Bounded context: users, authentication, password.
│   ├── Domain/                     User (pure domain — no Symfony interfaces), Email, HashedPassword, Role, UserID, UserRegistered, UserNotFoundException, EmailAlreadyTakenException, UserRepositoryInterface.
│   ├── Application/Command/        RegisterUserCommand + Handler.
│   ├── Infrastructure/
│   │   ├── Repository/             UserRepository — pure save/find, no Security mix.
│   │   └── Security/               SecurityUser adapter + UserProvider — Symfony Security lives ONLY here.
│   └── Interface/Cli/              CreateUserCommand (pre-hashes via PasswordHasherFactoryInterface, dispatches command).
│
└── News/                   Bounded context: articles, categories, comments, images.
    ├── Domain/             Pure domain. ORM attrs tolerated on entities for pragmatism.
    │   ├── Entity/                 Aggregate roots: Article, Comment (child of Article), Category, Image.
    │   ├── ValueObject/            Immutable VOs (ArticleID, ArticleTitle, CommentAuthor, …).
    │   ├── Event/                  Domain events (ArticleCreated, CommentPosted, …).
    │   ├── Exception/              Concrete subclasses of Shared\EntityNotFoundException.
    │   ├── Repository/             *RepositoryInterface (write-side: load + save aggregates).
    │   └── Service/                Stateless domain services (e.g. TopArticlesPolicy).
    │
    ├── Application/        Use cases. Knows about Domain only.
    │   ├── Command/                Command DTOs + Handler/ (auto-tagged messenger handlers).
    │   ├── Query/                  Query DTOs + Handler/ + Handler/DTO/ + Finder/.
    │   └── EventListener/          Reacts to domain events (RecomputeTopArticlesOnArticleCreated).
    │
    ├── Interface/          Adapters into the application.
    │   ├── Http/Admin|Client/Controller/  Thin controllers + Controller/Request/ DTOs.
    │   └── Cli/                           Symfony console commands.
    │
    └── Infrastructure/     Adapters out of the application.
        ├── Repository/             Doctrine ServiceEntityRepository impls.
        ├── Query/                  Doctrine impls of Finder interfaces.
        └── DataFixtures/           Doctrine fixtures.
```

Generic infrastructure (HTTP request DTO machinery, soft-delete filter, serializer helpers, exception listeners) lives in `Shared/Infrastructure/`. Anything genuinely domain-agnostic that arises in a context's `Infrastructure/` should be promoted there.

Dependency direction: **Interface → Application → Domain ← Infrastructure**. Domain never imports from the other three. Bounded contexts (News, Identity) do not import from each other; both may depend on `Shared`.

## Project-specific patterns

### Request DTOs (`BaseRequest`)

HTTP-input validation lives in dedicated request DTOs, **not** in controllers or Symfony forms.

`src/Shared/Infrastructure/Http/Request/BaseRequest.php` is the abstract base — domain-agnostic, usable from any context. Each endpoint has its own subclass under `…/Controller/Request/`, e.g. `CreateArticleRequest`. The DTO:

1. Declares typed properties (`protected string $title;` …).
2. Implements `getRules(): array` returning a map `field → Constraint[]`.
3. Exposes a `toCommand()` (or `toQuery()`) that maps validated fields into an Application-layer command.

`BaseRequestValueResolver` is a Symfony `ValueResolverInterface` (priority 200 in services.yaml) that:

- Auto-instantiates the DTO when a controller action type-hints any `BaseRequest` subclass.
- Calls `fillFromRequest($request)` — by default copies JSON body keys onto properties via reflection; override for multipart/uploads.
- Validates against `getRules()` using a `Collection` constraint.
- On failure throws `ValidationFailedException`, caught by `ValidationExceptionListener` → standard JSON error response (`{ok: false, message: "validation_failed", errors: [...]}`, HTTP 400).

Controller action then just does `$this->handle($request->toCommand())`. No try/catch, no manual validation.

### CQRS + Symfony Messenger

Single bus `messenger.bus.default`, sync, with `doctrine_transaction` middleware — every command handler runs inside a DB transaction.

Handlers are auto-tagged via per-context service-yaml blocks (`command_handlers`, `query_handlers` for News, `identity_command_handlers` for Identity). When adding a new bounded context, drop a matching block in `config/services.yaml` — one for commands, one for queries. Within an existing context, just drop the DTO + handler file in the right folder.

Controllers and CLI use `HandleTrait` + `MessageBusInterface` and call `$this->handle($message)`.

### Write vs read separation

- **Write side**: `Domain/Repository/*RepositoryInterface` — returns full aggregates (`Article`, `Comment`, …) used by command handlers. Implementations extend Doctrine `ServiceEntityRepository`.
- **Read side**: `Application/Query/Finder/*FinderInterface` — returns flat `*DTO`s (`ArticleDTO`, `CategoryPreviewDTO`, `CategoryWithTopArticlesDTO`, …). Implementations in `Infrastructure/Query/` use **plain Doctrine DBAL** — no ORM, no entity hydration. DTOs take primitives via constructor (`new ArticleDTO(id: …, title: …, …)`).

Don't return entities from query handlers. Don't import `EntityManagerInterface` or any `Domain\Entity\*` class from `Infrastructure/Query/` — that surface uses `Doctrine\DBAL\Connection` only.

Soft-deleted rows: ORM-side queries are auto-filtered by `SoftDeleteFilter`, but **DBAL finders bypass that filter** — every read query must append `deleted_at IS NULL` by hand.

Don't run write repositories inside query handlers. If a query handler needs to look up an entity for existence, prefer the matching `Finder::findOneById` (returns DTO) over `Repository::findById` (returns aggregate).

### Rich entities + Value Objects

All aggregates have:

- **Private constructor**, public static factory (e.g. `Article::create(...)`, `User::register(Email, HashedPassword)`, `Comment::post(Article, ...)`).
- Behavioural methods (`changePassword`, `markAsTop`, `addComment`, `softDelete`) — no open-ended `setX` setters.
- Getters that return VOs, not primitives: `getId(): ?ArticleID`, `getEmail(): Email`, `getTitle(): ArticleTitle`.

VOs are `final readonly class` (or `enum` for `Role`) with constructor validation. Primitives are exposed via a public `$value` property and `equals(self)` / `__toString()` methods. See `ArticleID`, `Email`, `HashedPassword` for the canonical shape.

Comments are a child entity of the `Article` aggregate — instantiation outside `Article::addComment()` is forbidden by an `@internal` doc on `Comment::post()`.

### Domain events

Aggregates implement `RecordsDomainEvents` via `RecordsDomainEventsTrait`, calling `$this->recordThat(new SomeEvent(...))` from inside their methods (often in `#[ORM\PostPersist]` lifecycle callbacks where the DB-assigned id is finally available).

Every concrete event extends `App\Shared\Domain\Event\AbstractDomainEvent`, which implements `DomainEvent` and auto-populates `eventId()` (UUID v4) and `occurredOn()` (DateTimeImmutable) in its constructor. Concrete events keep their own promoted-property payload but must call `parent::__construct()`. Use these fields for log correlation across the dispatcher boundary.

`Infrastructure/Doctrine/DomainEventDispatcher` is a Doctrine listener that:

1. `onFlush` — tracks every inserted/updated entity implementing `RecordsDomainEvents`.
2. `postFlush` — drains accumulated events via `pullEvents()` and dispatches them through Symfony's `EventDispatcher`. Re-entrancy guard handles listeners that themselves trigger a flush.

Application-layer event listeners use `#[AsEventListener(event: SomeEvent::class)]` and live in `Application/EventListener/`. They MAY call domain methods that trigger more events; the dispatcher's while-loop picks them up in the same outer flush.

### Exception → HTTP mapping

Domain throws semantic exceptions, the interface layer never `catch`-es them. Controllers and handlers throw directly — there are no manual `new JsonResponse(['error' => …], 404)` returns in the codebase.

Listeners run on `kernel.exception`. Both walk the `getPrevious()` chain, so a domain exception thrown from a Messenger handler (wrapped in `HandlerFailedException`) is mapped just the same as one thrown directly from a controller.

- `Shared/Domain/Exception/EntityNotFoundException` (abstract) → HTTP **404** via `Shared/Infrastructure/Exception/EntityNotFoundListener`. Concrete subclasses live per-context: `News\…\ArticleNotFoundException::byId(...)`, `Identity\…\UserNotFoundException::byEmail(...)`, etc. Body: `{ok: false, message}`.
- `Shared/Domain/Exception/DomainConflictException` (abstract) → HTTP **409** via `Shared/Infrastructure/Exception/DomainConflictListener`. Use for unique-key/state conflicts. Currently subclassed by `Identity\…\EmailAlreadyTakenException`. Body: `{ok: false, message}`.
- Validation failures from `BaseRequest` (thrown by `BaseRequestValueResolver`) → HTTP **400** via `ValidationExceptionListener`. Body: `{ok: false, message: "validation_failed", errors: [...]}`.

Adding a new mapping: subclass the right abstract (`EntityNotFoundException` or `DomainConflictException`) inside the context's `Domain/Exception/` and the existing listener picks it up. For an entirely new HTTP-status family, add a new abstract + listener pair to `Shared`.

### Timestamps and soft delete

Two reusable traits in `Shared/Domain/`:

- `Timestamped` — adds `createdAt` / `updatedAt` columns + `getCreatedAt()` / `getUpdatedAt()`. Call `initTimestamps()` from the static factory and `touch()` from every mutating method. The Doctrine column attributes live on the trait so each entity stays clean of timestamp boilerplate.
- `SoftDeletable` — adds `deletedAt` column + `isDeleted()` / `softDelete()` (idempotent). When the entity also `use`s `Timestamped`, `softDelete()` auto-bumps `updatedAt`.

Article, Comment, Category, Image all use both traits; User uses neither (no soft-delete or timestamps on the user record).

`Shared/Infrastructure/Doctrine/SoftDeleteFilter` (Doctrine SQL filter) automatically adds `deleted_at IS NULL` to any ORM query on an entity with the `deletedAt` field. **DBAL finders bypass this filter** and must append `deleted_at IS NULL` manually (see the Write vs read section).

### CLI commands

Same thin-adapter rule as HTTP controllers: parse input → build domain VOs → dispatch through `MessageBus` (`HandleTrait`). `Identity\Interface\Cli\CreateUserCommand` is the reference example — it uses `PasswordHasherFactoryInterface` to pre-hash the password (since `User::register` requires an already-`HashedPassword`), then dispatches `RegisterUserCommand`.

## Frontend

Two independent React apps under `frontend/src/`:

- `frontend/src/client/` → public site, mounted on `/`, `/category/:id`, `/article/:id`.
- `frontend/src/admin/` → admin SPA, mounted on `/admin/*`.

Both share `webpack.config.js` (Symfony Encore) which emits to `public/build/{client,admin}/`. Twig stubs in `templates/{client,admin}/index.html.twig` just include the bundles; everything else is React.

State / API: Redux Toolkit Query (`features/api/apiSlice.ts`). Admin slice attaches JWT from `localStorage`. Both slices redirect to `/404` (or `/admin` on 401) inside `baseQuery` — keep that in mind when debugging "why did my page just navigate away".

i18n: `i18next` with HTTP backend pulling `/locales/{lng}/translation.json`.

Cross-app linking: the admin SPA cannot route into client URLs via `<Link>` (its router has no `/article/:id` route). Use a plain `<a href="...">` to force a full-page navigation. See `ArticleRow.tsx` — the "eye" icon.

## Testing

PHPUnit 9, tests under `tests/News/...` mirror `src/News/...`.

- **Unit tests** for `Domain/Entity`, `Domain/ValueObject`, `Domain/Event`, `Domain/Service` — no kernel.
- **Integration tests** under `tests/News/Application/Command/Handler/` use `KernelTestCase`/`WebTestCase` against the real `symfony_test` MySQL database (see README "Running Tests").
- Shared fixtures in `tests/Helpers/` (`ArticleHelper`, `CategoryHelper`, `CommentHelper`).

Tests hit a real DB. When wiping state from a test that bulk-deletes articles, you have to delete dependent rows first (`comment`, `article_category`) — bulk DQL `DELETE FROM Article` doesn't cascade through ORM associations. See `CreateArticleHandlerTest::testHandleUpdatesTopArticlesCorrectly` for the pattern.

Doctrine clears `id` on entities deleted by `orphanRemoval`/`remove` during `flush`. If a test needs the deleted entity's id after the deletion, capture `$entity->getId()` into a local var **before** dispatching the delete.

## Auth (security.yaml)

- `^/api` — `PUBLIC_ACCESS` (public client API).
- `^/admin/api/login` — `PUBLIC_ACCESS`, `json_login` firewall, on success returns JWT.
- `^/admin/api/...` — JWT-only firewall.
- Anything else is served by nginx → React.

The domain `User` has **no** Symfony Security coupling. Adaptation happens in `Identity/Infrastructure/Security/`:

- `SecurityUser` implements `UserInterface` + `PasswordAuthenticatedUserInterface`; it's a flat snapshot (email + hashed password + roles) built via `SecurityUser::fromDomain(User)`. It is the class registered with `password_hashers` and the one Symfony Security sees end-to-end.
- `UserProvider` implements `UserProviderInterface` + `PasswordUpgraderInterface`. It loads the domain `User` via `UserRepositoryInterface::findByEmail`, wraps it in a `SecurityUser`, and on password rehash mutates the domain user via `User::changePassword(HashedPassword)`.

`security.yaml` uses the service provider (`id: App\Identity\Infrastructure\Security\UserProvider`), not the `entity:` shortcut.

CLI `app:create-user` looks up the hasher with `SecurityUser::class` (not the domain `User`) because that's the class that implements `PasswordAuthenticatedUserInterface`.

JWT keypair must exist in `config/jwt/` — generate with `lexik:jwt:generate-keypair`. Empty token in login response usually means missing keys.

## Conventions / gotchas

- Don't add `setX` setters back onto entities. Add a behavioural method instead.
- Don't make controllers `catch` domain exceptions. Add/extend an exception listener if a new mapping is needed.
- Don't return Doctrine entities from query handlers. Return DTOs.
- Don't put validation rules anywhere except a `BaseRequest` subclass (HTTP) or a VO constructor (domain invariants).
- The `assets/` directory has been removed — there is no Symfony AssetMapper / Stimulus in this project. All frontend lives in `frontend/`.
- Service name in `docker-compose.yml` is `app`, not `php` (older README versions had `docker-compose exec php`, which doesn't work).
