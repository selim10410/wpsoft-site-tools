=== WPSoft Site Tools ===
Version: 3.3
Author: WPSoft

WPSoft Site Tools; Elementor uyumlu header/footer builder, Template Library,
mega menu, blog/archive/single templates, global design system and advanced
WPSoft widgets provides.

Main modules:
- Header Builder
- Footer Builder
- Floating / Boxed Header
- Transparent / Sticky Header
- Mega Menu
- Template Library
- Elementor Widget Collection
- Blog Archive / Single Templates
- Global Design System
- Display Conditions
- License & Native WordPress Updater

Production notes:
- Development audit/report files are not included in production builds.
- Header button colors are managed per-button from the live Inspector.
- Global Design remains the fallback for components without a local override.
- Legacy stored settings remain supported for backward compatibility.

Package root must remain:
wpsoft-site-tools/
  wpsoft-site-tools.php
  includes/
  assets/
  templates/

Motion System 1.0:
- Central entrance animations
- Repeat / viewport threshold
- Stagger child animations
- Desktop / tablet / mobile disable controls
- Hover / parallax / mouse-follow motion
- Reduced-motion accessibility

Full Page Quality Tour:
- Signature full pages use curated Motion System presets.
- Hero motion remains subtle; card collections can use stagger.
- Page sections keep responsive spacing and device-safe layouts.

Sector Page Conversion:
- Legacy Widget Collection full pages converted to coherent real-sector templates.
- Motion presets are curated per section instead of applied uniformly.

Signature Preset:
- WPSoft Signature Hızlı Tasarım Preseti now uses a clean white widget canvas by default.

Global Design 3.2:
- Separate page background and component surface tokens.
- Opt-in page background application; Header/Footer unaffected.
- Fixed mobile grid override conflicts.
- H5/H6 now inherit global heading typography.
- Form/surface fields participate in live preview.
- Fixed malformed nested Global Design card markup.

Widget Global Design Bridge 4.0:
- Global surface/radius/shadow tokens now only apply when Global Design is enabled.
- Added Surface Alt / Primary Soft, spacing rhythm and button token controls.
- Local Surface is clearly treated as a widget-only override.
- Global-off widgets no longer receive opt-in token surfaces/radius/shadows.

Header Quick Start 1.0:
- Minimal / Corporate / Centered / Transparent now apply real saved settings.
- Current restores the Header Builder opening-state snapshot.
- Live preview and frontend use the same preset identity.
- Fixed later Builder 3.x calls that could not access applyStyles.

Header Quick Start 1.1:
- Added Floating Boxed preset with automatic side gutters, boxed surface, radius, border and scroll shadow.

Global Quick Design 1.0:
- Widget Hızlı Tasarım now includes Global Tasarımı Takip Et.
- Global Design includes a Widget Hızlı Tasarım selector with automatic preset mapping.
- Global style presets now also set grid gap, section spacing, spacing scale and button dimensions.
- Quick presets normalize component gaps, card radius, shadow and service-card padding responsively.

Header Floating Boxed Mobile Fix:
- Floating Boxed no longer forces boxed/floating layout on mobile.
- Mobile keeps the normal full-width header unless header_boxed_mobile is enabled.
- Quick Start preserves the existing Mobile Floating / Boxed preference.

Template Global Design Contract:
- All newly inserted WPSoft Template Library widgets default to Global Tasarımı Takip Et.
- Explicit local/template overrides remain supported.
- WPSoft Signature and Global->Signature now render as flat white with no ambient cast shadow.

Independent Inner Pages:
- Added About, Services, Contact, Team, Portfolio, FAQ, Pricing and Careers page templates.
- Each page uses a distinct composition and Global Design-following WPSoft widgets.
- Motion presets are curated per section.

Independent Inner Pages Phase 2:
- Added References, Service Detail, Project Detail, Blog Archive, Blog Detail, Legal/KVKK, 404 and Coming Soon templates.

Gallery Zoom 2.0 Navigation:
- Lightbox now behaves as a real gallery with previous/next controls, counter, click-to-next, swipe and keyboard navigation.
- Close button is always visible; background click and Escape still close.
- Adjacent images are preloaded for smoother navigation.

Carousel Quality Pass 1:
- Image Carousel and Card Carousel gain mouse drag, peek and progress controls.
- Services Carousel is now included in the actual shared carousel JS initializer.
- Shared progress, keyboard, swipe and drag state are synchronized.

Carousel Quality Pass 2:
- Team Carousel Pro gains real carousel mode, responsive visible count, peek, arrows, progress, mouse drag and swipe.
- Testimonial Slider gains optional autoplay, dots and progress with pause-on-hover/focus.
- Content Slider gains mouse drag and progress indicator while preserving autoplay, dots, keyboard and swipe.

Carousel Quality Pass 3:
- Hero Slider gains progress, counter, mouse drag and Fade/Slide/Soft Zoom transition modes.
- Mobile controls can be placed at the bottom or on image sides.
- Autoplay now pauses on focus/hover/visibility and restarts consistently after manual navigation.
- Added Home/End keyboard navigation and reduced-motion support.

Media Height Fix:
- Media height controls now apply to both wrapper and actual image/video elements.
- Added missing responsive media-height controls to Hero Commerce, Hero SaaS and Hero Split Modern.
- Fixed Image + Text, Service Cards, Story Cards and Media Card height propagation.

Responsive Media Position:
- Added Left / Center / Right / Custom media positioning to Hero and key media widgets.
- Custom mode provides responsive X and Y focal-point sliders.
- Positioning works independently on desktop, tablet and mobile.

Image Slider:
- Added a pure image-only slider widget with no text or CTA.
- Responsive image height (desktop/tablet/mobile), cover/contain, image position, radius, autoplay, arrows, dots, progress, swipe and mouse drag.

Elementor Header/Footer Hover Fix:
- Header and footer text no longer turns blue when hovering links in Elementor editor mode.
- Frontend hover/active behavior is unchanged.
- Header CTA and footer accent icons preserve their intended colors.

Footer Links 2.1:
- Added responsive Vertical / Horizontal layout.
- Horizontal mode supports wrap or single-line behavior.
- Alignment now controls horizontal link distribution.
- Vertical column control remains available only for vertical layout.

Footer Links 2.2:
- Added icon visibility/position controls, hover styles, separators and active-link styles.
- Added link/hover/active colors, icon size and horizontal padding controls.
- Optional external links can open in a new tab automatically.

Footer Links 2.2 Horizontal Fix:
- Vertical/Horizontal now outputs display:grid/flex directly through Elementor responsive CSS.
- Fixed horizontal mode not activating because previous CSS tried to detect generated values from inline style attributes.
- Wrap, responsive layout and alignment now use direct Elementor CSS properties.

Footer Brand 2.1:
- Added show/hide controls for description, phone, email and contact icons.
- Added responsive horizontal/vertical contact layout and alignment.
- Added description width, contact gap, icon box size and contact hover colors.

Footer Social 2.1:
- Added responsive horizontal/vertical layout and wrap controls.
- Added icon shape, hover styles, custom icon/background/border colors and icon/text sizing.
- Optional platform brand colors for Instagram, LinkedIn, YouTube, Facebook, X/Twitter, TikTok and WhatsApp.

Image Slider Links:
- Added Görseller + Linkler repeater with independent URL per image.
- Supports new-tab and nofollow settings per image.
- Legacy Gallery field remains fully supported; linked repeater takes priority when populated.

Image Slider Cleanup:
- Removed the old Gallery field and its legacy render path.
- Image Slider now uses one unified Görseller repeater only.
- Each image retains optional URL, new-tab, nofollow and alt-text controls.

Image Slider Controls 2.0:
- Added Glass, Solid, Outline, Minimal and Square arrow button styles.
- Arrow button size, icon size and side offsets are responsive for desktop/tablet/mobile.
- Added separate arrow normal/hover colors, backgrounds and borders.
- Dots now support Dot, Pill, Line and Boxed styles with responsive size/gap/bottom spacing.
- Added dot, active-dot and dot-shell color controls with mobile overflow protection.

Mobile Header + Image Slider Fix:
- Floating Boxed mobile header now always shows the generated hamburger button/actions.
- Mobile logo/actions receive safe flex widths so the logo cannot push hamburger out of view.
- Image Slider default mobile height reduced to 240px and adaptive mobile height caps were added.
- Mobile arrows/dots scale down and remain inside the slide.

Mobile Hamburger Color Fix:
- Floating Boxed mobile hamburger is dark from first paint, not only after sticky/scrolled state.
- Hamburger lines now always inherit the explicit toggle color.
- Transparent mobile headers may remain light before scroll and become dark on solid sticky surface.

Header Inspector Shadow:
- Added Normal · Gölgesiz to Floating/Boxed shadow controls.
- Explicit shadow style is now rendered as data-wpst-shadow-style and overrides preset hardcoded shadow.
- Normal remains shadow-free before and after sticky/scrolled state.

Global Header Shadow Style:
- Moved shadow control out of Floating/Boxed into general Header Behaviour.
- Added Normal / Soft / Medium / Strong for all header layouts.
- Floating/Boxed now mirrors the global shadow setting.
- Added Inspector pointer-events/z-index fix so the select remains clickable.

Floating/Boxed Shadow Cleanup:
- Removed diffuse shadow from Floating/Boxed header in normal and scrolled states.
- Preserved floating width, side/top spacing, border, background and border radius.

Global Page Background Fix:
- Page background now reaches body plus common WordPress/theme content wrappers.
- Elementor page/document roots follow the global page background while locally styled Elementor sections remain independent.
- Header/Footer surfaces remain independent.

Active Menu Shadow Cleanup:
- Kept one soft active-menu shadow.
- Removed extra glow/backdrop/filter halo under the active item.

Header Inspector Cleanup Phase 1:
- Removed duplicate Header Builder 3.0 preset cards from Inspector; quick-start remains the visible preset entry.
- Removed legacy header-wide Background/Text/Container/Padding controls from the Header UI; row-level controls are now the visible source.
- Removed duplicate Normal/Scroll height controls from Menu section; Main Header row heights remain visible.
- Legacy values remain as hidden compatibility fields, so existing sites are not reset when settings are saved.

Header Inspector Cleanup Phase 2:
- Grouped Sticky/Scroll behavior separately from Transparency/Glass and Shadow controls.
- Normal/Transparent/Sticky preview buttons are now preview-only and no longer mutate saved settings.
- Scroll logo controls moved under Transparency & Glass.
- Shadow controls grouped into one compact section.

Header Menu Interactions:
- Consolidated menu gap, hover and active states into Menu Interactions.
- Active styles: None, Background, Soft Shadow, Border, Fade.
- Added Soft/Medium/Strong active shadow intensity.
- Active shadow uses one shadow layer and explicitly disables filter/backdrop/text glow.
- Scroll/Transparent colors moved to their own compact section.

Header Logo & Responsive Cleanup:
- Normal Logo and Scroll Sonrası Logo remain separate media selections.
- Both are grouped under one Logo & Responsive section with desktop/mobile sizes.
- Duplicate scroll/mobile logo size controls were removed from other panels.

Floating / Boxed Cleanup:
- Simplified Header Appearance to Normal / Floating Boxed.
- Boxed-only fields are hidden unless Floating / Boxed is selected.
- Kept only max width, top/side spacing, radius, background, border and mobile floating toggle.
- Shadow, sticky, transparent and row-size settings remain in their own general sections.

Header Rows Cleanup:
- Main Header is now clearly marked as the permanent primary row.
- Top Bar and Bottom Bar have independent enable/disable controls.
- Optional disabled rows no longer render on frontend.
- All rows use the same Height / Appearance / Container / Device Visibility structure.
- Sticky row selectors are disabled automatically when their optional row is disabled.

Header Inspector Quality Pass:
- Simplified section names and helper copy.
- Only Logo & Responsive, Main Header, and Sticky & Scroll are open by default.
- Added compact hierarchy and spacing polish across Inspector groups.
- Header Inspector accordion open/closed state is remembered during the current admin session.

Image Slider Touch / Drag:
- Added explicit Dokunmatik Kaydırma toggle for mobile/tablet/touch screens.
- Desktop Mouse ile Sürükle remains separately controllable and works directly on linked images.
- Prevents accidental image-link navigation after swipe/drag.
- Added grab/grabbing cursor and touch-action behavior without blocking vertical page scrolling.

Header CSS Consolidation · Phase 1:
- Added wpst-06-header-canonical.css as the final Header state authority.
- Moved late Floating/Boxed, mobile hamburger, shadow and active-menu patch blocks out of three older CSS modules.
- Floating/Boxed layout geometry remains unchanged; diffuse header shadow stays disabled.
- Active menu shadow now has one canonical layer with Soft/Medium/Strong support and no glow filters.
- Mobile hamburger visibility/color rules are centralized for normal, transparent and boxed states.

Header CSS Consolidation · Phase 2:
- Builder Rows now owns desktop menu structure/dropdowns only; active menu visuals moved to canonical Header CSS.
- Removed deprecated underline active-state CSS from the active runtime layer.
- Removed old Floating/Boxed style-attribute shadow variants from Modern Compat.
- Canonical Header CSS now owns None / Background(Pill) / Fade / Shadow / Border active states.
- Boxed transparent contrast rules keep color/background only and no longer fight canonical shadow/filter rules.

Header CSS Consolidation · Phase 3:
- Transparent / Sticky / Scroll presentation moved from PHP inline CSS and legacy modules into Header Canonical.
- Added explicit data attributes for scroll-solid, blur, scroll-shadow and shrink state.
- Added canonical CSS variables for scroll blur and desktop scroll-logo sizing.
- Builder Rows retains sticky-row visibility structure, but no longer paints transparent/scrolled visual states.
- Removed legacy Transparent Contrast block and old transparent active-menu glow rules from Modern Compat.
- Admin-bar offsets, transparent overlay, glass modes, scrolled surface, dual-logo switch and row scrolled heights now have one final authority.

Header CSS Consolidation · Phase 4 / Final:
- Unified mobile breakpoint state in one frontend.js controller.
- Removed the second legacy Header Builder responsive resize listener.
- is-wpst-mobile and wpst-device-* classes now update together from the configured Header breakpoint.
- Moved mobile normal/scroll logo sizing from Builder Rows to Header Canonical.
- Replaced historical max-width:900 mega-menu mobile hiding with configured-breakpoint class logic.
- Added canonical mobile visibility safeguards so custom breakpoints do not collide with old fixed media queries.

Scroll Logo Hotfix (v3.3.18.21.13 base):
- CSS-only fix; no admin save/sanitize flow changed.
- Desktop scroll logo width/height now override generic image constraints.
- Mobile scroll logo targets the real mobile-brand/q-logo markup too.
- Removed legacy 170x42 mobile hard cap that blocked configured values.

Hero Quality Hotfix:
- Added shared responsive Hero Content Alignment that centers text, action rows and direct buttons together.
- Fixed Commerce Centered and Hospitality Centered layouts so content is genuinely centered.
- Hero buttons are shadow-free by default; old hover/glow shadow is removed.
- Added Hero button styles: Default, Solid, Outline, Soft, Glass, Dark, Minimal.
- Added button radius, optional shadow, responsive height/gap and local hover/color controls.

Hero Control Reliability:
- Hero content alignment now controls the real copy flex container, action rows and direct buttons.
- Added responsive vertical content position (Top / Center / Bottom).
- Center alignment now centers the complete content block, not just text.
- Hardened Hero color selectors against preset/fallback CSS conflicts.
- Slider, Split Modern, SaaS, Commerce, Hospitality and Spotlight color controls now apply to their real rendered elements with reliable priority.

Hero Slider Controls:
- Added Arrow Color.
- Added Arrow Hover Color.
- Arrow SVG inherits the selected foreground color reliably.
- Existing arrow background and hover-background controls remain unchanged.

Hero Slider Arrow Color Runtime Fix:
- Arrow Color bridges to shared control color variable.
- Visible pseudo-arrow and SVG both inherit selected color.
- Hover foreground color applies with final priority.

Reviews Carousel:
- Added WPSoft Reviews Carousel widget with responsive 1-4 visible cards.
- Touch swipe, desktop mouse drag, autoplay, pause-on-hover, arrows and dots.
- Rating, avatar, name, role/company, source and verified badge per review.
- Modern, Minimal, Soft, Dark and Glass card styles.
- Fully controllable card colors, stars, arrows and dots.

Services Grid Fix:
- Icon visibility now uses an explicit runtime flag.
- Added CSS safety so Iconları Göster = Hayır always hides service icons.

Services Grid Icon Toggle Final Fix:
- Iconları Göster now controls both the main service icon and the action arrow/icon.
- Added explicit has-icons/no-icons wrapper state.
- Added defensive CSS for old Elementor cached markup.
- No icon spacing remains when icons are disabled.

Services Grid Action Refinement:
- "Hizmeti İncele" action is centered by default.
- Added Left / Center / Right action alignment control.
- Added Minimal Line, Soft, Pill and Plain action styles.
- Hover movement is subtle and shadow-free.

Safe Local Color Reliability Hotfix:
- Reverted the broad typography authority approach from v3.3.18.21.13.9.
- Font family, font size, line-height, letter-spacing and existing widget typography remain exactly on the v3.3.18.21.13.8 behavior.
- Strengthened 30 direct widget color controls only.
- No shared typography dimensions are forced globally.

Footer Links 2.0 Quality Pass:
- Added optional title visibility plus dedicated title typography/color/spacing controls.
- Added dedicated link typography.
- Added link and hover background colors, separator color, badge colors.
- Added vertical padding, radius and icon/text gap controls.
- Added icon visibility modes: Always / Hover / Subtle.
- Explicit no-icons render state now overrides Footer template CSS reliably.
- Improved active/hover/separator behavior and horizontal responsive robustness.

Footer Links 2.0 · Link Quality:
- Each repeater URL now honors Elementor is_external / nofollow / custom attributes.
- Legacy global external-new-tab switch remains as a fallback.
- Added Active Link Detection: Exact or Parent Path.
- Parent Path keeps /services active on /services/web-design style child URLs.
- Current link outputs aria-current="page" for accessibility.
- _blank links automatically receive noopener noreferrer.

Video Background Pro Alignment Fix:
- Added responsive Content Alignment control (Left / Center / Right).
- Center now aligns the complete copy composition, not only text glyphs.
- Constrained paragraph width centers with auto margins.
- CTA button centers with the copy.
- Existing Center content-position variants now retain true horizontal centering.

Local Style Button / Typography Reliability:
- Expanded shared action selector to Video Background Pro and other WPSoft CTA structures.
- Local button text/background/hover colors now receive final priority.
- Local button font-size, line-height, letter-spacing, padding and radius now reliably apply.
- Added safe custom-property bridge: empty controls never change widget defaults.
- Removed generic Signature typography !important blockers so Elementor Group Typography can work.
- Local design mode no longer receives Global Design button-color forcing.

Header Navigation Migration:
- All generated WPSoft Header presets now use WPSoft Navigation instead of Elementor nav-menu.
- Header styles receive sensible Navigation presets (Modern / Minimal / Floating / Glass / Clean).
- Existing WPSoft-managed Header templates are migrated once from nav-menu to wpsoft-navigation.
- Selected legacy menu ID is preserved when available.
- WPSoft Navigation is explicitly listed in the WPSoft Navigation widget category.
