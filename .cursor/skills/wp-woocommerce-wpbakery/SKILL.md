---
name: wp-woocommerce-wpbakery
description: >-
  Builds and customizes WordPress + WooCommerce sites that use WPBakery Page
  Builder (js_composer), especially the Sober theme with sober-child overrides.
  Use when editing themes, WooCommerce templates, WPBakery/VC shortcodes,
  vc_templates, product/cart/checkout UI, or when the user mentions WPBakery,
  Visual Composer, js_composer, WooCommerce, or Sober.
---

# WordPress / WooCommerce + WPBakery

## Scope

Project stack:

| Layer | Location | Editable? |
|-------|----------|-----------|
| Child theme | `wp-content/themes/sober-child/` | **Yes — only place to write** |
| Parent theme | `wp-content/themes/sober/` | Read-only reference |
| WPBakery | `wp-content/plugins/js_composer/` | Read-only |
| WooCommerce | `wp-content/plugins/woocommerce/` | Read-only |
| Sober VC elements | `wp-content/plugins/sober-addons/` | Read-only |

Also follow the personal `wp-coding-standards` skill (WPCS, sanitize/escape, nonces, capabilities).

## Workflow

1. Confirm the change belongs in `sober-child`.
2. Locate the source template/hook in parent or plugin (**read only**).
3. Implement via child override, hook, or enqueue — never patch parent/plugins.
4. Keep markup/class names compatible with existing Sober / WPBakery / WooCommerce CSS when possible.

## Child theme structure

```
sober-child/
├── style.css                 # Theme header only (Template: sober)
├── functions.php             # Enqueues, hooks, filters
├── package.json              # Asset build scripts
├── src/
│   ├── scss/                 # Source SCSS (edit here)
│   │   └── main.scss
│   └── js/                   # Source JS (edit here)
│       └── main.js
├── assets/                   # Compiled output (do not hand-edit)
│   ├── css/main.css
│   └── js/main.js
├── woocommerce/              # Woo template overrides
├── vc_templates/             # WPBakery shortcode template overrides
├── template-parts/           # Copied parent parts when needed
└── *.php                     # Copied parent templates (header.php, etc.)
```

### Asset build

Edit only `src/`. Compile into `assets/`:

```bash
cd wp-content/themes/sober-child
npm install
npm run watch    # development (sass + esbuild)
npm run build    # expanded CSS + bundled JS
npm run prod     # compressed/minified for deploy
```

- SCSS: `src/scss/**` → `assets/css/` (entry: `main.scss`)
- JS: `src/js/main.js` → `assets/js/main.js` (esbuild bundle)
- Enqueued from `functions.php` as `sober-child-main` (CSS depends on `sober-child` style.css; `filemtime` for versions)
- Never edit compiled files under `assets/` by hand
- Keep `style.css` for the theme header; put real styles in `src/scss/`

Enqueue priority remains `20` on `wp_enqueue_scripts`.
## WooCommerce

- Override templates in `sober-child/woocommerce/...` using the same relative path as WooCommerce or the parent theme override.
- Prefer `remove_action` / `add_action` / filters on Woo hooks from `functions.php` when a full template copy is unnecessary.
- Parent Woo logic lives in `sober/inc/class-sober-wc.php` — extend via hooks from the child, do not edit that class.
- Escape all output; use Woo helpers (`wc_price()`, `wc_get_template()`, etc.) where appropriate.
- Test critical flows after UI changes: shop, single product, cart, checkout, account.

### Common override targets

- Loop: `woocommerce/content-product.php`, `loop/price.php`, `loop/result-count.php`
- Single: `single-product/*`, `content-product-quickview.php`
- Cart/checkout: `cart/cart.php`, `checkout/form-checkout.php`, `checkout/review-order.php`
- Global: `global/quantity-input.php`, notices

## WPBakery (js_composer)

- Plugin slug/path: `js_composer`; APIs use `vc_*` / `wpb_*` prefixes.
- Parent overrides: `sober/vc_templates/` (e.g. `sober_banner.php`, `vc_custom_heading.php`).
- Child overrides: copy into `sober-child/vc_templates/{shortcode}.php`.
- Custom Sober elements are registered in `sober-addons` via `vc_map()` (`includes/class-sober-vc.php`). To customize behavior/markup, override the matching `vc_templates` file in the **child theme**, or add filters from the child — do not edit `sober-addons`.
- When editing VC templates:
  - Keep `if ( ! defined( 'ABSPATH' ) ) { die( '-1' ); }`
  - Use `vc_map_get_attributes()`, `vc_build_link()`, `wpb_getImageBySize()` as in existing templates
  - Escape attributes/URLs/HTML appropriately

### Adding a custom VC element from the child

Only if the user asks for a new shortcode:

1. Register with `vc_map()` on `vc_before_init` from child `functions.php`.
2. `add_shortcode()` for the front end.
3. Optional: provide `sober-child/vc_templates/{base}.php` for the view.
4. Prefix shortcode base uniquely (e.g. `sober_child_...`) to avoid clashes.

## Hooks over copies

Prefer this order:

1. CSS/JS via `src/scss` + `src/js` (then `npm run build` / `watch`)
2. `add_filter` / `add_action` / `remove_action` in child `functions.php`
3. Small template part override
4. Full template copy (last resort; harder to maintain on theme updates)
## Security & i18n

- Validate input, sanitize on save, escape on output.
- Nonce + `current_user_can()` for privileged/AJAX actions.
- Text domain: use a child-specific domain for new strings (e.g. `sober-child`); keep existing `sober` domain only when overriding copied parent strings intentionally.

## Checklist

- [ ] Only `sober-child` files changed
- [ ] Parent/plugin used as reference only
- [ ] Woo/VC template paths match expected override locations
- [ ] Styles/scripts written in `src/`, compiled to `assets/`, enqueued from `functions.php`
- [ ] Output escaped; hooks secured where needed
- [ ] No edits to `sober`, `js_composer`, `woocommerce`, or `sober-addons`
