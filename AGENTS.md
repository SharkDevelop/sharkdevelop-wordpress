# Codex Instructions

## Project Context

- This is a WordPress site using Elementor.
- Local development runs through Local.
- Local public path: `/Users/olgastennikova/Local Sites/sharkdevelop/app/public`.
- That Local public path is a symbolic link to this repository.
- Integration branch: `dev`.
- Dev site: `https://dev.sharkdevelop.com`.
- Production is out of scope for now. Do not change production.

## Repository Rules

- WordPress core is not managed as project code.
- `wp-config.php` is not stored in Git.
- `wp-content/uploads/` is not stored in Git.
- Do not commit secrets, database dumps, archives, backups, cache files, or uploaded media.
- Do not manually edit generated Elementor CSS files.
- Keep changes focused and avoid unrelated cleanup.

## Solution Quality

- Do not add hacks, workaround layers, or masking fixes when the root cause can be corrected directly.
- First identify the real source of a problem, then fix that source in the proper system: plugin setting, WordPress content, theme code, server config, or deployment script.
- Do not hide symptoms with CSS, JavaScript, filters, or defensive overrides unless the user explicitly approves that temporary approach.
- Prefer simple, maintainable changes that match WordPress and theme conventions.

## Temporary Asset Folders

- `_projects/` is a temporary workspace for source project materials only.
- `_images/` is a temporary workspace for source image files only.
- Do not treat `_projects/` or `_images/` as website storage.
- Do not reference files from `_projects/` or `_images/` in theme code, page content, CSS, or database content.
- Do not commit `_projects/` or `_images/`.
- When a file from these folders must be used on the site, import or copy it into the proper WordPress location first:
  - images and media go through the WordPress Media Library and `wp-content/uploads/`;
  - project content goes into the appropriate WordPress post type, currently `sd_project`.
- After importing, the temporary source file may remain in `_projects/` or `_images/` only as local working material.

## Design Palette

- Use the Shark Develop palette as shared theme tokens, not scattered one-off colors.
- Base palette:
  - Background: `#0B1020`
  - Surface: `#151D2D`
  - Card: `#1B2438`
  - Primary: `#2563FF`
  - Secondary: `#0AA7FF`
  - Accent: `#9055FF`
  - Text: `#F4F7FB`
  - Secondary text: `#A6B9C3`
  - Border: `#273043`
- The labels are palette roles, not strict usage rules. A light section may use `#F4F7FB` as background and `#0B1020` as text; a dark section may use `#0B1020` as background and `#F4F7FB` as text.
- Prefer CSS variables in `wp-content/themes/sharkdevelop/assets/css/app.css` for colors, gradients, borders, shadows, and translucent overlays.
- Do not introduce unrelated colors unless there is a specific visual requirement and it is added as a named token first.
- Primary gradient: `#2563FF -> #0AA7FF`.
- Accent gradient: `#9055FF -> #0AA7FF`.

## Workflow

1. Work on the `dev` branch unless the user says otherwise.
2. Check the current Git state before making changes.
3. Make small, reviewable changes.
4. Do not commit until the user has reviewed the local result, unless the user explicitly asks for a commit.
5. Test locally first when possible.
6. Use the dev site for integration checks.
7. Before deploy, Git must be clean.
8. Deploy local database and uploads to dev with:

```bash
./scripts/deploy-dev.sh
```

## Deployment Boundary

- `dev` is allowed to receive full local DB and uploads sync.
- Production must not be deployed, migrated, imported into, or otherwise changed.
- If a task could affect production, stop and ask for confirmation.

## Elementor Rules

- Prefer project code, WP-CLI, and controlled database changes.
- Do not rely on manual edits in WordPress or Elementor admin unless the user explicitly requests them.
- Do not edit Elementor-generated CSS by hand.
- After DB import to dev, regenerate Elementor CSS with WP-CLI when needed:

```bash
wp elementor flush_css || true
```
