# Frontend Architecture

## Direction

The rebuild uses WordPress as the CMS and a custom theme as the frontend layer.

Elementor is not a frontend dependency for the new build.

## Layers

- WordPress: admin, content, users, media, translations, SEO.
- `Shark Develop Core` mu-plugin: project content types, taxonomies, and metadata.
- `Shark Develop` theme: templates, layout, CSS, JS, and rendering.
- Gutenberg: structured page editing after the base templates are stable.

## Initial Data Model

`sd_project`

- title
- editor content
- excerpt
- featured image
- project summary
- client
- year
- tools
- project URL
- gallery
- featured flag
- homepage order
- visual theme token
- card size token
- platforms
- technologies
- project type
- services

`sd_service`

- title
- editor content
- excerpt
- featured image
- service summary
- icon
- featured flag
- homepage order
- service area

## First Build Target

Start with portfolio because projects are structured and repeatable.

Minimum screens:

- project archive
- project detail
- service archive
- service detail
- regular page
- blog archive
- blog post

## Plugin Cleanup

Do not remove plugins blindly.

First classify plugins as:

- required for CMS, SEO, forms, translations, or media;
- temporary migration dependencies;
- Elementor-only dependencies;
- unused legacy plugins.

Disable Elementor-related plugins only after the new theme can render the target pages locally.
