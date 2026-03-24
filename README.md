# Alynt Enhanced Search

A minimalistic, AJAX-powered search plugin for WordPress featuring a responsive grid layout, content-type filtering, and WooCommerce product integration.

## Features

- **AJAX-powered search** — Live results without page reloads
- **Responsive grid layout** — Configurable 1–5 column grid with CSS custom properties
- **Content-type filtering** — Toggle pills to filter between all results and products
- **WooCommerce integration** — Display product results alongside standard post types; filters to in-stock products only
- **Featured images** — Optional thumbnails for general posts and/or products
- **Excerpt display** — Configurable excerpt length per result card
- **Full color customization** — 26 color settings for every UI element, editable via color pickers in the admin
- **Custom CSS field** — Inject additional styles without editing files
- **Search result caching** — Transient-based cache (30 seconds) to reduce database load
- **Custom search template** — Plugin-provided template overrides the active theme's search template
- **Shortcode support** — Render a search trigger button or icon anywhere with `[alynt_es_search]`

## Requirements

- **WordPress:** 6.0 or later
- **PHP:** 7.4 or later
- **WooCommerce:** Optional. Install to enable product search and filtering.

## Installation

1. Upload the `alynt-enhanced-search` folder to `/wp-content/plugins/`
2. Activate the plugin through **Plugins > Installed Plugins** in the WordPress admin
3. Configure settings under **Settings > Enhanced Search**

## Usage

### Search Page

The plugin automatically replaces the active theme's search results template (`search.php`) with its own AJAX-powered template. Navigate to your site's search results page (e.g. `/?s=query`) to see it in action.

### Shortcode

Use the shortcode to add a search trigger anywhere:

```
[alynt_es_search]
[alynt_es_search type="icon"]
[alynt_es_search type="button" text="Find something" class="my-custom-class"]
```

**Attributes:**

| Attribute | Values | Default | Description |
|-----------|--------|---------|-------------|
| `type` | `button`, `icon` | `button` | Display style for the search trigger |
| `text` | any string | `Search` | Button label (used when `type="button"`) |
| `class` | any string | _(empty)_ | Additional CSS class(es) on the wrapper |

Clicking the button or icon takes the user to the WordPress search results page.

## Configuration

Navigate to **Settings > Enhanced Search** in the WordPress admin.

### General

| Setting | Description |
|---------|-------------|
| Post Types | Select which post types appear in search results |
| Results Per Page | Number of results per page (1–50, default 12) |
| Max Columns | Grid column count (1–5, default 3) |

### Display

| Setting | Description |
|---------|-------------|
| Show Excerpt | Toggle excerpt display on result cards |
| Excerpt Length | Word count for excerpts (5–100, default 20) |
| Show Featured Images (Posts) | Show thumbnail images for non-product results |
| Show Featured Images (Products) | Show thumbnail images for WooCommerce products |

### Colors

26 color settings control every visual element of the search UI, including the search form, result cards, toggle pills, category badges, and pagination. Each field accepts a 6-digit hex color code.

### Custom CSS

A textarea field for injecting additional CSS. Input is sanitized via `wp_strip_all_tags()`.

For a full settings reference see [docs/SETTINGS.md](docs/SETTINGS.md).

## FAQ

**Does this plugin replace my theme's search template?**
Yes. The plugin hooks into `template_include` to serve its own template on all `is_search()` pages.

**Does it work without WooCommerce?**
Yes. WooCommerce integration is optional. If WooCommerce is not active, product-specific features (product toggle pill, product image settings) are hidden automatically.

**How long are search results cached?**
Results are cached as WordPress transients for 30 seconds. The cache is cleared on plugin deactivation and uninstall.

**Can I override the search template from my theme?**
The current version always uses the plugin's bundled template. Custom template override support may be added in a future release.

**How do I clear the search cache manually?**
Deactivate and reactivate the plugin, or delete the plugin and reinstall it — both operations clear the cache.

## Changelog

See [CHANGELOG.md](CHANGELOG.md) for the full version history.

## License

GPL v2 or later — https://www.gnu.org/licenses/gpl-2.0.html
