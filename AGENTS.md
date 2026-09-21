# AGENTS.md — HWinfo

## Project
HWinfo is a Persian-oriented web application for organizational hardware inventory, monitoring, assignment and audit.

## Source of truth
- The actual collector is `powershell/hardware-info.ps1`.
- The actual CSV contract is documented in `docs/csv-format.md` and must match the supplied collector output.
- Do not invent hardware fields. If a requirement is unclear, mark it as an Open Question or Assumption.

## Stack
PHP 8.x, MySQL 8.x, PDO, HTML5, CSS3, Vanilla JavaScript.

## Architecture
Presentation → Application → Data Access → Storage.

## Security
CSRF protection, prepared statements, output escaping, bcrypt password hashing, session regeneration, upload validation, RBAC and audit logging are mandatory.

## Versioning
Use QAVNS. The policy is in `VERSIONING.md`. Published versions are immutable.

## Documentation tags
Use `@tag:<slug>` for cross-cutting concepts such as `@tag:csv-import`, `@tag:audit`, and `@tag:rbac`.
