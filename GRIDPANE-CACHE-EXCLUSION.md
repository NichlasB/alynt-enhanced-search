# GridPane Cache Exclusion Instructions

To ensure the Alynt Enhanced Search plugin works correctly with GridPane's caching system, you need to exclude the search page from server-side caching.

## Method 1: Exclude Search Pages (Recommended)

Add the following to your site's Nginx configuration in GridPane:

```nginx
# Exclude search pages from caching
location ~* /\?s= {
    set $skip_cache 1;
}

location ~* /search/ {
    set $skip_cache 1;
}
```

## Method 2: Exclude AJAX Endpoints

If you prefer to only exclude the AJAX endpoints, add this instead:

```nginx
# Exclude Enhanced Search AJAX from caching
location ~* /wp-admin/admin-ajax\.php {
    if ($request_body ~* "action=alynt_es_search") {
        set $skip_cache 1;
    }
}
```

## How to Apply in GridPane

1. **Log into your GridPane control panel**
2. **Navigate to your site**
3. **Go to Nginx → Custom Config**
4. **Add the configuration above to your custom Nginx config**
5. **Click "Update Nginx Config"**
6. **Test the search functionality**

## Additional Cache Headers

The plugin automatically adds the following cache-busting headers to search pages:

```
Cache-Control: no-cache, no-store, must-revalidate
Pragma: no-cache
Expires: 0
```

## Redis Object Cache Compatibility

The plugin uses WordPress transients for short-term result caching (30 seconds), which works seamlessly with GridPane's Redis Object Caching. This provides a good balance between performance and real-time results.

## Testing Cache Exclusion

To verify the cache exclusion is working:

1. **Perform a search on your site**
2. **Check the response headers** (using browser dev tools)
3. **Look for the cache-control headers mentioned above**
4. **Verify that search results update immediately when you modify content**

## Troubleshooting

If search results appear cached:

1. **Verify the Nginx configuration is applied correctly**
2. **Clear all caches (Redis + Nginx)**
3. **Check that the custom headers are being sent**
4. **Contact GridPane support if issues persist**

For more information about GridPane cache exclusions, see: https://gridpane.com/kb/exclude-a-page-from-server-caching/
