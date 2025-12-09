# BRF Live Filters

A flexible, fast, and modern WordPress plugin for live filtering, querying, and displaying posts similar to WP Gridbuilder. Supports posts, pages, and custom post types with taxonomy and custom field filters, AJAX updates, and Elementor integration.

## Installation
1. Copy the `brf-live-filters` directory to `wp-content/plugins/`.
2. Activate **BRF Live Filters** from the WordPress Plugins screen.
3. Navigate to **Live Filters** in the admin menu to create filter sets.

## Creating a Filter Set
- Add a new **Live Filter Set** and configure:
  - **Query**: choose post type, posts per page, sort order.
  - **Layout**: grid, list, or a custom PHP template within your theme.
  - **Filters**:
    - Taxonomies: comma-separated slugs (e.g., `category,post_tag,genre`).
    - Custom fields: one per line using `key|type|label|choices`. `type` can be `meta_text`, `meta_select`, or `meta_boolean`. Choices only apply to `meta_select` (comma-separated values).
 - The **Shortcode** panel in the sidebar shows the exact `[brf_live_filters id="123"]` snippet with the filter set ID for easy copying.

## Shortcode Usage
Insert the generated shortcode in content or templates:

```
[brf_live_filters id="123"]
```

You can override settings directly in the shortcode to let editors choose the filtering context without editing the filter set:

```
[brf_live_filters id="123" post_type="event" taxonomies="category,post_tag" meta_fields="color|meta_select|Color|red,blue" posts_per_page="9" orderby="title" order="ASC"]
```

Supported override attributes:
- `post_type`, `posts_per_page`, `orderby`, `order`, `layout`, `template`
- `taxonomies`: comma-separated list (e.g., `category,genre`)
- `meta_fields`: one-per-line string using `key|type|label|choices` (same as the admin field definition)

## Elementor Integration
Use the **BRF Live Filter** widget in Elementor:
- Select a filter set.
- Optionally override the template (grid or list) and set a custom wrapper class.
- The widget renders the same output as the shortcode and supports live preview inside Elementor.

## Template Overrides
The plugin ships with default templates:
- `brf-live-filters/templates/grid.php`
- `brf-live-filters/templates/list.php`

To override, copy a template into your theme at `your-theme/brf-live-filters/` and edit it there. You can also reference a custom template path from the **Layout** meta box when editing a filter set.

## Notes
- AJAX requests are secured with nonces and capabilities.
- Queries are cached via WordPress transients and automatically purged when posts or terms are updated.
- Frontend assets are lightweight vanilla JS and scoped CSS for easy styling.
