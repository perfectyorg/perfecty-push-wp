# CLAUDE Configuration for Perfecty Push WP Plugin

This is a WordPress plugin for self-hosted push notifications. This file contains project-specific instructions for Claude Code.

## Project Overview
- **Type**: WordPress Plugin
- **Language**: PHP
- **Framework**: WordPress Plugin API
- **License**: GPL v2

## Development Workflow

### After Code Changes
Always run tests after making code changes:
```bash
make test
```

### Code Formatting
Use the project's formatter:
```bash
make format
```

### Local Development
Start the development environment:
```bash
make up
make setup
```

Access WordPress at https://localhost/wp-login.php

## WordPress Standards
- Follow WordPress Coding Standards (WPCS)
- Use WordPress hooks, filters, and APIs appropriately
- Maintain compatibility with WordPress multisite
- Consider WordPress security best practices

## Key Files & Structure
- `perfecty-push.php` - Main plugin file
- `admin/` - Admin interface files
- `lib/` - Core library classes
- `public/` - Public-facing functionality
- `tests/` - PHPUnit tests

## Dependencies
- PHP >= 7.2
- Composer dependencies (minishlink/web-push, ramsey/uuid)
- WordPress compatible versions

## Testing
- Run full test suite: `make test`
- Tests are based on WordPress Core testing guidelines
- Use PHPUnit for unit testing

## Build & Distribution
- Create distributable zip: `make bundle`
- Bundle includes production dependencies only

## Version Release Process
When asked to "generate a new version" or create a release, update these files:

1. **perfecty-push.php**:
   - Plugin header `Version:` field
   - `PERFECTY_PUSH_VERSION` constant

2. **README.txt**:
   - `Stable tag:` field 
   - Add changelog entry under `== Changelog ==` section

Always run `make test` after version updates to verify changes work correctly.