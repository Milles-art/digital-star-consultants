# Production Checklist – Digital Star Consultants

## Before go-live

### Environment
- [ ] `APP_ENV=production`
- [ ] `APP_DEBUG=false`
- [ ] Strong unique `APP_KEY` (`php artisan key:generate`)
- [ ] Real `APP_URL` (https)
- [ ] Database credentials correct
- [ ] Mail driver configured (SMTP / SES / etc.)
- [ ] Queue driver not `sync` in production (use `database` or Redis)
- [ ] `SESSION_SECURE_COOKIE=true`
- [ ] `SESSION_SAME_SITE=lax` (or strict)

### Security
- [ ] Public site needs **no login** (already the case)
- [ ] Staff/admin behind auth + role middleware (done)
- [ ] Files on `private` disk only (done)
- [ ] Rate limits on login, register, submit, contact, track (done)
- [ ] Track endpoint does not leak phone/email (done)
- [ ] No temp passwords returned in API responses (done)
- [ ] `composer audit` clean
- [ ] Web root points to `/public` only

### Performance
- [ ] `php artisan config:cache`
- [ ] `php artisan route:cache`
- [ ] `php artisan view:cache`
- [ ] Queue worker running (`php artisan queue:work` or Supervisor)
- [ ] Scheduler running (`* * * * * php artisan schedule:run`)

### Data
- [ ] Run migrations: `php artisan migrate --force`
- [ ] Seed demo users only on staging: `php artisan db:seed`
- [ ] Change default seeded passwords immediately

### Seeded demo accounts (from DatabaseSeeder)
| Email | Password | Role |
|-------|----------|------|
| admin@digitalstar.local | password | admin |
| ceo@digitalstar.local | password | ceo |
| gm@digitalstar.local | password | gm |
| staff1@digitalstar.local | password | staff |
| staff2@digitalstar.local | password | staff |

**Delete or change these before production.**
