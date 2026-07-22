# Contributing to MYADS

Thank you for your interest in contributing to **MYADS**! We welcome contributions, bug fixes, feature requests, and documentation improvements.

---

## 📜 Code of Conduct

Please note that this project is governed by our [Code of Conduct](CODE_OF_CONDUCT.md). By participating, you are expected to uphold these guidelines.

---

## 🛠️ How to Contribute

### 1. Reporting Bugs
Before opening an issue, please check existing issues to see if the bug has already been reported.

When submitting a bug report:
- Use the **🐛 Bug Report** template.
- Include clear steps to reproduce the issue.
- Attach log outputs or screenshots if relevant.

### 2. Suggesting Features
- Use the **💡 Feature Request** template.
- Clearly describe the problem the feature solves and how it fits into the MYADS ecosystem.

### 3. Submitting Pull Requests (PRs)
1. **Fork** the repository and create your branch from `main`:
   ```bash
   git checkout -b feature/my-amazing-feature
   ```
2. Make your code changes adhering to our coding style.
3. Ensure local tests pass:
   ```bash
   vendor/bin/phpunit
   ```
4. Commit your changes with a clear commit message.
5. Push to your branch and open a Pull Request.

---

## 🎨 Coding Standards

- **Laravel Backend**: Follow PSR-12 coding standards and Laravel conventions. Run `vendor/bin/pint` to format PHP code.
- **Flutter App (`myads_app`)**: Follow effective Dart guidelines and run `flutter analyze`.
- **Database**: Use Laravel Migrations for any schema changes.

---

## 🔒 Security Vulnerabilities

If you discover a security vulnerability within MYADS, please review our [Security Policy](SECURITY.md) for reporting guidelines instead of creating a public issue.

---

Thank you for helping build a better MYADS ecosystem! 🚀
