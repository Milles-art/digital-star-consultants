# Digital Star public frontend clean slate

Replace only the public Blade/CSS/JS files listed in this bundle. Do NOT replace admin/auth views.

The Blade views preserve the public route/data contracts discussed for Digital Star Consultants.
After copying, run:

php artisan view:clear
npm run build

Then verify:
GET /
GET /services
GET /work
GET /about
GET /track
GET /contact
