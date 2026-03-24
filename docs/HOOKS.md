# Hooks Reference

## Custom Hooks

Alynt Enhanced Search does not currently define or fire any custom `do_action()` or `apply_filters()` hooks. The plugin is not designed as an extensibility platform in its current version.

---

## WordPress Hooks Used

The following is a reference of all WordPress core hooks this plugin registers against.

### Actions

| Hook | Callback | Priority | File |
|------|----------|----------|------|
| `init` | `Alynt_Enhanced_Search::init()` | 10 | `alynt-enhanced-search.php` |
| `wp_enqueue_scripts` | `Alynt_Enhanced_Search::enqueue_scripts()` | 10 | `alynt-enhanced-search.php` |
| `wp_enqueue_scripts` | `Alynt_ES_Search_Style_Manager::enqueue_dynamic_styles()` | 20 | `public/class-search-style-manager.php` |
| `wp_head` | `Alynt_ES_Search_Style_Manager::add_cache_headers()` | 10 | `public/class-search-style-manager.php` |
| `admin_enqueue_scripts` | `Alynt_Enhanced_Search::enqueue_admin_scripts()` | 10 | `alynt-enhanced-search.php` |
| `admin_menu` | `Alynt_ES_Admin_Settings_Page::add_admin_menu()` | 10 | `admin/class-admin-settings-page.php` |
| `admin_init` | `Alynt_ES_Admin_Settings_Registry::init_settings()` | 10 | `admin/class-admin-settings-page.php` |
| `wp_ajax_alynt_es_search` | `Alynt_ES_Ajax_Handler::handle_search()` | 10 | `includes/class-ajax-handler.php` |
| `wp_ajax_nopriv_alynt_es_search` | `Alynt_ES_Ajax_Handler::handle_search()` | 10 | `includes/class-ajax-handler.php` |

### Filters

| Hook | Callback | Priority | File |
|------|----------|----------|------|
| `template_include` | `Alynt_ES_Search_Template_Loader::load_search_template()` | 10 | `public/class-search-template-loader.php` |
| `posts_search` _(temporary)_ | `Alynt_ES_Search_Query_Service::custom_search_filter()` | 10 | `includes/class-search-query-service.php` |
| `posts_orderby` _(temporary)_ | `Alynt_ES_Search_Query_Service::custom_search_orderby()` | 10 | `includes/class-search-query-service.php` |

> **Note:** `posts_search` and `posts_orderby` are added and removed within a single `perform_search()` call. They are not permanently registered.

---

## AJAX Action

The plugin responds to the AJAX action `alynt_es_search` for both logged-in and logged-out users.

**Action:** `alynt_es_search`
**Method:** POST
**Nonce:** `alynt_es_nonce` (field name: `nonce`)

**POST Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `nonce` | string | Security nonce generated with `wp_create_nonce('alynt_es_nonce')` |
| `query` | string | The search term |
| `type` | string | Content type filter: `'all'` or `'products'` |
| `page` | int | Results page number (1-based) |

**Success Response:**
```json
{
  "success": true,
  "data": {
    "posts": [...],
    "pagination": {...},
    "total": 42,
    "pages": 4
  }
}
```

**Error Response (invalid nonce):**
```json
{
  "success": false,
  "data": { "message": "Invalid nonce." }
}
```
Status code: 403

---

## Shortcode

**Tag:** `[alynt_es_search]`

**Registered in:** `public/class-shortcode.php`

**Attributes:**

| Attribute | Type | Default | Description |
|-----------|------|---------|-------------|
| `type` | string | `button` | `button` or `icon` |
| `text` | string | `Search` | Button label text (button type only) |
| `class` | string | _(empty)_ | Additional CSS class(es) |
