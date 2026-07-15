# Project Overview

## Project

Shark Develop company website.

Purpose:
- present the company and its services;
- publish portfolio and case studies;
- generate client leads;
- support SEO content;
- be maintained through WordPress + Elementor.

## Stack

- WordPress site.
- Elementor is used for page layout and generated styles.
- Local development runs through Local.
- Local public path:

```text
/Users/olgastennikova/Local Sites/sharkdevelop/app/public
```

- The Local public path is a symbolic link to this repository:

```text
/Users/olgastennikova/Documents/sharkdevelop-wordpress
```

## Git

- Current integration branch: `dev`.
- The `dev` branch deploys to:

```text
https://dev.sharkdevelop.com
```

## What Belongs In Git

- Project theme/plugin code that is intentionally maintained.
- Deployment scripts.
- Documentation.
- Task and workflow notes.

## What Does Not Belong In Git

- WordPress core.
- `wp-config.php`.
- `wp-content/uploads/`.
- Database dumps.
- Secrets and credentials.
- Cache directories.
- Generated backups and archives.

## Current Deployment Model

- Code is pushed through Git.
- Dev database and media are synced from Local with:

```bash
./scripts/deploy-dev.sh
```

- Production deployment is not configured and must not be changed.

