# Round 4 – Feature tests + factories

## What’s included

### Factories
- `UserFactory` (with `admin()`, `staff()`, `inactive()` states)
- `ServiceCategoryFactory`
- `ServiceFactory`
- `SubmissionFactory` (with `assignedTo()`, `completed()` states)

### Feature tests
- `PublicSubmissionTest` – public submit, validation, track (no PII leak)
- `AuthLoginTest` – login success/fail, inactive user, rate limiting
- `StaffSubmissionAccessTest` – staff only sees assigned submissions, isolation, admin access
- `MassAssignmentProtectionTest` – role/status cannot be mass-assigned, no temp password leak

## Apply

```bash
cd ~/digital-star-consultants
git checkout fix/security-and-architecture

unzip digital-star-fixes-round4.zip

cp -r laravel-fixes-round4/database/factories/* database/factories/
cp -r laravel-fixes-round4/tests/Feature/* tests/Feature/

# Make sure models use HasFactory (most already do)
git add database/factories/ tests/Feature/

git commit -m "test: add feature tests for public submit, track PII, auth rate limit, staff isolation, mass assignment"
git push
```

## Run the tests

```bash
php artisan test --group=public
php artisan test --group=auth
php artisan test --group=staff
php artisan test --group=security

# Or everything
php artisan test
```
