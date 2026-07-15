# WordPress Rules

## General

- Treat Local as the source for dev database and media.
- Treat Git as the source for project code.
- Do not treat Git as the source for uploads or database state.
- Do not change production.

## Elementor

- Do not manually edit generated Elementor CSS files.
- Make layout/content changes through Elementor or controlled project code.
- After database import on dev, flush Elementor CSS:

```bash
wp elementor flush_css || true
```

- If styles look broken on dev, check Elementor CSS regeneration before changing theme code.

## Plugins

- Avoid changing plugin files directly unless they are project-owned custom plugins.
- Follow existing project workflows.
- Do not modify plugin source code unless it is a project-owned plugin.
- Prefer documented WP-CLI commands when available.
- After pulling code on dev, confirm WordPress can load plugins:

```bash
wp plugin list >/dev/null
```

## Uploads

- `wp-content/uploads/` is not stored in Git.
- Uploads are synced to dev by `./scripts/deploy-dev.sh`.
- Do not manually copy uploads into Git.
- Do not enable deletion of remote uploads unless dev must exactly match Local.

## Cache

- Cache files are not project source.
- Do not commit cache directories.
- After database import or deploy, flush cache:

```bash
wp cache flush
```

## Database

- Database dumps are not stored in Git.
- The dev database may be overwritten from Local.
- Production database must not be overwritten.
- After importing the Local database to dev, replace URLs from Local to dev.

## Config

- `wp-config.php` is environment-specific and is not stored in Git.
- Do not add credentials or environment secrets to the repository.

## Generated Files

Never manually edit:

- wp-content/uploads/
- Elementor generated CSS
- cache directories
- temporary files