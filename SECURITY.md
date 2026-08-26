# Security Policy

The MYADS team takes platform and community security vulnerabilities very seriously. We appreciate the efforts of security researchers and developers who help keep our platform, webmasters, and users safe.

---

## 🛡️ Supported Versions

We provide active security updates and maintenance for the following versions of MYADS:

| Version | Supported          | Security Maintenance Status |
| ------- | ------------------ | --------------------------- |
| 4.5.x   | :white_check_mark: | Active Development, Patches & Latest Features (Current: `v4.5.3`) |
| 4.4.x   | :white_check_mark: | Active Security Patches Only |
| 4.3.x   | :white_check_mark: | Critical Security Patches Only |
| 4.2.x   | :white_check_mark: | Critical Security Patches Only |
| < 4.2   | :x:                | End of Life (Unsupported) |

---

## 🔒 Built-in Security Protections & Mitigation Features

MYADS includes multi-layered security controls across the core platform, API, and ad exchange ecosystem:

- **Public Member IDs & Enumeration Protection**: When `public_member_ids_enabled` is active in `/admin/security`, numeric database ID lookups are strictly blocked with HTTP 404 on profile routes (`/u/{id}`) to prevent user enumeration. All internal database `id`s in referral links (`?ref=`), ad impression trackers, API resources, mention lookups, follow/block routes, and popover handlers are replaced with secure public identifiers (`public_uid`).
- **Anti-Click-Farm & Click Fraud Protection (Custom Ads)**: Multi-layered verification using privacy-first anonymous SHA-256 visitor fingerprinting (`sha256(IP + UserAgent + AcceptLanguage)`), 24-hour rate limiting windows per visitor, and a 1.5-second minimum session dwell window check. Suspicious or rapid clicks are flagged (`is_flagged = true`) and isolated from PTS billing without deducting advertiser points or rewarding publishers.
- **Granular OAuth 2.0 Scopes & Token Authorization**: Developer platform enforces 27 granular permission scopes across 7 categories (Identity, Content, Messages, Wallet, Media, Store, Owner Integrations) with strict scope-checking middleware on all Developer API v1 endpoints.
- **Sanctum Multi-Device Session Control**: Complete visibility into active mobile API tokens and browser sessions with instant single-click remote token revocation (`POST /api/settings/tokens/{id}/revoke`).
- **Maintenance Mode Safety & IP Whitelisting**: Dual-layer maintenance mode access control featuring an Allowed IP Whitelist (`allowed_ips`) and a secret emergency bypass token (`emergency_token`) for safe administrative access during 503 maintenance mode.
- **Safe Orphan Records Cleanup (`OrphanCleanupService`)**: Transaction-isolated, subquery-based cascading cleanup engine preventing data corruption or inadvertent content/reaction deletion during database maintenance.
- **Strict Table Sanitization & Query Isolation**: Strict table name whitelisting and PDO query isolation on administrative database tools (`/admin/system-monitor`, `/admin/database-cleanup`, and `/admin/maintenance/db-repair`).
- **Stored SVG XSS Elimination**: SVG MIME type is restricted from rich text editor uploads, eliminating Stored Cross-Site Scripting attack vectors via embedded scripts in SVG payloads.
- **OAuth Rejection Open Redirect Prevention**: Strict registration whitelist validation on application `redirect_uri` prior to issuing authorization rejection redirects.
- **Default Session Encryption & Secure Cookies**: Mandatory session payload encryption (`SESSION_ENCRYPT=true`) and HTTPS-only secure cookies (`SESSION_SECURE_COOKIE=true`) protecting session data at rest and over the wire.
- **Constant-Time Cryptographic Comparison**: Constant-time `hash_equals()` string comparison across legacy MD5 password verification and OAuth client secrets to neutralize timing attack vectors.
- **Cryptographic Update Script Verification**: Mandatory SHA-256 integrity signature verification (`update.php.sha256`) before executing custom disk update scripts during system updates.
- **Strict Production SSL Enforcement (`http_secure()`)**: Centralized HTTP client enforcing strict SSL certificate verification in production environments for extension update checks and remote marketplace operations.
- **Username Character Whitelist Validation**: Strict regex whitelisting (`regex:/^[\p{L}\p{N}_\-\.]+$/u`) on member usernames preventing HTML/script payload injection.
- **Dependency & Transitive Vulnerability Management**: Continuous dependency audits and package overrides (e.g. PostCSS `postcss >= 8.5.18` security patch, `guzzlehttp/guzzle 7.15.2`, `league/commonmark 2.9.0` quadratic-time DoS patch).

---

## 📩 Reporting a Vulnerability

If you discover a potential security vulnerability in MYADS (Web Platform, REST API, or Flutter Mobile App), please follow our responsible disclosure policy:

1. **Private Reporting**: Submit your findings privately via [GitHub Private Vulnerability Reporting](https://github.com/mrghozzi/myads/security/advisories/new) or directly contact the maintainer ([@mrghozzi](https://github.com/mrghozzi)).
2. **Do Not Disclose Publicly**: Please **do not** open public issues, discussion threads, or pull requests regarding unpatched vulnerabilities.
3. **Include Reproduction Details**: Provide a clear description of the vulnerability, step-by-step reproduction instructions, affected components, and proof of concept (PoC) if applicable.

---

## ⏱️ Response & Disclosure Timeline

- **Initial Acknowledgment**: We aim to acknowledge vulnerability reports within **24 to 48 hours**.
- **Assessment & Triage**: We will assess the risk, verify the issue in our test suite, and keep you informed of remediation progress.
- **Fix & Patch Release**: A security patch will be committed, tested, tagged, and released as quickly as possible.
- **Public Disclosure**: Once the fix is published and users have had sufficient time to update, a public advisory will be issued with credit to the reporter.

Thank you for helping keep MYADS and its webmasters safe! 🚀
