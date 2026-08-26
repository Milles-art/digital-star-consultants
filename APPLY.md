# Admin submission show page

## Features
- Customer + service details
- Dynamic field values
- File download links (private disk)
- Assign staff
- Status: in progress / complete / reject (with reason)
- Staff notes (PUT update)
- Browser forms redirect back with flash (API JSON still works)

## Apply

```bash
cd ~/digital-star-consultants
unzip digital-star-submission-show.zip

mkdir -p resources/views/admin/submissions
cp laravel-submission-show/app/Http/Controllers/Admin/SubmissionController.php app/Http/Controllers/Admin/
cp laravel-submission-show/resources/views/admin/submissions/show.blade.php resources/views/admin/submissions/

php artisan view:clear
```

Open any submission from the list: `/admin/submissions/{id}`
