# Security Policy

The MYADS team takes security vulnerabilities very seriously. We appreciate the efforts of security researchers and developers who help keep our platform and community safe.

---

## 🛡️ Supported Versions

We provide security updates for the following versions of MYADS:

| Version | Supported          | Security Maintenance Status |
| ------- | ------------------ | --------------------------- |
| 4.5.x   | :white_check_mark: | Active Development & Patches|
| 4.4.x   | :white_check_mark: | Active Security Patches     |
| 4.3.x   | :white_check_mark: | Security Patches Only       |
| 4.2.x   | :white_check_mark: | Security Patches Only       |
| < 4.2   | :x:                | End of Life (Unsupported)   |

---

## 🔒 Built-in Security Protections & Mitigation Features

MYADS includes multi-layered security controls across the core platform, API, and ad exchange:

- **Anti-Click-Farm Protection (Custom Ads)**: Multi-layered rate limiting using privacy-first anonymous SHA-256 visitor fingerprinting (`sha256(IP + UserAgent + AcceptLanguage)`), 24-hour impression/click rate windows, and 1.5-second minimum session dwell verification. Fraudulent or bot clicks are recorded with `is_flagged = true` without deducting advertiser PTS or rewarding publishers.
- **Strict Table Sanitization**: Strict table name whitelisting and PDO query isolation on administrative database tools (`/admin/system-monitor` and `/admin/database-cleanup`).
- **CSRF & Rate Limiting**: Token-based anti-fraud protection across visit exchange, status reactions, comments, and member messaging.
- **Dependency Vulnerability Scanning**: Continuous dependency updates and package overrides (e.g. PostCSS `postcss >= 8.5.18` security patch).

---

## 📩 Reporting a Vulnerability

If you discover a potential security vulnerability in MYADS (Web App, API, or Mobile App), please follow our responsible disclosure policy:

1. **Private Reporting**: Submit your findings privately via [GitHub Private Vulnerability Reporting](https://github.com/mrghozzi/myads/security/advisories/new) or directly contact the author (`mrghozzi`).
2. **Do Not Disclose Publicly**: Please **do not** create public issues, pull requests, or forum posts regarding unpatched vulnerabilities.
3. **Include Details**: Provide a clear description of the vulnerability, step-by-step reproduction instructions, and proof of concept (PoC) if applicable.

---

## ⏱️ Response & Disclosure Timeline

- **Initial Acknowledgment**: We aim to acknowledge vulnerability reports within **48 hours**.
- **Assessment & Triage**: We will assess the risk, verify the issue, and keep you informed of progress.
- **Fix & Patch Release**: A security patch will be tagged and released as quickly as possible.
- **Public Disclosure**: Once the fix is published and users have had time to update, a public advisory will be issued thanking the reporter.

Thank you for helping keep MYADS and its webmasters safe! 🚀
