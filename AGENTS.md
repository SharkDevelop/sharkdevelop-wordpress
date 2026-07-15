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
