# Digital Star Consultants - Recreated Public Frontend

This package recreates the supplied `demo1.html` design language rather than copying its museum content. The source composition was translated into a Digital Star Consultants technology consultancy experience.

## Design translation
- Cinematic dark spatial background from the reference
- Orbiting hero visual recreated with CSS instead of importing the original museum artwork
- Monospace technical labels and editorial oversized typography
- Asymmetric capability layout
- Timeline-like delivery rail
- Case-study composition
- Technical ecosystem matrix
- FAQ and conversion/track section
- Digital Star brand colors: navy, blue, yellow, white

## Laravel integration
The layout and homepage use the existing named routes:
`home`, `public.services.index`, `work`, `about`, `public.track.form`, `public.contact.show`, `login`.

Admin/auth/backend files are intentionally excluded.

## Install
Copy the `resources` files into the project, then run:

`npm run build`

The existing Laravel controllers/data contracts should remain the source of truth for the service, tracking and contact pages.

## Management authentication

Management uses a dedicated `/admin/login` portal. There is intentionally no public management self-registration route: administrators create staff and management accounts from the authenticated Admin → Users area. This prevents anyone on the public site from registering an administrative account.

For local development, run `php artisan db:seed` to create the demo management accounts defined in `DatabaseSeeder`. Change those credentials before any real deployment.
