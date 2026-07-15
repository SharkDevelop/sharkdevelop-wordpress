# Deployment

## Current Scope

Only dev deployment is active.

- Local source: Local WordPress site.
- Git branch: `dev`.
- Dev URL: `https://dev.sharkdevelop.com`.
- Production is not part of the current workflow.

## Dev Deploy Command

Run from the repository root:

```bash
./scripts/deploy-dev.sh
```

## Before Deploy

- Be on the `dev` branch.
- Make sure the Local site is running.
- Make sure Git is clean.
- Do not deploy with uncommitted changes.

Useful check:

```bash
git status --short
```

## What The Script Does

1. Checks local environment.
2. Checks SSH access to the dev server.
3. Pushes the `dev` branch.
4. Pulls latest `dev` on the dev server.
5. Exports the Local database.
6. Syncs `wp-content/uploads/` to dev.
7. Uploads the database dump to dev.
8. Imports the database into the dev database.
9. Replaces Local URLs with the dev URL.
10. Sets `home` and `siteurl` to the dev URL.
11. Marks the dev site as not public.
12. Deactivates `wps-hide-login` on dev if present.
13. Flushes WordPress cache and rewrite rules.
14. Flushes Elementor CSS.
15. Removes temporary database dump files.

## Important Notes

- The dev database is overwritten from Local.
- Dev uploads are synced from Local.
- By default, remote uploads are not deleted.
- `DELETE_REMOTE_UPLOADS=1` may be used only when dev uploads should exactly mirror Local uploads.
- The script must not be pointed at production.

## Troubleshooting

If the deploy fails while exporting the database:

- Start the site in Local.
- Confirm Local generated `app/.envrc`.
- Re-run the deploy command.

If the dev site appears without styles:

- Confirm the deploy reached the Elementor CSS flush step.
- Run on dev if needed:

```bash
wp elementor flush_css || true
```

## Deploy Policy

Local → Dev

Only after verification on dev may changes be merged and later deployed to production.

Production deployment is a separate workflow and is currently disabled.
