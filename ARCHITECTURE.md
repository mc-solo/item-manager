# Item Manager — Architecture

Device repair tracking for The Phone Boss (Miki and Mesi). Orders are
`TPB-xxxxx`. This document is the map of the system: what runs where, how a
request flows, and where the API lives.

## System overview

| Piece | Tech | Notes |
| --- | --- | --- |
| Web app | Laravel 12 + Livewire (Flux 2) | The shop UI, session auth, roles, receipt printing, CSV export |
| API | Laravel 12, Sanctum token auth, **v1** under `/api/v1` | Backend for the Flutter mobile app, same rules as the web |
| Mobile app | Flutter (planned) | Mobile front end, talks to the API only, no own backend |
| Database | MySQL / MariaDB | Same database for web and API |
| Deploy | Manual cPanel deploy | Merging to `main` does **not** deploy; see Deploy below |

One backend, one database, two front ends. The API does not duplicate shop
rules: it reuses `DevicePolicy`, the web form requests
(`StoreDeviceRequest` / `UpdateDeviceRequest`) and the `Device` query
scopes. A rule changed in the policy changes the web and the API together.

## Request flow

### Web (session)

```
Browser -> routes/web.php, routes/device.php, routes/admin.php
  -> auth middleware (session) -> Livewire / DeviceController / DashboardController
  -> DevicePolicy (Gate) -> Device scopes (forUser, search, status, ...)
  -> MySQL
```

Login is a plain POST form (`AuthenticatedSessionController`), username or
email plus password, 5 attempts per login + IP, session regenerate.

### API (token)

```
Flutter / curl -> routes/api.php (prefix v1)
  -> auth:sanctum (Bearer token, no session)
  -> App\Http\Controllers\Api\V1\* -> same DevicePolicy / form requests / scopes
  -> MySQL, JSON out
```

Login (`POST /api/v1/login`) takes the same `login` + `password` as the web,
picks the field by whether `login` contains `@`, applies the same
5-attempt throttle, then returns a Sanctum token. The credential check uses
`Hash::check` instead of `Auth::attempt` on purpose: API routes are
stateless (no session middleware), so a session login would fail.

## Roles

Roles are a plain string column on `users`: `admin`,
`hardware_technician`, `software_technician`, `reception`.

| Action | Admin | Hardware / software technician | Reception |
| --- | --- | --- | --- |
| See orders | All | Only assigned to them | All |
| Create an order | Yes | Yes | Yes |
| Update / change status / complete | Yes | Only assigned to them | No (403) |
| Assign an order | Yes | No (403) | Yes |
| Delete an order | Yes | No (403) | No (403) |
| Manage users (`/admin/users`) | Yes | No (403) | No (403) |

Enforcement is server side: `DevicePolicy` (`view`, `update`,
`complete`, `delete`, `create`), `Device::scopeForUser` on every list,
and explicit admin-or-reception checks where the policy has no rule
(assign). The API has no user-management endpoints; users are managed in
the web app at `/admin/users` (admin only).

## Models

| Model | Key fields | Notes |
| --- | --- | --- |
| `User` | `name`, `username`, `email`, `password`, `role` | `HasApiTokens` (Sanctum). `isAdmin()`, `isTechnician()`, `isReception()`. `assignedDevices()` |
| `Device` | `order_number`, `client_name`, `client_number`, `device_type`, `device_model`, `serial_number`, `imei`, `issue_description`, `repair_status`, `amount`, `assigned_to` | `STATUSES`: pending, in_progress, completed, cancelled, delivered. `DEVICE_TYPES`: mobile, laptop, tablet, pc, mac, other. Scopes: `forUser`, `search`, `status`, `deviceType`, `dateRange`, `amountRange`. `client_number` must start `09` or `07` and be 10 digits |
| `Transaction` | `device_id`, `type`, `amount` | Payments recorded against a device (`type`: deposit, cash, ...) |
| `Payment` | `device_id`, `amount`, `payment_method`, `status` | Payment record for a device |
| `DeviceProgressLogs` | `device_id`, `progress_description`, `status` | Progress history for a device |

`Device` relations: `assignedTechnician()` (belongs to `User`),
`progressLogs()` (has many), `transaction()` (has many).

## API versioning

- The version is part of the path: `/api/v1/...`. There are no unversioned
  `/api/*` routes.
- Breaking changes go to a new version (`/api/v2`). `v1` stays stable;
  only backwards-compatible additions are made to `v1`.
- Code is versioned the same way: controllers in
  `App\Http\Controllers\Api\V1`, resources in
  `App\Http\Resources\Api\V1`. A future `V2` sits next to `V1`.
- Docs: [`docs/API.md`](docs/API.md) (human guide),
  [`docs/API_V1_OPENAPI.yaml`](docs/API_V1_OPENAPI.yaml) (OpenAPI 3.0),
  [`docs/API_V1_POSTMAN.json`](docs/API_V1_POSTMAN.json) (Postman).

## Directory map

```
app/
  Http/
    Controllers/
      Device/DeviceController.php      # web device actions
      Dashboard/DashboardController.php # web dashboard
      Admin/UserController.php          # web user management
      Auth/AuthenticatedSessionController.php  # web login
      Api/V1/
        AuthController.php              # POST /api/v1/login, logout, user
        DeviceController.php            # devices CRUD, status, assign, complete
        DashboardController.php         # GET /api/v1/dashboard
    Requests/
      StoreDeviceRequest.php            # shared by web + API create
      UpdateDeviceRequest.php           # shared by web + API update
      BulkDeviceRequest.php             # web bulk actions only
    Resources/Api/V1/
      DeviceResource.php                # device JSON shape
  Models/  Device.php  User.php  Transaction.php  Payment.php  DeviceProgressLogs.php
  Policies/  DevicePolicy.php  UserPolicy.php
routes/
  web.php  auth.php  device.php  admin.php   # web, session auth
  api.php                                     # API v1, auth:sanctum (except login)
docs/
  API.md  API_V1_OPENAPI.yaml  API_V1_POSTMAN.json
tests/Feature/Api/ApiTest.php                 # 16 API tests
```

## Testing

```bash
php artisan test --filter=ApiTest   # 16 API tests, SQLite in-memory
php artisan test                    # full suite
```

API tests use `Sanctum::actingAs` and cover login (username, email, wrong
password), unauthenticated access, per-role visibility, create, assign,
status change, complete, delete, validation (including the 09/07 client
number rule) and the dashboard. Note: the full suite has 6 pre-existing
failures on `main` that are not from the API (admin user management,
dashboard print text, the `/` example test returning 302); they are
unrelated to the API work.

## Deploy

- Merging to `main` does **not** deploy. Deploy is a manual cPanel
  deploy (`./deploy.sh main` on the server, then check the live site).
- No migration is needed for the API: the Sanctum
  `personal_access_tokens` migration already exists in the repo
  (`database/migrations/2025_07_27_120856_create_personal_access_tokens_table.php`).
- The API is served from the same Laravel app as the web, at
  `https://item-manager.thephoneboss.net/api/v1`.
- After deploy, smoke-test the live API: log in, list devices, create and
  complete one order (curl examples are in `docs/API.md` and the Postman
  collection).
