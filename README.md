# congraph/filesystem

`congraph/filesystem` stores file metadata and provides file-serving and image-version behavior for Congraph content.

## Responsibilities

- file CRUD commands and validators
- file metadata persistence
- file serving
- image version handlers
- generated admin image variants

## Installation

```bash
composer require congraph/filesystem
```

Laravel package provider:

- [FilesystemServiceProvider.php](/Users/nikolap/git/np/congraph/packages/filesystem/FilesystemServiceProvider.php)

## Image handling

This package now uses maintained Intervention Image v3:

- `intervention/image`
- `intervention/image-laravel`

Current image handlers use `coverDown()` instead of the old `fit()` semantics to avoid upscaling smaller originals.

## Typical usage

Use this package to:

- create file records for uploaded content
- fetch metadata for assets
- serve originals and derived versions
- attach files to EAV entities

## Development

### Install dependencies

```bash
cd /Users/nikolap/git/np/congraph/packages/filesystem
composer update -W
```

### Run tests

```bash
composer test
composer test:pgsql
```

The package includes:

- command tests
- validator tests
- image handler tests
- integration tests for serving and file behavior

## Contribution notes

- verify behavior on both SQLite and PostgreSQL-backed suites
- test image behavior explicitly when changing handler logic
- keep file-serving routes and signed URLs compatible with `laravel-api`
