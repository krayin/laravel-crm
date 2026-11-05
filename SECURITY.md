# Security Policy

## Supported Versions

Krayin CRM follows Laravel's release cadence. The 2.2 line, built on
Laravel 12, receives bug and security fixes. The 2.1 line receives
security fixes only. Earlier lines are End of Life, and users should
upgrade to the latest 2.2 release.

| Version | PHP required | PHP tested | Laravel | Release            | Bug Fixes | Security Fixes |
| ------- | ------------ | ---------- | ------- | ------------------ | --------- | -------------- |
| 2.2     | 8.3+         | 8.3        | 12.x    | March 17th, 2026   | Yes       | Yes            |
| 2.1     | 8.2+         | 8.2, 8.3   | 10.x    | March 26th, 2025   | No        | Yes            |

**Current:** 2.2.x

## End of Life

These versions no longer receive any patches. Upgrade to a supported
version.

| Version | PHP       | Laravel     | First Release      | Last Release                  |
| ------- | --------- | ----------- | ------------------ | ----------------------------- |
| 2.0     | 8.1+      | 10.x        | August 30th, 2024  | 2.0.6 — March 20th, 2025      |
| 1.x     | 7.3 - 8.x | 8.x - 10.x  | July 21st, 2021    | 1.3.1 — October 9th, 2024     |

---

## Reporting a Vulnerability

Report security issues privately. Do **not** open a public GitHub
issue, pull request or discussion.

**Email** — [support@krayincrm.com](mailto:support@krayincrm.com)

Please include:

- A description of the issue
- Steps to reproduce (and a PoC, if available)
- Impact assessment (CVSS 4.0 or 3.1 vector welcome)
- Affected version(s)
- A suggested fix, if any

## Security Process

For every **supported** line (any row in the Supported Versions table
with Security Fixes marked Yes), the flow is:

1. **Acknowledgement** within 72 hours
2. **Triage** — reproduction and severity assessment
3. **Fix development** on `2.2`, the current release line, then
   backported to every other supported line
4. **Patched releases** for each supported line, with the fix listed
   under a `[security]` entry in the [changelog](CHANGELOG.md)
5. **GitHub Security Advisory** published with the reporter credited
   (when the reporter consents)

We aim to ship patches within **14 days** of acknowledgement for high
or critical severity, and **30 days** for medium severity.

Please give us a reasonable chance to release a fix before you
disclose the issue publicly.

---

## Published Advisories

Fixed vulnerabilities are announced as
[GitHub Security Advisories](https://github.com/krayin/laravel-crm/security/advisories)
and listed in the [changelog](CHANGELOG.md) of the release that fixes
them, tagged `[security]`.

| CVE            | Issue                          | Fixed in | Reference                                                   |
| -------------- | ------------------------------ | -------- | ----------------------------------------------------------- |
| CVE-2026-38526 | Unrestricted file upload       | 2.2.4    | [#2585](https://github.com/krayin/laravel-crm/pull/2585)    |

---

## Preferred Language

Reports in **English** are easiest for our team to triage quickly.

---

## Thank You

Responsible disclosure keeps Krayin CRM and its users safe. Thank you
for taking the time to report issues to us privately.
