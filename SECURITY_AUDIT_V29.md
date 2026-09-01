# Digital Star Consultants — Security Audit V29

## Completed hardening

- Restored CSRF protection for every browser/session write. No public/admin routes are exempted.
- Admin browser exceptions now render normal HTML; JSON is reserved for API/AJAX requests.
- Added a dedicated `private` filesystem disk for customer documents. Uploaded documents remain outside the public web root and downloads stay behind authenticated authorization.
- Public tracking no longer returns customer phone/email, staff notes, or submitted form/file values.
- Tracking reference routes are constrained to the expected `DSC-YYYYMMDD-XXXXXX` format (or the configured safe reference prefix for generated records).
- Dynamic service fields no longer support password/hidden field types; checkbox validation is explicit and select/radio rules validate option values.
- Public submission payloads have tighter nested-field limits and dynamic file MIME/size validation.
- Assignment now rejects inactive processors.
- Non-admin management users cannot create/promote privileged management accounts, manage management targets, reset their passwords, or delete them.
- Password reset no longer reveals whether an email exists and now sends the real reset notification. Reset raises password minimum to 12 characters, rotates the remember token, and clears database sessions when the database session driver is used.
- Production sessions automatically use secure cookies unless explicitly overridden; local development remains usable over HTTP.
- Security headers use a production-aware CSP and add HSTS only for HTTPS production responses.

## Still required before production

- Use HTTPS end-to-end and set `APP_ENV=production`, `APP_DEBUG=false`, a real `APP_KEY`, real mail credentials, queue workers, and a production database.
- Never run the demo `DatabaseSeeder` in production.
- Review third-party integrations, DNS, server firewall, backups, storage permissions, and monitoring on the deployment host.
- Consider moving from simple role-based permissions to a named permission matrix as the organization grows.
