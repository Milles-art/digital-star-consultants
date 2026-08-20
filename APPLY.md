# Round 5 – Polish remaining controllers

## Changes

| File | What was fixed |
|------|----------------|
| `Auth/RegisterController.php` | Explicit assignment of role/is_active (no longer fillable). No temp password in response. |
| `Admin/ContactMessageController.php` | Implemented (was empty stub). Management-only. |
| `Admin/ServiceFieldController.php` | Route model binding, authorize calls, cleaner validation. |

## Apply

```bash
cd ~/digital-star-consultants
git checkout fix/security-and-architecture

unzip digital-star-fixes-round5.zip

cp laravel-fixes-round5/app/Http/Controllers/Auth/RegisterController.php app/Http/Controllers/Auth/
cp laravel-fixes-round5/app/Http/Controllers/Admin/ContactMessageController.php app/Http/Controllers/Admin/
cp laravel-fixes-round5/app/Http/Controllers/Admin/ServiceFieldController.php app/Http/Controllers/Admin/

git add app/Http/Controllers/Auth/RegisterController.php \
        app/Http/Controllers/Admin/ContactMessageController.php \
        app/Http/Controllers/Admin/ServiceFieldController.php

git commit -m "fix: polish Register, ContactMessage, ServiceField controllers"
git push
```

## Note on routes

If you want contact messages in the admin UI, add these routes under the admin group in `routes/web.php`:

```php
Route::get('/contact-messages', [App\Http\Controllers\Admin\ContactMessageController::class, 'index'])->name('admin.contact-messages.index');
Route::get('/contact-messages/{contactMessage}', [App\Http\Controllers\Admin\ContactMessageController::class, 'show'])->name('admin.contact-messages.show');
Route::delete('/contact-messages/{contactMessage}', [App\Http\Controllers\Admin\ContactMessageController::class, 'destroy'])->name('admin.contact-messages.destroy');
```

Service field routes already exist; they will now use model binding correctly if the route parameters match (`{service}`, `{field}`).
