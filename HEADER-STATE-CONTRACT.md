# WPSoft Header State Contract 4.0

Canonical owner: `assets/css/frontend/wpst-06-header-canonical.css`

States:
- Normal: Inspector row-local appearance.
- Transparent: `data-wpst-transparent="1"` before scroll.
- Scrolled solid: `data-wpst-scroll-surface="solid"`.
- Scrolled transparent: `data-wpst-scroll-surface="transparent"`.

Rules:
1. Surface state belongs only to canonical section 0.
2. Menu active/hover belongs to canonical section 2.
3. Mobile layout belongs to canonical section 3 and mobile module.
4. Mega menu geometry stays in `mega-menu.css`.
5. Do not add `#fff` fallback to scroll background.
6. Transparent scrolled background rules must not change menu/text colors.
