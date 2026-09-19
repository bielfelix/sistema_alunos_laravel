# Security Policy

## Supported branch

Security fixes are applied to the current `main` branch.

This repository is a portfolio-grade reference implementation, not a hosted production service. Even so, security-relevant issues are treated as engineering defects and should be handled through a controlled disclosure path.

## Reporting a vulnerability

Do not open a public issue for a vulnerability that could expose credentials, authentication weaknesses, sensitive data, injection paths, authorization bypasses or integrity failures.

Instead, contact the maintainer privately through the professional contact information published on the GitHub profile.

Please include:

- affected component or endpoint;
- reproduction steps;
- expected versus observed behavior;
- impact assessment;
- proof of concept when safe to share;
- suggested remediation, if available.

## Security boundaries in this project

The current implementation is intentionally small, but it still applies explicit controls around:

- database integrity through foreign keys and unique constraints;
- transactional enrollment operations;
- row locking around course-capacity decisions;
- request validation;
- bounded pagination;
- environment-based configuration;
- dependency monitoring with Dependabot;
- CI validation before merge.

## Out of scope

The current project does not claim to provide:

- production authentication or authorization;
- tenant isolation;
- secrets management;
- WAF or edge protection;
- distributed rate limiting;
- production observability or incident response.

Those controls would be required before deploying the application as a public production service.

## Dependency security

Dependencies are tracked through Composer and GitHub Dependabot. Security advisories should be reviewed before routine version upgrades are merged.
