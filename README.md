# Rabbyte Kernel

Rabbyte Kernel is the core of the Business Context Platform. It is a Laravel 12 application that provides governance, package lifecycle, events, and integration surfaces. It intentionally ships without product/domain features (invoicing, reservations, etc.). Those arrive as packages.

## Table of contents
- [What the kernel provides](#what-the-kernel-provides)
- [Motivation](#motivation)
- [Architecture overview](#architecture-overview)
- [Technology choices](#technology-choices)
- [Packages and manifests](#packages-and-manifests)
- [Creating a package](#creating-a-package)
- [Event model](#event-model)
- [MCP and webhooks](#mcp-and-webhooks)
- [Requirements](#requirements)
- [Local development (Sail)](#local-development-sail)

## What the kernel provides
- Package registry and enable/disable lifecycle.
- Roles, permissions, and API keys.
- Event bus with versioned event names and an envelope.
- MCP registry for tools/resources/prompts.
- Webhook subscriptions and delivery tracking.
- Filament admin UI for operations.

## Motivation
Modern businesses run on many disconnected tools. Each one owns a fragment of reality, which leads to duplicated data, missing context, and brittle automation. This project exists to provide a shared source of business context and governance so automation and AI can act safely and predictably. The goal is not to ship an all-in-one suite, but to build a stable kernel that products can plug into, enabling consistent decisions, clear permissions, and auditable outcomes across the system.

## Architecture overview
The kernel is a modular monolith focused on wiring and governance:
- **Kernel**: always present, minimal, stable.
- **Packages**: installable modules that bring domain logic, UI, routes, and event listeners.
- **Composition**: packages do not call each other directly; they communicate via events and shared primitives.
- **Domain actions**: all state changes happen in actions with centralized authorization.

## Technology choices
### Why PHP and Laravel
We chose PHP and Laravel for their maturity, ecosystem depth, and rapid iteration speed for domain-heavy systems. PHP remains one of the most common languages for web, is simple to learn, and is inexpensive to run at scale. Laravel gives us a stable foundation (auth, queues, migrations, policies) and strong conventions that keep the kernel minimal while enabling packages to scale.

### Why Filament
Filament is used for rapid development with a good-enough UI. It can be the actual UI for internal systems, but it is not the customer-facing product UI by default. Operators use Filament to manage packages, permissions, MCP entries, webhooks, and to inspect state. Business logic should not live in Filament; it should live in domain actions so the same capabilities can be exposed to API, MCP, and Filament without duplication.

### MCP, webhooks, and APIs
- **MCP** provides a structured interface for AI assistants to safely access tools, resources, and prompts with permission checks.
- **Webhooks** are the outward event stream for integrations that need real-time delivery and retries.
- **APIs** expose the kernel for systems and custom UIs; they map to the same domain actions and authorization rules.

### Why Redis
Redis is used for queues and event distribution. It is fast, reliable, and widely supported across languages, which is essential for external workers that process jobs and consume event envelopes.

### External workers (language-agnostic)
Some tasks are better handled outside the PHP runtime. We use separate workers that communicate only through Redis streams/queues. This lets us run specialized tooling in the best language for the job (e.g. a Python worker using Mistral OCR) without forcing unsupported libraries into PHP. Workers are easy to replace or scale independently, and the kernel remains focused on governance and orchestration rather than heavy processing.

## Packages and manifests
Local packages live in `packages/` and expose a `manifest.php`. Vendor packages can be discovered when their `composer.json` defines a manifest path:

```json
{
  "extra": {
    "kernel": {
      "manifest": "manifest.php"
    }
  }
}
```

Manifest responsibilities (typical):
- Package metadata (name/version).
- Permissions to register.
- Events provided by the package.
- Listener map and event bindings (string-keyed; no cross-package class references).
- MCP entries (tools/resources/prompts).
- Filament resources and API routes (if any).

Package lifecycle commands:

```bash
vendor/bin/sail artisan packages:sync
vendor/bin/sail artisan packages:enable invoice
vendor/bin/sail artisan packages:disable invoice
```

## Creating a package
Packages can be local (in-repo) or vendor (Composer-installed). Both must ship a `manifest.php`.

### Option A: Local package
1. Create a folder under `packages/your-package`.
2. Add a `composer.json` with PSR-4 autoloading.
3. Add `manifest.php` at the package root.
4. Wire the package as a path repository in the kernel `composer.json`.
5. Run `vendor/bin/sail composer dump-autoload` and `vendor/bin/sail artisan packages:sync`.

Example structure:

```
packages/invoice/
  composer.json
  manifest.php
  src/
  routes/
  resources/
```

Example `composer.json` (local):

```json
{
  "name": "rabbyte/invoice",
  "type": "library",
  "autoload": {
    "psr-4": {
      "RabbyteTech\\Invoice\\": "src/"
    }
  }
}
```

### Option B: Vendor package (Composer)
1. Publish the package to Packagist or install via VCS.
2. Add `extra.kernel.manifest` to point to the manifest path.
3. Run `vendor/bin/sail composer require vendor/package`.
4. Run `vendor/bin/sail artisan packages:sync`.

Example `composer.json` (vendor):

```json
{
  "name": "rabbyte/invoice",
  "type": "library",
  "autoload": {
    "psr-4": {
      "RabbyteTech\\Invoice\\": "src/"
    }
  },
  "extra": {
    "kernel": {
      "manifest": "manifest.php"
    }
  }
}
```

### Minimal `manifest.php`
```php
<?php

return [
    'package' => [
        'name' => 'invoice',
        'version' => '0.1.0',
    ],
    'permissions' => [
        'invoice.viewAny',
        'invoice.create',
    ],
    'events' => [
        'invoice.created.v1',
    ],
    'listeners' => [
        'map' => [
            'invoice.log' => RabbyteTech\\Invoice\\Listeners\\LogInvoiceEvent::class,
        ],
        'bindings' => [
            'invoice.created.v1' => [
                'invoice.log',
            ],
        ],
    ],
];
```

### Manifest field reference
- `package.name` (string): unique package identifier used by the kernel.
- `package.version` (string): semantic version for the package.
- `permissions` (array<string>): permission names to register.
- `events` (array<string>): event names exposed by the package (version suffix required).
- `listeners.map` (map<string,string>): string key to listener class mapping.
- `listeners.bindings` (map<string,array<string>>): event name to listener keys.
- `mcp.tools|resources|prompts` (array): MCP entries with `class`, `name`, `permission`.
- `filament.resources` (array<class-string>): Filament resources to register.
- `routes.api` (string): path to the package API routes file.

### Service providers
Laravel packages can register a service provider. This is recommended for binding contracts, publishing config, or wiring package services. Add the provider to `composer.json`:

```json
{
  "extra": {
    "laravel": {
      "providers": [
        "RabbyteTech\\Invoice\\InvoiceServiceProvider"
      ]
    }
  }
}
```

## Event model
Events use dot-case names with a version suffix, for example `invoice.created.v1`. Dispatching produces an envelope with:
- `event`, `id`, `occurred_at`
- `actor` (type/id/name)
- `source` (package/version)
- `data` (payload)
- `meta` (correlation/request ids + schema version)

This keeps events versioned, auditable, and safe for external integrations.

## MCP and webhooks
- MCP entries are registered from package manifests and stored in `mcp_entries`.
- Webhook subscriptions are stored in `webhook_subscriptions`.
- Deliveries are tracked in `webhook_deliveries` with retries and status.

## Requirements
- PHP 8.5 (provided by Sail runtime).
- Node.js 24 (provided by Sail runtime).
- `pnpm` for frontend tooling (installed in Sail; preferred over `npm`).
- Docker + Docker Compose for Sail.

## Local development (Sail)
Start the stack:

```bash
vendor/bin/sail up -d
```

Install dependencies and bootstrap:

```bash
vendor/bin/sail composer install
vendor/bin/sail artisan migrate
vendor/bin/sail pnpm install
vendor/bin/sail pnpm run dev
```

Run tests:

```bash
vendor/bin/sail artisan test
```
