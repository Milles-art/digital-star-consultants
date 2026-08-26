# Public site v1 — install notes

## Files in this package

```
app/Http/Middleware/SetLocale.php
app/Http/Controllers/Public/AboutController.php
app/Http/Controllers/Public/PortfolioController.php
app/Http/Controllers/Public/TrackPageController.php
resources/lang/en/site.php
resources/lang/sw/site.php
resources/views/layouts/app.blade.php
resources/views/home.blade.php
resources/views/about.blade.php
resources/views/work.blade.php
resources/views/contact.blade.php
resources/views/services/index.blade.php
resources/views/services/show.blade.php
resources/views/track/form.blade.php
resources/views/track/show.blade.php
routes/public-additions.php   ← copy routes into web.php
```

## Steps

1. Extract into project root (merge).
2. Register middleware `SetLocale` globally or on web group (bootstrap/app.php or Kernel).
3. Merge routes from `routes/public-additions.php` into `routes/web.php`.
4. Ensure `ContactController::show` returns `view('contact')` (already in repo).
5. `npm run build`
6. Test `/`, `/services`, `/work`, `/about`, `/track`, `/contact`, locale switch.

## Phones

- 0783 257 716 → wa.me/255783257716
- 0754 931 751 → wa.me/255754931751

## Address

Mbagala, near Puma gas station, Dar es Salaam
