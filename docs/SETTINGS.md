# Settings Reference

All plugin settings are stored in a single serialized array under the `alynt_es_settings` option key.

**Option name:** `alynt_es_settings`
**Option group:** `alynt_es_settings_group`
**Sanitize callback:** `Alynt_ES_Admin_Settings_Sanitizer::sanitize_settings()`

---

## General Settings

| Option Key | Type | Default | Sanitization | Tab | Description |
|------------|------|---------|--------------|-----|-------------|
| `post_types` | array | `['post', 'page']` | `sanitize_text_field` per value, validated against registered public post types | General | Post types included in search results |
| `results_per_page` | int | `12` | `intval`, clamped 1–50 | General | Number of results displayed per page |
| `max_columns` | int | `3` | `intval`, clamped 1–5 | General | Maximum grid columns for the results layout |

---

## Display Settings

| Option Key | Type | Default | Sanitization | Tab | Description |
|------------|------|---------|--------------|-----|-------------|
| `show_excerpt` | bool | `true` | Cast to `1`/`''` | Display | Whether to show excerpt text on result cards |
| `excerpt_length` | int | `20` | `intval`, clamped 5–100 | Display | Number of words in each excerpt |
| `show_featured_images_general` | bool | `true` | Cast to `1`/`''` | Display | Show featured image thumbnails on non-product result cards |
| `show_featured_images_products` | bool | `true` | Cast to `1`/`''` | Display | Show featured image thumbnails on WooCommerce product cards |

---

## Color Settings

All color values are 6-digit hex strings (e.g. `#ffffff`). Input is validated against `/^#[0-9A-Fa-f]{6}$/` and falls back to the default on failure.

| Option Key | Default | Tab | Description |
|------------|---------|-----|-------------|
| `search_icon_color` | `#333333` | Colors | Color of the search icon in the shortcode icon variant |
| `search_page_bg_color` | `#f5f5f5` | Colors | Background color of the search results page |
| `main_title_text_color` | `#333333` | Colors | Color of the main search results heading |
| `toggle_pill_bg_color` | `#e0e0e0` | Colors | Background color of inactive content-type toggle pills |
| `toggle_pill_text_color` | `#333333` | Colors | Text color of inactive toggle pills |
| `toggle_pill_active_bg_color` | `#0073aa` | Colors | Background color of the active toggle pill |
| `toggle_pill_active_text_color` | `#ffffff` | Colors | Text color of the active toggle pill |
| `result_card_border_color` | `#dddddd` | Colors | Border color of result cards |
| `card_title_text_color` | `#333333` | Colors | Color of result card title text |
| `card_excerpt_container_bg_color` | `#ffffff` | Colors | Background color of the excerpt container within a card |
| `card_excerpt_text_color` | `#666666` | Colors | Color of excerpt text |
| `category_border_color` | `#cccccc` | Colors | Border color of category/tag badges |
| `category_text_color` | `#555555` | Colors | Text color of category/tag badges |
| `category_bg_color` | `#f0f0f0` | Colors | Background color of category/tag badges |
| `product_image_bg_color` | `#f9f9f9` | Colors | Background color behind product featured images |
| `result_card_bg_color` | `#ffffff` | Colors | Background color of result cards |
| `pagination_item_bg_color` | `#f0f0f0` | Colors | Background color of pagination page buttons |
| `pagination_item_text_color` | `#333333` | Colors | Text color of pagination page buttons |
| `pagination_current_bg_color` | `#0073aa` | Colors | Background color of the current page indicator |
| `pagination_current_text_color` | `#ffffff` | Colors | Text color of the current page indicator |
| `pagination_item_border_color` | `#dddddd` | Colors | Border color of pagination page buttons |
| `search_form_border_color` | `#dddddd` | Colors | Border color of the search form input |
| `search_form_bg_color` | `#ffffff` | Colors | Background color of the search form input |
| `search_submit_bg_color` | `#0073aa` | Colors | Background color of the search submit button |
| `search_submit_bg_hover_color` | `#005177` | Colors | Hover background color of the search submit button |
| `search_submit_text_color` | `#ffffff` | Colors | Text color of the search submit button |
| `search_submit_text_hover_color` | `#ffffff` | Colors | Hover text color of the search submit button |

---

## Custom CSS

| Option Key | Type | Default | Sanitization | Tab | Description |
|------------|------|---------|--------------|-----|-------------|
| `custom_css` | string | `''` | `wp_strip_all_tags` | Custom CSS | Additional CSS injected into the search page via inline style |
