### 📤 External Sharing & Developer Tools
* **Feature**: Introduced the **External Share API** (`/share`), allowing third-party websites to integrate a "Share on MYADS" button.
* **Add**: Added support for the `text` query parameter on `/share`, which automatically pre-fills the community post composer with the provided content (including links, hashtags, and mentions).
* **Feature**: Added a dedicated **Developer Documentation** page (`/developer`) with technical guides, HTML/JS code examples, and best practices for site owners to integrate MYADS sharing.
* **i18n**: Fully localized the sharing interface and developer documentation across all **9 supported languages**, including specialized Arabic terminology for "External Share API".
* **Improvement**: Enhanced the core post composer (`add_post.blade.php`) with a fallback mechanism that prioritizes URL parameters when no old form input is present.


### 🎨 Admin Panel Architecture (Super-Independence)
* **Feature**: Decoupled the **Admin Panel Templates** from the primary public theme. Admin templates now reside in a dedicated root-level `admin_themes/` directory, ensuring that future admin panel redesigns do not affect the public site design.
* **Architecture**: Introduced a new `admin::` view namespace in `AppServiceProvider` that dynamically resolves templates based on the active admin theme stored in the `options` table.
* **Add**: Added a dedicated **Admin Theme** setting in `/admin/settings`, allowing administrators to switch admin panel designs independently from the main site theme.
* **Helper**: Introduced the `admin_asset()` helper to manage admin-specific CSS, JS, and image assets, ensuring they are served from the independent `admin_themes/` repository.
* **Cleanup**: Migrated 39+ controllers to use the new `admin::` namespace and purged legacy admin templates from the default public theme, resulting in a cleaner and more modular codebase.

### 🔌 Plugins Management
* **Feature**: Introduced a new **Plugin Details Modal** in the admin panel (`/admin/plugins`), allowing administrators to view comprehensive plugin documentation and metadata without leaving the dashboard.
* **Add**: Support for local documentation files:
    * `README.md`: Rendered as the main plugin description.
    * `changelogs.md`: Displayed in a dedicated "Changelog" tab.
    * `screenshots.md`: Displayed in a "Screenshots" tab.
* **Improvement**: Dynamic tab visibility: The "Changelog" and "Screenshots" tabs are now automatically hidden if the corresponding files are missing.
* **Architecture**: Integrated `marked.js` and `DOMPurify` for secure client-side Markdown rendering of local plugin documentation.
* **i18n**: Added new translation keys for plugin documentation labels (`screenshots`, `requires_myads`, `adstn_page`, `plugin_website`) across all supported languages.

### 🌍 Internationalization & Localization
* **Architecture**: Relocated the **Default Language** setting from the General Settings page to the dedicated **Languages** management interface, providing a more contextual workflow.
* **Feature**: Added a "Set as Default" one-click action for all installed languages, allowing administrators to switch the site's primary locale without manually entering ISO codes.
* **UI**: Introduced a visual "Default" indicator (Star badge) in the languages table to clearly identify the active site-wide locale.
* **i18n**: Added new translation strings for `default`, `set_as_default`, and success notifications across shared language packs.

### 🧹 Feature Purge & Cleanup
* **Removal**: Permanently removed the **Educational Links** (روابط تعليمية) feature from the entire platform to streamline the user and admin experience.
* **Cleanup**: Purged `e_links` logic and help-center icon triggers from the User Dashboard, Forum, Visit Exchange, and Ads management screens.
* **Database**: Updated the `Setting` model to exclude legacy `e_links` data while maintaining schema compatibility.
### 🛡️ Admin Management & Fixes
* **Fix**: Resolved a critical synchronization bug in the **User Edit** form (`/admin/users/{id}/edit`) where updating the "User Slug" would unintentionally overwrite the "Username".
* **Improvement**: Decoupled **Username** and **User Slug** updates in the admin panel, allowing administrators to manage login identities and profile handles independently.
* **Security**: Added robust unique validation for `username` and `email` during admin-side user updates to prevent accidental account conflicts.
* **Fix**: Resolved `500 Internal Server Error` in the **Arabic Fixer** plugin after the admin template decoupling by correcting the layout namespace reference.
* **Fix**: Corrected database table name mapping in the Arabic Fixer service to resolve failed character-fix operations on forum comments.

### ⚡ Performance & Core Web Vitals
* **Optimization**: Consolidated Google Fonts into a single HTTP/2 request and purged redundant `@import` rules to reduce font-loading latency.
* **Optimization**: Implemented **Critical CSS inlining** for the header and layout grid, improving First Contentful Paint (FCP).
* **Speed**: Leveraged the `media="print"` technique for non-critical stylesheets to prevent render-blocking and unblock the browser's main thread.
* **Payload**: Reduced the initial page weight by minifying `styles.min.css` and critical inline theme-selection scripts in `master.blade.php`.
* **Deferral**: Migrated **jQuery** and secondary UI plugins to the document footer with `defer` attributes, ensuring the visual page renders before heavy script execution.
* **Stability (CLS)**: Eliminated **Cumulative Layout Shift** (CLS) by enforcing explicit dimensions for the header search bar, SVG icons, and site logos in both HTML and CSS.
* **Architecture**: Added persistent resource hints (`preconnect`, `dns-prefetch`) for FontAwesome and DataTables CDNs to accelerate external asset resolution.
* **Fix**: Resolved missing image dimensions for `Alert-icon.png` in ad serving templates, ensuring stable ad delivery.
* **Fix**: Resolved a critical layout regression where misplaced script tags caused raw JavaScript to render as text and broke the theme toggle functionality.

### 🛡️ Security & Privacy
* **Feature**: Implemented strict enforcement of **Public Member IDs** for user profiles. When enabled in `/admin/security`, access to profiles via internal numeric IDs (e.g., `/u/1` or `/u/id/1`) is automatically blocked with a **404 Not Found** error, ensuring that internal identifiers are never exposed to the public.
* **Feature**: Implemented strict enforcement of **Encrypted Conversation Keys** for private messages. When "Private message encryption" is enabled, access to conversations via numeric message IDs (e.g., `/messages/123`) is strictly disabled, forcing the use of secure, system-generated hash keys to prevent ID-enumeration and metadata discovery.

### 📢 Site Ads & Inventory Reliability
* **Feature**: Added **Bulk Deletion** capabilities for Site Ads and Visits (`/admin/banners`, `/admin/links`, `/admin/smart-ads`, and `/admin/visits`), allowing administrators to select multiple records and permanently delete them in a single action.
* **Improvement**: Integrated a reactive deletion confirmation modal across all ad inventories that dynamically displays the total count of selected items to prevent accidental bulk deletions.
* **Improvement**: Refactored the underlying deletion logic for banners, text ads, smart ads, and visits to ensure that the system automatically sends an unread, localized system notification to the ad owner whenever the administration deletes their ad (during both individual and bulk operations).
* **Fix**: Resolved `500 Internal Server Error` and potential data corruption on the **Site Ads** management page (`/admin/site-ads`) when using the "Save All" feature.
* **Fix**: Standardized individual ad saving logic to prevent cross-contamination between different ad slots (e.g., Header vs Footer) during update operations.
* **Stability**: Implemented strict non-null enforcement for ad code fields, preventing SQL integrity violations when clearing ad content.

### Ads Settings & Branding
* **Add**: Added a dedicated `/admin/ads/settings` page for shared ad-delivery configuration, including the ad brand name, banner repeat window, and Smart Ads points divisor.
* **Change**: Ad branding for banner, link, and smart ads now reads from database-backed ad settings instead of `APP_NAME` in `.env`, with graceful fallback to the site title and runtime config when needed.
* **Change**: Removed banner repeat-window and Smart Ads divisor controls from `/admin/settings` so general site settings stay focused on site-wide metadata.
* **Fix**: Resolved a `500 Internal Server Error` on `/b_code` by stabilizing the banner code page variable bootstrapping after the new ad-branding integration.

### 👥 User Deletion & Data Integrity
* **Feature**: Added **Bulk User Deletion** to the admin users management page (`/admin/users`), allowing administrators to select multiple members and permanently delete them in a single action.
* **Improvement**: Added a reactive deletion confirmation modal that dynamically displays the total count of selected users to prevent accidental bulk deletions.
* **Fix**: Resolved a critical SQL `Column not found` error (`uid`, `rid`) in the `deleteUser()` action that previously triggered a `500 Internal Server Error` during both individual and bulk user deletions.
* **Fix**: Resolved `500 Internal Server Error` on `/u/{username}/followers` and `/u/{username}/following` pages when a deleted member still had follow records in the database.
* **Fix**: Follower and following counts on profile pages no longer include deleted members, ensuring accurate statistics.
* **Fix**: The `deleteUser()` and bulk delete admin actions now perform full cleanup of orphaned data: follow/reaction records, user options (slug, social links, point history), notifications, statuses, and messages.
* **Security**: Enforced hard-coded protection logic inside the centralized `performUserDeletion` service to secure the super-admin account (ID 1) from being deleted during single or bulk operations.
* **Add**: Added a null-safety guard to the `user_preview_landscape` partial to gracefully skip rendering for deleted users as a defense-in-depth layer.
* **Add**: Added a new **Repair Orphaned Records** tool to the `/admin/maintenance` page, allowing administrators to clean up follow and reaction records linked to previously deleted members.
* **Add**: Added a new **Repair Orphaned Content** tool to the `/admin/maintenance` page, allowing administrators to clean up orphaned comments and reactions linked to deleted posts, forum topics, directory listings, store products, and order requests across all content types.
* **Add**: Added a new **Repair Orphaned Stats** tool to the `/admin/maintenance` page, allowing administrators to clean up orphaned view and click statistics from the `state` table for deleted ads.
* **i18n**: Added translation keys for the bulk deletion interface (`delete_selected`, `selected_users_count`, `bulk_delete_warning`) and orphaned records tools across all **9 supported languages**.

# v4.2.3
> **Corrective Release** — User slug synchronization, RTL post layout fixes, and UI consistency.

### 🔐 Users & Admin Management
* **Fix**: Resolved an issue where updating a member's slug (username) in the admin panel would break their public profile URL; the system now correctly synchronizes the `username` field in the `users` table with the unique `options` identifier.
* **Fix**: Corrected the slug update redirect in the admin panel to ensure administrators are returned to the correct user entry after a successful name change.

### 🎨 UI & RTL Modernization
* **Fix**: Overhauled the **RTL layout** for community activity posts and forum topics to ensure visual consistency in Arabic and Persian locales.
    * **Avatar Position**: Moved the user's avatar to the right side of the post header.
    * **Action Icons**: Relocated the settings dropdown (three dots) and the post-category sticker (globe, blog, gallery icons) to the left side.
    * **Padding & Spacing**: Implemented specialized padding rules to ensure post metadata and usernames correctly clear both the avatar and the action icons.
* **Architecture**: Introduced the `activity-post-card` hook across `wrapper.blade.php`, `forum/post.blade.php`, and `forum/image.blade.php` to centralize RTL layout logic and prevent layout drift in future updates.

### 🛡️ Core Stability
* **Fix**: Resolved layout clipping on forum topic pages in RTL mode where long titles could overlap with the absolutely positioned action icons.

### Admin Premium Refresh
* **Improvement**: Expanded the Duralux-aligned "premium admin" redesign across a wide set of management pages, including users, pages, ads monitoring, banners, links, smart ads, visits, knowledgebase, forum settings/moderators, emojis, products, menus, site ads, SEO, Security, Settings, Maintenance, Admins, Plugins, Themes, Reports, and category management screens.
* **Improvement**: Standardized admin pages around shared `admin-hero`, `admin-panel`, `admin-suite-nav`, `admin-control-grid`, `admin-action-tiles`, and `admin-form-workspace` patterns through the shared `admin-redesign.css` layer while preserving all existing routes, form contracts, filters, and AJAX flows.
* **Fix**: Added an explicit `admin_shell_header_mode` contract in the admin layout so pages with custom hero headers can suppress the lightweight shell header instead of rendering duplicated top sections.
* **Fix**: Migrated remaining legacy `page-header` screens and suite pages toward the new hero contract, improving placement on desktop/mobile and preventing awkward stacked headers inside the admin shell.

### Ads, Inventory & Moderation Polish
* **Fix**: Resolved dropdown clipping on shared `Filter` controls for `/admin/users`, `/admin/banners`, `/admin/links`, `/admin/smart-ads`, and `/admin/visits` so the full filter panel now renders correctly inside the redesigned admin hero.
* **Fix**: Resolved a `500 Internal Server Error` on `/admin/ads/posts` caused by an undefined status badge variable in the promoted-posts monitoring view.
* **Improvement**: Added stronger, high-contrast visual status badges for promoted-post campaign states on `/admin/ads/posts` so `active`, `paused`, `completed`, `expired`, and `budget_capped` campaigns are easier to scan against the admin background.
* **Add**: Added admin-side editing for visit ads directly from the `/admin/visits` table through an inline modal flow, preserving the existing update route and field contract.

### Translation Workspace & Localization
* **Improvement**: Rebuilt `/admin/languages/{id}/terms` into a dedicated translation workspace with a sticky search/filter toolbar, progress counters, accordion-based term cards, multiline auto-growing textareas, and a separate extra-keys section so long strings remain readable without horizontal expansion.
* **Add**: Added translation-workspace UI labels, counters, and status copy across all 9 supported language packs to support the new admin translation flow.
* **Fix**: Corrected mojibake/encoding issues in the Arabic and Persian language files for the newly added translation-workspace keys.

# v4.2.2
> **Corrective Release** - User Social Links, admin extensions redesign, stability fixes, and localization polish.

### ✍️ Content Editing Modernization
*   **Editor**: Replaced the legacy `sceditor` with `StackEdit` for all rich content editing fields in the following areas:
    *   **Admin News**: `/admin/news` (create and edit news articles).
    *   **Store Product Creation**: `/store/create` (main product body/description).
    *   **Store Product Update**: `/store/{name}/update` (new version description/release notes).
    *   **Admin Product Edit**: `/admin/products/{id}/edit` (main product body/description).
*   **Editor**: Enhanced public news display (`/news/{id}`) to now correctly render Markdown content using `marked.js` and `DOMPurify`, ensuring new StackEdit-generated content is beautifully displayed.
*   **Editor**: Enhanced store product display (`/store/{name}`) to correctly render Markdown for main product details (`o_valuer`) and product file descriptions (in versions tab) using `marked.js` and `DOMPurify`.

### ⚡ Inline Editing for Store
*   **Add**: Introduced inline editing functionality for the main product **Topic Body** directly from the `/store/{name}` public page.
    *   **Access**: Available to product publishers and site administrators.
    *   **Editor**: Utilizes `StackEdit` for a seamless Markdown editing experience.
    *   **UX**: Features AJAX-based saving with real-time feedback (`Saving...`, `Saved!`) without page reload.
    *   **UI**: "Edit Topic" button integrated into the product's action menu for quick access.
*   **Add**: Introduced inline editing functionality for the main product **Details (Description)** (`o_valuer`) directly from the `/store/{name}` public page.
    *   **Access**: Available to product publishers and site administrators.
    *   **Editor**: Utilizes `StackEdit` for a seamless Markdown editing experience.
    *   **UX**: Features AJAX-based saving with real-time feedback (`Saving...`, `Saved!`) without page reload.
    *   **UI**: "Edit Product" button integrated into the Details tab for direct content modification.
*   **Fix**: Improved topic retrieval logic in `StoreController` for the `/store/{name}` page to ensure the associated forum topic is correctly fetched, enabling the inline editing functionality where applicable.

### 🌍 Localization & i18n
*   **Add**: Added new translation keys (`saving`, `saved`, `topic_updated`) across all 9 supported languages to support the new inline editing features.

### 🔗 User Social Links
* **Add**: Introduced a new **Social Links** section in the user settings (`/settings/social`).
* **Add**: Support for **10 major platforms**: Facebook, Twitter (X), Vkontakte, LinkedIn, Instagram, YouTube, Threads, Reddit, GitHub, and **ADStn**.
* **Add**: Brand-specific icons and colors for every platform, including a high-fidelity integration for the ADStn community platform.
* **Add**: Smart URL normalization engine that clean-validates handles and URLs into standardized profile links.
* **Improvement**: Integrated the social links widget into the member profile hub, positioned elegantly below the badges section.
* **i18n**: Fully localized platform names and social settings labels across all **9 supported languages**.

### 🎨 UI & UX Modernization
* **Improvement**: Standardized the **Floating Label** behavior across the **Social Links** settings (`/settings/social`), **Registration** (Verification Code), and **Order Request** creation pages (`/orders/create`) by enforcing the `active` class on themed form inputs for a more consistent and polished UI.
* **Improvement**: Overhauled the **Forum Categories** admin interface (`/admin/forum/categories`) with a "Superdesign" aesthetic, featuring glassmorphic headers, modern card layouts, and refined CRUD modals.
* **Improvement**: Modernized the **Directory Categories** admin interface (`/admin/directory/categories`) with a matching glassmorphic design, hierarchical visual cues, and improved table layouts.
*   **Improvement**: Redesigned the **Plugins** admin page (`/admin/plugins`) into a modern extension hub with a hero header, live stats, responsive management cards, reusable changelog/delete modals, and lazy-loaded previews.
*   **Improvement**: Enhanced the **Plugin Deletion Workflow** by implementing a client-side safety check that intercepts the deletion modal for active plugins. It now displays a localized warning and hides the delete action until the plugin is deactivated, ensuring a safer management experience across all **9 supported languages**.
* **Improvement**: Redesigned the **Themes** admin page (`/admin/themes`) with the same Duralux-aligned visual system, including a hero header, status chips, richer preview cards, clearer activation/update actions, and responsive gallery behavior.
* **Improvement**: Redesigned the **Reports** admin page (`/admin/reports`) into a dedicated moderation hub with a hero header, moderation stats, modern report cards, clearer reporter/target actions, and a responsive review queue while preserving the existing review and delete flows.
* **Improvement**: Enhanced the visual hierarchy of the **Add Category** buttons on management pages, using high-contrast "pop" colors (Warning/Primary) to distinguish them from the background.
* **Architecture**: Introduced shared admin partials for the new extension management shell and report-specific moderation styles so Plugins, Themes, and Reports stay visually consistent without duplicating heavy UI markup.
* **i18n**: Added missing translation keys for category lists, descriptions, placeholder text, plugin/theme management labels, report moderation copy, extension stats, modal content, and action feedback across all **9 supported languages**.

### 🛡️ Core Stability & Database
* **Fix**: Resolved `Integrity constraint violation` when creating or updating Forum and Directory categories without a description by enforcing default empty strings in `AdminController`.
* **Fix**: Created a repair migration to make `txt` and `metakeywords` columns `nullable` for `f_cat` and `directory_cat` tables.
* **Refactor**: Simplified `storeNews` logic in the Admin panel to remove redundant input handling.
* **Fix**: Standardized translation key usage in `deleteDirectoryCategory`.
* **Fix**: Corrected the plugin deletion flow so success is only reported when deletion actually returns `true`; active or blocked plugins now surface a translated error message instead of a false success notice.
* **Fix**: Resolved the **Reported Content Removed** false state on `/admin/reports` for **Order Requests** and reported request publishers by unifying report type handling for both current and legacy report flows (`6/99` and `701/702`).
* **Improvement**: Refactored admin report target resolution into a centralized controller path, reducing scattered per-row lookup logic and improving consistency for Directory, Forum, News, Store, Knowledgebase, Ads, Orders, and User reports.

### 🌍 Localization & UX
* **Improvement**: Replaced technical placeholders (`confirm_suspend`) with descriptive, human-readable confirmation messages across all **9 supported languages**.
* **Improvement**: Removed remaining visible English fallback copy from the Plugins and Themes admin pages so extension management now follows the active locale more consistently.
* **Improvement**: Added localized moderation copy for the redesigned Reports admin experience, including queue descriptions and reviewed-state labels across all **9 supported languages**.
* **Policy**: Updated technical documentation (`Agents.md`) with new safety guidelines for AI agents regarding terminal command execution.

### 🛠️ Metadata & Encoding
* **Fix**: Implemented robust character encoding detection and conversion in `LinkPreviewService` to resolve "strange symbols" (mojibake) in community link previews, especially for sites missing charset headers or using legacy encodings (Windows-1256, ISO-8859-6).
* **Improvement**: `LinkPreviewService` now explicitly hints UTF-8 to `DOMDocument` using pseudo-XML declarations, preventing default fallback to Latin-1 for Arabic content.
* **Refactor**: Unified website metadata extraction in `DirectoryController` (`fetchMetadata`) by integrating the improved `LinkPreviewService`, ensuring consistent title, description, and keyword retrieval while reducing code duplication.
* **Add**: Added keyword (meta keywords) extraction support to `LinkPreviewService` to maintain feature parity across discovery modules.

# v4.2.1
> **Patch Release** - Version consistency, installer metadata fixes, and update flow polish.

### Version Metadata & Update Flow
* **Fix**: Centralized the MYADS runtime version in one support class so the updater, installer, middleware, and seed data all use the same source of truth.
* **Fix**: Corrected version writes that could previously store inconsistent values like `4.0.0` or `4.1.3` inside the `options` table.
* **Fix**: Updated installer and upgrade UI copy so fresh installs and upgrades now consistently display `v4.2.1`.
* **Improvement**: Version-check caching is now scoped to the active version number, so patch upgrades do not wait on a stale cache key before syncing the database version.
* **Tests**: Refreshed maintenance/update coverage to validate the new version baseline and update flow behavior.
* **Fix**: Resolved missing sidebar icons for `Directory`, `Order Requests`, `News`, and `Advertising` on some `v4.2.1` sites after Git-based uploads where older asset caches or stale `svg-loader.js` files were still being served.
* **Improvement**: Added theme-level fallback symbols and styling for the affected sidebar icons so they now render consistently in both Light and Dark modes even when public theme assets are not fully refreshed yet.

### 🎨 UI & Responsive Fixes
* **Improvement**: Overhauled the profile editing page (`/profile/edit`) with a modern "Superdesign" layout, featuring unified float-label inputs, rounded styling, and categorized sections.
* **Improvement**: Refined the avatar and cover photo upload mechanics on the profile editor to display as native floating action buttons over the preview layout instead of generic separate boxes.
* **Fix**: Resolved an alignment and styling issue mapping over `input[type="email"]` fields in the default theme that caused them to appear unstyled globally; they now use consistent `inputmode="email"` mapping to inherit the platform's standard input styling.
* **Fix**: Fixed a visual bug in the password fields where placeholder text explicitly overlapped with floating active labels.

# v4.2.0
> **Feature Release** - Community feed overhaul, profile hub privacy, admin ACL, and platform integrations.

### 📊 Admin Analytics & Charts (Community Stats)
* **Add**: Introduced a new **Community Stats Charts** section in the Admin Dashboard (`/admin`).
* **Add**: **30-Day Activity Visualization**: Two new interactive line charts (Chart.js) visualizing trends over the last 30 days.
    *   **Posts Chart**: Tracks new content by type — **Text Posts**, **Link Posts**, **Gallery Posts**, **Forum Topics**, **Store Products**, **Order Requests**, and **News Articles**.
    *   **Engagement Chart**: Tracks community interactions — **Forum Comments**, **Store Comments**, **Order Comments**, **Directory Comments**, **Forum Reactions**, **Store Reactions**, and **New Member Follows**.
* **Add**: **Reaction Counters Widget**: A new horizontal stats bar below the charts showing total counts for all 8 reaction types: `Like`, `Love`, `Dislike`, `Funny`, `Wow`, `Sad`, `Angry`, and `Happy`.
* **Add**: Support for the **Dislike** reaction type in both timeline charts and total counters.
* **i18n**: Added comprehensive translation keys for all 15+ new chart labels and categories across Arabic and English.
* **Architecture**: Implemented a performant backend query logic in `AdminController.index` using `DB::raw` and `FROM_UNIXTIME` for grouped daily stats across disparate tables (`status`, `f_coment`, `options`, `like`).
* **UI/UX**: Refined chart containers with fixed heights (`350px`) to prevent layout jumping and ensure consistency with the Duralux admin theme.

### 🔍 Advanced Inventory Filters
* **Add**: Implemented a new **Advanced Filter System** for admin inventory pages: `/admin/banners`, `/admin/links`, `/admin/smart-ads`, and `/admin/visits`.
* **Add**: Multiple filter fields based on data type (text, date, number, dropdown select) for comprehensive search capabilities.
* **Add**: Support for **AND/OR logic** to combine filters, allowing for more precise data retrieval.
* **Add**: "Apply Filter" and "Reset" buttons for easy management of filter settings.
* **Add**: Display of **results count** after filtering to provide immediate feedback on search outcomes.
* **Add**: User preference saving for filter settings, ensuring that preferred filters are retained across sessions.
* **Improvement**: Standardized UI components, CSS styles, JavaScript functionality, data structure, and responsive behavior across all affected admin inventory pages.
* **i18n**: Added new translation keys for all filter-related labels and messages across all 9 supported languages.
* **Tests**: Added `AdminInventoryFiltersTest` to cover functional testing of the new filter system, including saved preferences and AND/OR logic.

### Security Suite
* **Add**: Introduced a dedicated **Security** admin area at `/admin/security` with a new ACL module, Duralux sidebar entry, main settings page, IP ban management, member session audit log, and admin password confirmation flow.
* **Add**: Added centralized `SecuritySettings` backed by `options` (`o_type = security_settings`) for link/domain blacklists, cooldowns, registration caps, login throttling, admin reconfirmation, private message encryption, and public member IDs.
* **Add**: Added `SecurityPolicyService`, `LocalUrlSafetyInspector`, and `SecurityThrottleService` to enforce local URL safety rules and per-member cooldowns across registration, login, posts, comments, forum topics, private messages, link previews, and banner/link/smart/visit ads.
* **Add**: Added global `BlockBannedIp` and `TrackMemberSecuritySession` middleware plus the `security_ip_bans` and `security_member_sessions` tables for whole-site IP blocking and revocable member session tracking.
* **Add**: Added `users.public_uid` with backward-compatible public shortcut routing so `/u/id/{id}` can redirect toward canonical public IDs when enabled.
* **Add**: Added optional at-rest encryption for newly created private messages using `enc:` payloads and Laravel `Crypt`, while keeping legacy plaintext messages readable.
* **Improvement**: Private message URLs now use encrypted conversation identifiers on `messages/{id}` while legacy numeric links remain compatible.
* **Improvement**: Mixed legacy/private-message threads now show a lightweight in-thread notice when newer encrypted messages begin, so both participants can clearly distinguish the encrypted section.
* **Fix**: Header and sidebar avatar/profile shortcuts now respect `public_member_ids_enabled` and switch to public short-profile identifiers immediately.
* **i18n**: Added security/admin translation keys across all 9 supported languages.
* **Tests**: Added `SecuritySuiteFeatureTest` covering security ACL access, admin password confirmation middleware, private message encryption, and public profile shortcut identifiers.

### Update Safety & Backups
* **Add**: Introduced a hard safety layer for test execution using `.env.testing`, `database/testing.sqlite`, and `TestingSafetyGuard` so automated tests can no longer touch the live MySQL database.
* **Add**: Added `php artisan myads:update:preflight` and `UpdateSafetyService` to verify the active database connection, writable update paths, pending migrations, and destructive operations before any upgrade is allowed to continue.
* **Improvement**: The admin updater now blocks execution unless the operator confirms that both the database and files were backed up, and it runs maintenance mode plus `migrate --force` / `optimize:clear` only after safety checks pass.
* **Docs**: Added a dedicated Git upgrade guide and strengthened installation/upgrade documentation with explicit backup requirements and warnings against `migrate:fresh`, `db:wipe`, and non-isolated test runs.

### Promoted Community Posts
* **Add**: Introduced a full **Promoted Posts** system for community activity, allowing members to promote eligible posts they own from `/portal` using `PTS`.
* **Add**: Added a dedicated member flow from the post settings menu to `/ads/posts/{status}/promote`, including campaign setup, smart quote preview, points affordability checks, and launch confirmation.
* **Add**: Added `status_promotions`, `StatusPromotion`, `StatusPromotionService`, `StatusPromotionPricingService`, and `StatusPromotionSettings` to support campaign storage, smart pricing, delivery pacing, progress tracking, and admin-configurable limits.
* **Add**: Added member and admin management pages for promoted posts, including `/ads/posts`, `/admin/ads/posts`, and `/admin/ads/posts/settings`, with integration into the existing ads ACL module.
* **Add**: Promoted posts now appear inside `/portal?filter=all` alongside organic activity with a visible **Ad** label, pacing controls, non-adjacent insertion rules, and per-viewer cooldown protection.
* **Improvement**: Added support for promotion objectives based on views, comments, reactions, or campaign duration in days, with smart `PTS` pricing and delivery-cap calculations.
* **Improvement**: Added campaign progress monitoring for members and administrators, including `active`, `completed`, `expired`, `paused`, and `budget_capped` statuses plus owner notifications for campaign milestones.
* **Fix**: Resolved AJAX quote loading on the promoted-post setup page so campaign pricing works correctly even when MYADS is installed in a subdirectory such as `/myads`.
* **Fix**: Resolved infinite-scroll loading on `/portal` so additional community posts continue loading correctly when follow-up pagination URLs are generated from subdirectory or mixed-base paths.
* **i18n**: Added promoted-post translation coverage across all 9 supported languages for member flows, admin settings, statuses, notifications, and pricing labels.
* **Tests**: Added feature coverage for promoted-post purchase rules, quote validation, feed injection, budget-capped campaigns, admin settings, member dashboards, and portal follow-up page loading.

### 🗺️ Sitemap & Privacy
* **Add**: Implemented a comprehensive **Sitemap Privacy Suite** that enforces author privacy and forum visibility rules across the search engine index.
* **Add**: Integrated `visible()` scope for Topics and Users, ensuring "Private" and "Followers-only" content is excluded from guest crawlers.
* **Add**: Added a dedicated **Forum Category Section** to the sitemap to improve SEO discoverability for public boards.
* **Add**: Automated exclusion of restricted forum topics belonging to members-only or moderator-only categories.
* **Improvement**: Hardened `SitemapController` with schema-safe queries and robust date parsing for legacy timestamps.

### 👥 Users & Admin Roles
* **Fix**: Resolved an issue in the Admin Panel (`/admin/users`) where verified users (`ucheck`) were incorrectly displayed as Admins in the Role column and filter.
* **Add**: Introduced sortable columns in the Admin Users table (Username, Role, Connection Status, Points) with persistent sorting via pagination.
* **Add**: Added a visual verification badge (<i class="bi bi-patch-check-fill"></i>) next to verified usernames in the Admin Users list.

### 🔌 Plugins & Themes System
* **Add**: Advanced Plugin & Theme management system with a secure thumbnail delivery route.
* **Add**: Real-time update checking via GitHub API, featuring version badges and "Update Now" links.
* **Add**: Interactive **Changelog Modal** that fetches and displays release notes directly from GitHub.
* **Add**: Support for **Thumbnails** in the admin panel (checks `plugin.json`/`theme.json` or falls back to `screenshot.png`).
* **Add**: Compatibility locking using the new `min_myads` metadata field to ensure system stability.
* **Security**: Deletion of active plugins or themes is now restricted to prevent system breakage.
* **Architecture**: Standardized `plugins/` and `themes/` directory tracking in Git while keeping contents ignored.
* **Dev Resources**: Created comprehensive `PLUGIN_GUIDE.md` and `THEME_GUIDE.md` for third-party developers.
* **i18n**: Added new translation keys for plugin/theme management across all 9 supported languages.

### 🏆 Gamification & Badges
* **Add**: Introduced a new **Badges Hub** (`/badges`) featuring a premium responsive grid for browsing all community achievements.
* **Add**: Implemented 12 brand-new unlockable badges across various categories:
    * **Community Activity**: `Night Owl` (late-night posting), `Social Butterfly` (follow count), `Point Millionaire` (wealth milestone).
    * **Marketplace & Directory**: `Marketplace Collector` (store additions), `Directory Guru` (site listings), `Knowledge Contributor` (wiki articles).
    * **Orders System**: `Order Pioneer` (first request), `Best Helper` (winning offers), `Top Bidder` (participation), `Highly Rated` (5-star feedback).
    * **Forum**: `Forum Veteran` (topic count), `Helpful Member` (replies), `Topic Starter` (diversity), `Expert Moderator` (moderation actions).
* **Add**: Integrated **Visual Progress Tracking** with animated progress bars and status tags (Locked/Unlocked) for every badge.
* **Add**: Enhanced **Badge Icons** using a curated set of FontAwesome 6 icons specifically matched to each achievement.
* **Improvement**: Rebuilt the **Badge Showcase** on member profiles to support the new dynamic icons and localized badge names.
* **Architecture**: Hardened `GamificationService` with silent error handling and automatic activity triggers across Store, Directory, Orders, and Forum modules.
* **i18n**: Fully localized all badge titles, descriptions, and hub interfaces across all 9 supported languages.
* **Fix**: Resolved an issue where the **Highly Rated** badge was not- Fix: Corrected misspelled `v_visited` and implemented surfing progress recording in `GamificationService`.

### [Order Requests]
- Fix: Added error and validation feedback to order creation page for better debugging.
- Fix: Corrected reaction type in `Status` model to align with `ReactionController` (Type 6).
- Fix: Separated order social comments (`order_comment`) from bids (`o_order`) in `CommentController` to fix broken counters.
- Fix: (Systemic) Database encoding transition to `utf8mb4` to support Arabic content.
* **Fix**: Resolved progress tracking for the **Active Surfer** badge by implementing visit recording and correcting a typo in the criteria query.
* **UI/UX**: Implemented a modern "Badges Hub" header with dynamic gradients, RTL support, and mobile-responsive layouts.

### 🌐 Localization & i18n
* **Improvement**: Conducted a full synchronization of all translation files with the English master (`lang/en/messages.php`).
* **Fix**: Repaired missing or broken translation keys for Arabic, German, Spanish, Persian, French, Italian, Portuguese, and Turkish.
* **i18n**: Standardized the localization architecture, ensuring consistent PHP array formatting across all language packs.
* **Fix**: Corrected RTL layout issues in the Badges Hub and Profile sections for better alignment in Arabic and Persian modes.

### 💼 Services & Order Requests
* **Add**: Introduced a standalone **Order Request System** for website owners to hire service providers (developers, designers, writers).
* **Add**: Automatic social integration where new order requests are published as interactive cards in the **Community Feed**.
* **Add**: Dedicated browsing and creation interfaces for order requests, supporting category filtering and budget tracking.
* **Add**: Direct messaging integration from order request cards to facilitate quick communication between owners and providers.
* **Add**: **Engagement Enhancements**: Implemented advanced sorting (Newest, Most Active, Highest Rated), "Best Offer" selection by owners, and a mutual rating system for orders and participants.
* **Gamification**: Integrated with `PointLedgerService` to award PTS for posting orders, bidding, and winning the "Best Offer" selection.
* **Management**: Added order closure functionality for owners and site administrators, with automated information notifications.

### 📰 News & Community Portal
* **Fix**: Resolved an issue where news posts appeared on the profile page of the admin who created them; news now strictly appears only in the Community Portal feed (`/portal`).
* **Fix**: Resolved `500 Internal Server Error` when posting news by fixing missing `statu` and `img` database columns.
* **Add**: News articles are now automatically posted to the Community Portal feed (`/portal`) upon creation.
* **Add**: News articles in the portal now display uploaded images dynamically with a default cover fallback.
* **Improvement**: Updated `FeedService` to rank current news articles by recency and engagement.
* **Improvement**: News articles are now searchable via the community portal search bar.

### SEO & Indexing Suite
* **Add**: Introduced a centralized SEO engine for public pages, unifying titles, descriptions, canonical tags, robots directives, Open Graph, Twitter cards, and structured data output.
* **Add**: Added a full `/admin/seo` suite with Dashboard, Settings, Head Meta, Rules, and Indexing screens for managing and auditing search visibility.
* **Add**: Added local SEO analytics with charts for page views, unique visitors, bot hits, top scopes, and top content pages.
* **Add**: Added optional Google Analytics 4 integration through a GA4 Measurement ID with safe injection on public indexable pages only.
* **Improvement**: `robots.txt` is now managed dynamically from the admin panel instead of blocking indexing site-wide, with full support for multi-site `APP_URL` configurations.
* **Improvement**: `sitemap.xml` now respects `lastmod`, skips `noindex` content, and stays aligned with published public resources using absolute URLs.
* **Fix**: Resolved `500 Internal Server Error` on the SEO Indexing admin page by naming the dynamic robots route.
* **Fix**: Hardened `SitemapController` and `SeoAuditService` with robust `try-catch` blocks and `V420SchemaService` to prevent 500 errors during database instability or incomplete upgrades.
* **Improvement**: Standardized `RobotsTxtService` for consistent, dynamic sitemap URL generation across all server environments.
* **i18n**: Added SEO translation keys across all 9 supported languages and removed hardcoded SEO admin strings.

### Community Feed & Composer
* **Add**: Expanded `POST /status/create` to support `text`, `link`, `gallery`, and `repost` publishing flows while remaining compatible with legacy `s_type` inputs.
* **Add**: Added URL preview fetching via `POST /status/link-preview`, optional Directory saves from the same composer flow, and automatic duplicate suppression so generated Directory statuses no longer appear twice in `/portal?filter=all`.
* **Add**: Added multi-image gallery publishing with support for up to 10 images per post, persisted through `forum_attachments` while preserving legacy `image_post` compatibility.
* **Add**: Added quote reposts backed by `status_reposts`, repost counters in activity cards, and repost notifications to original authors.
* **Improvement**: Refactored gallery posts to store attachments in `public/upload/` and decoupled them from forum moderation logic (Pin/Lock).
* **Improvement**: Redesigned the gallery view (`/t{id}`) into a premium responsive image grid and simplified the editor for non-forum gallery posts.
* **Improvement**: Reworked `/portal?filter=all` around a hybrid ranking model that blends recency, follows, author affinity, content affinity, social proof, reactions, comments, reposts, capped view boosts, and diversity penalties with a 5-minute cache.
* **Improvement**: Kept `/portal?filter=me` chronological for followed accounts and the current member while excluding duplicated directory statuses from the ranked feed.
* **Improvement**: Activity cards now render link previews, galleries, repost embeds, repost counts, and quote repost actions more consistently across supported content types.
* **Standardization**: Unified post and comment deletion into a single, robust 'in-place' confirmation system across all platform modules (Forum, Store, Directory, Orders).
* **UI/UX**: Replaced inconsistent native browser confirmation dialogs with premium theme-integrated confirmation boxes that replace content in-place for a seamless experience.
* **Architecture**: Centralized deletion logic in `master.blade.php` with support for type-specific API routing and dynamic container targeting.

### 📚 Wiki & Markdown Knowledgebase
* **Feature**: Transformed the legacy Knowledgebase into a collaborative **Wiki-style system** with a dedicated creation flow for missing articles.
* **Add**: Integrated **StackEdit** for a premium, Markdown-first editing experience with real-time preview and full-screen support.
* **Add**: Implemented a high-performance **AJAX Preview System** that allows side-by-side review of historical and pending article versions.
* **Improvement**: Optimized the Wiki layout for **RTL (Right-to-Left)** languages, ensuring the preview area and sidebar are correctly aligned.
* **Architecture**: Integrated `marked.js` and `DOMPurify` for secure client-side Markdown rendering and XSS prevention.
* **Fix**: Resolved UI issues where the site header overlapped with the StackEdit editor using dynamic JavaScript-based positioning.
* **i18n**: Added new translation keys for the StackEdit editor across all 9 supported languages.

### Mentions, Notifications & Formatting
* **Add**: Added server-side `@mentions` extraction and storage through `status_mentions` for both posts and comments, including member notifications without self-notify behavior.
* **Add**: Added `GET /mentions/users?q=` lookup support for composer/autocomplete integrations and filtered it through member privacy rules.
* **Improvement**: Unified text formatting for community posts and comments so hashtags, safe links, Markdown, and mentions now flow through shared formatter services instead of Blade regex fragments.

### Profile Hub, Privacy & Gamification
* **Add**: Added `/settings/privacy` with per-member controls for profile, about, photos, followers, following, points history, direct messages, mentions, reposts, and online status visibility.
* **Improvement**: Enforced the new privacy rules across profile tabs, SEO profile descriptions, direct messaging, mention delivery, repost delivery, and profile navigation visibility.
* **Add**: Added badge showcase management via `/settings/badges`, badge display on profile pages, and a refreshed points history ledger on `/history`.
* **Improvement**: Rebuilt profile `About Me` around `users.sig`, upgraded `/u/{username}?tab=photos`, and refreshed `/history` toward a more profile-hub style layout.
* **Add**: Introduced `point_transactions`, `badges`, `user_badges`, `badge_showcase`, `quests`, and `quest_progress` with seeded v1 milestones and daily/weekly quests for posts, comments, reactions, reposts, and follow growth.
### 🏆 Premium Quests & Gamification
*   **Add**: Introduced a new **Premium Quests Page** (`/quests`) with a high-fidelity "MyAds_Default_Theme" design, featuring fluid animations and entrance effects.
*   **Add**: Implemented 5 brand-new repeatable quests: **New Connections** (Follows), **Forum Starter** (Topics), **Web Explorer** (Directory), **Tool Collector** (Store), and **Service Helper** (Order Bids).
*   **Improvement**: Enhanced the **Daily Quests Sidebar Widget** with a prominent "All Quests" button and a direct "History" link for improved user navigation.
*   **Architecture**: Integrated quest event hooks across `ProfileController`, `ForumController`, `StoreController`, `DirectoryController`, `AuthController`, and `CommentController`.
*   **Maintenance**: Built a centralized `repairQuestData` service and a dedicated migration (`2026_03_26_040000_fix_legacy_quest_icons.php`) to permanently correct icon mappings and target counts.
*   **Automation**: Quest data repair is now automatically triggered during **System Installations**, **Dashboard Updates**, and through the **Admin Maintenance** "Repair Database" tool.
*   **i18n**: Fully localized all new quest strings and widget actions across all **9 supported languages**.

### 🏛️ Forum & Category Management
* **Add**: Introduced **Forum Category Visibility Controls**, allowing administrators to restrict access to specific categories and their topics based on user roles (Everyone, Registered Members, or Moderators/Admins only).
* **Add**: Implemented **Safe Category Deletion** with mandatory topic migration. Administrators must now select a target category to move existing topics to before a category can be deleted, preventing accidental data loss.
* **i18n**: Added comprehensive translation keys for visibility settings and deletion migration flows across all supported languages.
* **Fix**: Resolved `500 Internal Server Error` when updating forum categories by refactoring `AdminController.php` to use a secure whitelisted input flow for model updates.
* **Add**: Integrated a premium searchable **FontAwesome 6 Icon Picker** in the forum category admin panel, replacing the legacy static dropdown with a dynamic, real-time preview component.
* **Add**: Massive expansion of the available icon set, featuring over **250+ curated FontAwesome 6 icons** across categories like Places, Transport, Digital, and Brands (including user-requested icons like PlayStation, Bilibili, and Satellite).
* **Improvement**: Standardized the icon selector to follow the **Duralux Admin** design pattern (hstack, gap-3), providing interactive previews and better alignment inside the selection box.
* **i18n**: Added missing `select_icon` translation keys for all supported locales to ensure a localized interface for the new icon picker.
* **Add**: Dedicated **SEO Sidebar Section**, moving all search-related tools (Dashboard, Settings, Head Meta, Rules, Indexing) into a standalone menu for better organization.

### Admin ACL & Forum Navigation
* **Add**: Added `/admin/admins` CRUD for site administrators with full-access and module-scoped permissions backed by `site_admins`.
* **Improvement**: Centralized admin authorization through `AdminAccessService`, updated `AdminMiddleware`, and wired admin navigation visibility to module-based access rules.
* **Security**: Locked `user_id=1` as the default super-admin, preventing scoped admins from managing administrators and preventing super-admin removal.
* **Add**: Permanent **Admin Maintenance Dashboard** (`/admin/maintenance`) for cache clearing, database migrations, and table optimization (Repair & Optimize) directly from the browser.
* **Add**: Integrated "System Maintenance" link into the admin sidebar under the "Options" menu for quick system recovery.
* **Delete**: Safely removed the temporary `maintenance.php` web utility after successful integration into the secure admin panel.
* **Improvement**: Switched forum category pages `/f{id}` from infinite-scroll loading to standard HTML pagination with 20 topics per page.

### 🧩 Portal Widgets
* **Add**: Expanded the platform's widget system with new dynamic components for the Community Portal and Member Profiles.
* **Add**: **Latest Orders**: A new sidebar widget that fetches and displays the 5 most recent service requests from the marketplace with live categorization and budget tracking.
* **Add**: **Badge Showcase**: A personalized widget for authenticated members that renders their manually selected and showcased achievements directly in the sidebar.
* **Add**: **Daily Quests**: An interactive gamification widget that tracks and displays real-time progress for daily milestones (Posts, Comments, Reactions, Visits) with visual progress bars.
* **Standardization**: Unified the Portal Sidebar widget ecosystem, ensuring all "Latest Content" blocks (Topics, News, Products, Sites, Orders) follow a consistent design pattern and localized title management.
* **Improvement**: Updated the Admin Widget management interface to support the new v4.2.0 widget types and unified the selection dropdown with localized labels.
* **i18n**: Added comprehensive translation keys for all new widget labels and states in both English and Arabic.

### i18n, Recovery & Validation
* **Fix**: Repaired corrupted v4.2.0 locale entries and ensured the new community, privacy, badge, quest, mention, repost, and admin ACL keys are present across all 9 shipped locales.
* **Fix**: Added centralized v4.2.0 schema detection with graceful fallback handling so missing upgrade tables such as `site_admins`, `user_privacy_settings`, and `status_link_previews` no longer trigger `500` errors on `/portal`, profile pages, forum topic cards, sidebars, and other authenticated surfaces.
* **Fix**: Hardened `/portal` activity cards and repost embeds against legacy feed rows whose author account no longer exists, so missing users now fall back to translated placeholders instead of triggering Blade `500` errors.
* **Fix**: Settings and admin pages that depend on incomplete v4.2.0 tables now render upgrade notices and block writes with friendly translated messages instead of crashing, while public reads continue in compatibility mode where possible.
* **Fix**: Resolved a critical syntax error in `AdminController@validatedBannerSize` that caused 500 errors across several admin panel surfaces.
* **Add**: Manual `.env` loader in `bootstrap/app.php` to ensure correct configuration loading in restrictive hosting environments.
* **Add**: Added a dedicated repair migration for the March 23, 2026 social feed extension schema and new fallback-focused feature coverage for incomplete-upgrade scenarios.
* **Add**: Added feature coverage for social publishing, mention privacy, profile privacy enforcement, badge showcase limits, admin ACL, portal feed deduplication and chronology, forum pagination, and v4.2.0 translation key coverage.
* **Validation**: Verified updated PHP files with `php -l`, confirmed the new routes during route inspection, and ran the dedicated `V420*` feature test suite successfully.

### 🛒 Store & Product Management
* **Add**: Introduced a robust **Product Suspension System**. Administrators can now suspend specific products, hiding them from the public store index and search results while maintaining access for owners and admins.
* **Add**: High-visibility **Suspension Notice** displayed on the product page for owners and administrators to clearly identify the product status.
* **Add**: **Status Badges** for suspended products in store preview cards for an improved administrative workflow.
* **Add**: **Automated Update Notifications**. The system now automatically publishes a community feed post whenever a product owner updates their product version, increasing visibility for new releases.
* **i18n**: Added comprehensive translation keys for product suspension and update labels across all 9 supported languages.
* **Architecture**: Overrode the `visible` scope in the `Product` model to seamlessly integrate with `HasPrivacy` while enforcing new suspension visibility rules.

### 🖼️ Community Feed & Directory Modernization
* **Add**: Introduced a premium **"Superdesign"** card layout for directory and forum activities, featuring high-fidelity visuals, shadow depth, and localized RTL support.
* **Add**: Implemented a **Persistent Image System** that stores website banners in the database, providing returning users with an instant visual experience without waiting for background fetches.
* **Add**: Introduced **Asynchronous Lazy Loading** for directory images using the **Intersection Observer API** and a dedicated AJAX endpoint, significantly reducing initial page load times and bandwidth.
* **Improvement**: Implemented a **"Stale-While-Revalidate"** logic for directory images; the system displays the stored image immediately while revalidating and updating it in the background if a change is detected.
* **Add**: Enhanced **Broken Link Detection** with automatic 404 status persistence, displaying a specialized alert icon and preventing redundant external fetches for dead links.
* **Standardization**: Unified directory image fallbacks and placeholders across the Community Feed, Website Directory, and Member Profiles.

# v4.1.3
> **Patch Release** - Embed delivery, routing, and Smart Ads reliability fixes.

### Smart Ads & Embed Delivery
* **Fix**: Root `.htaccess` now preserves dynamic Laravel embed endpoints such as `/embed/smart.js`, `/embed/banner.js`, and `/embed/link.js` under subdirectory installs, preventing Apache `404` errors before requests reach Laravel.
* **Fix**: Smart, banner, and link short-code loaders now use safer slot-based injection instead of chained `document.write` calls, improving compatibility when ads are embedded on external websites.
* **Fix**: Legacy ad-serving endpoints (`bn.php`, `link.php`, and `smart.php`) now support safer targeted rendering near the copied embed script without changing their public URLs.
* **Improvement**: Smart Ads code pages continue to expose the same public snippets while generating more reliable loader behavior behind the scenes.

### Smart Ads Metadata & Display
* **Fix**: Smart Native Ad image and title are now fully clickable, matching the existing "Visit Sponsor" button behavior.
* **Fix**: Smart ad analysis now normalizes UTF-8 metadata more safely for Arabic and other non-Latin landing pages that declare charset inside HTML meta tags.
* **Fix**: Long Smart Ads URL fields and extracted source images are now handled more safely, reducing save-time failures on very long landing-page metadata.
* **Improvement**: Smart Native Ad images are now fully responsive, preventing cropping regardless of the uploaded image dimensions.
* **Improvement**: Banner, link, smart, and fallback ad output now keeps centered placement more consistently across third-party websites.

### Sitemap & SEO Improvements
* **Improvement**: Completely redesigned to use a **Sitemap Index Protocol** instead of a single giant XML file.
* **Improvement**: Sitemap is now generated dynamically using XML streaming and database chunking (10,000 URLs per file), which prevents memory exhaustion on large websites and complies with search engine limits.
* **Add**: Custom dynamic pages (`/page/{slug}`) are now automatically indexed inside their own sub-sitemap.
* **Fix**: Generating the sitemap via the admin panel automatically purges any leftover static `public/sitemap.xml` file so the high-performance streamed version takes over seamlessly.

# v4.1.2
> **Patch Release** - RTL, Smart Ads, and ad code fixes.

### 🎨 UI & Responsive Fixes
* **Fix**: Fixed a bug where the profile page (`/u/{username}`) content disappears to the far right on mobile devices when switching to RTL mode.
* **Fix**: Resolved an infinite scrolling bug on Community, Directory, Forum, and Profile pages where the loader occasionally stopped fetching older posts.
* **Fix**: Fixed an AJAX parsing error on infinite scroll and other API endpoints caused by UTF-8 BOM encodings in language files.

### Smart Ads & Embed Code
* **Fix**: Smart Ads code page (`/ads/smart/code`) now shows a direct built-in preview of the latest active owned smart ad instead of relying on the live loader response.
* **Fix**: Smart ad serving (`smart.php`) now tries eligible ads from other members first, then falls back to the publisher's own smart ad when no external campaign matches.
* **Fix**: Self-fallback smart ads no longer award points, consume `nsmart`, or record impression/click tracking when the publisher's own ad is shown as a fallback.
* **Improvement**: Added clearer Smart Ads code page messaging and empty-state translations across all shipped languages.

### Text Ads & Link Code
* **Fix**: Responsive text ads served from `/l_code` now open ad clicks in a new browser tab.

### Store Editor & Product Flow
* **Improvement**: Redesigned the store product create page (`/store/create`) and product update page (`/store/{name}/update`) into a clearer shared editor layout with stronger card hierarchy and a reusable file source picker.
* **Improvement**: Store file delivery now uses a unified `ZIP Upload / Direct Link` switcher with a single `linkzip` submission contract, clearer upload feedback, and safer reusable frontend logic.
* **Fix**: Product updates now redirect back to the product detail page after publishing instead of staying on the update form.
* **Fix**: Product detail pages now surface success flash messages after creating or updating a store product.
* **Fix**: The product create page keeps the `SCEditor` experience on `textarea#editor1`, preserving rich content submission for the main topic body.
* **Improvement**: Product update pages now show file version history inside a collapsible card instead of mixing it directly into the edit form.

### Store Product & Knowledgebase Experience
* **Improvement**: Rebuilt the store product page (`/store/{name}`) into a stronger product shell with clearer hierarchy, publisher summary, stat tiles, and a single actions menu.
* **Add**: Product pages now support inline reporting for the product and its publisher, while owners and admins can manage the same page from the new actions menu.
* **Fix**: Deleting a product from its detail page now redirects back to `/store` with a visible success flash, while the existing JSON delete response remains compatible with current JavaScript clients.
* **Improvement**: Rebuilt the shared knowledgebase experience across `/kb/{name}`, `/kb/{name}:{article}`, `/edk/{name}:{article}`, `/pgk/{name}:{article}`, `/hkd/{name}:{article}`, and `/kb/{name}?st={name}` with a unified product-linked shell and clearer review/editor layouts.
* **Add**: Knowledgebase topics now support inline reporting for the topic itself and for the current topic publisher, with guest-authored topics hiding publisher report actions automatically.
* **Add**: Admin reports now recognize a dedicated Knowledgebase report type and link directly back to the current KB topic preview.

# v4.1.1
> **Patch Release** - Store enhancements, UI, RTL, and translation fixes.

### 🏪 Store & Admin Management
* **Add**: Product creators can now choose between uploading a ZIP file or providing a direct external download link.
* **Add**: Optional cover image and price updates on the product edit page (`/store/{name}/update`).
* **Add**: Full product editing capabilities from the Admin panel (`/admin/products/{id}/edit`).
* **Add**: Admin product suspension system (suspend/unsuspend) with automatic notifications sent to product owners.
* **Add**: File version history and download preview in the Admin product edit view.

### 💬 Translations & Localization
* **Add**: Added new translation keys for Store external links and Admin product management across all 9 languages.
* **Add**: Fully translated strings for `Responsive 2` embed code formats across all 9 languages.
* **Fix**: Removed hardcoded English strings from `/b_code` and `/l_code` pages.
* **Improvement**: Clarified point earning sentences (referrals, exchange visits, ad impressions) in Arabic and English translations.

### 🎨 UI & Responsive Fixes
* **Fix**: Fixed critical RTL layout bug where `margin-left: auto` caused main content limits to clip beyond the right edge on desktop views.
* **Fix**: Fixed a bug where `/forum` category sections disappeared completely on mobile devices in both RTL and LTR orientations.
* **Fix**: Corrected javascript sidebar toggler layout calculation in RTL mode to prevent horizontal sliding content jumps.

### 🐛 Bug Fixes
* **Fix**: Resolved `500 Internal Server Error` on `/l_code` triggered by an undefined `$responsive2Preview` variable preventing the link codes page from loading.

# v4.1.0
> **Feature Release** - Notification center refresh, forum topic flow fixes, and test coverage improvements.

### Installer & Fresh Install
* **Fix**: Fresh installs from Git now auto-create `.env` before the first full bootstrap so installer requests no longer start with missing environment configuration.
* **Fix**: Installer bootstrap now generates a unique `APP_KEY` during fresh setup instead of relying on the shared example key.
* **Fix**: CSRF exclusions now explicitly cover both `install` and `install/*` routes to prevent `419 Page Expired` during setup.
* **Fix**: Default session and cache fallbacks now use `file` instead of `database`, so `/install` no longer depends on Laravel system tables before migrations run.
* **Fix**: Legacy upgrades from v3.2.x now repair missing forum schema pieces such as `f_cat.ordercat`, forum moderation tables, and related compatibility columns so `/forum` and new admin forum pages no longer fail with `500` after upgrade.

### Notifications & Header UX
* **Improvement**: Redesigned `/notification` into a clearer notification center with a summary card, stronger unread highlighting, and a cleaner notification feed.
* **Add**: Added a red unread count badge above the notification button in the desktop header.
* **Add**: Added a red unread count badge above the notification button in the mobile `floaty bar`.
* **Improvement**: `Mark all as read` now updates visible unread indicators and counters immediately across the header, mobile bar, and notification page.
* **Improvement**: Reworked notification item rendering into reusable partials so initial page load and AJAX-loaded items share the same markup.

### Notification Loading & AJAX
* **Improvement**: Replaced classic pagination on `/notification` with infinite scroll loading while scrolling down.
* **Add**: `GET /notification` now returns JSON for AJAX requests with `html` and `next_page_url`.
* **Improvement**: `POST /notification/mark-all-read` now returns a clearer AJAX response with `success` and `unread_count`.
* **Fix**: Preserved existing read behavior so notifications are marked as read only when opened or when `mark all as read` is used.

### Forum Topic Flow Fixes
* **Fix**: Topic creation now redirects directly to the newly created topic page.
* **Fix**: Topic update now redirects back to the topic page even when category input is missing, using a safe fallback path.
* **Fix**: Attachments uploaded during topic updates now remain visible on the topic page.
* **Fix**: Topic pages now use the standard comment container selector consistently.
* **Fix**: Forum comment store flow continues to return HTML correctly for AJAX usage without forcing a full page reload.
* **Fix**: Knowledgebase routes now accept legacy store names that contain hyphens, so links مثل `/kb/Web-Designing` no longer return `404`.
* **Fix**: Legacy listing pages `/b_list`, `/l_list`, and `/v_list` now submit real delete forms directly instead of relying on outdated modal triggers, so ad and visit deletion works again.

### Forum Sidebar Refresh
* **Improvement**: Redesigned the forum category sidebar on `/f{id}` so it now follows the stronger card language used in the website directory sidebar while staying consistent with the forum theme.
* **Improvement**: Forum category cards now show the category icon, title, short description, and a topic count pill, with a clearer active state for the currently opened section.
* **Improvement**: Sidebar category data is now prepared in the controller instead of querying categories directly inside the Blade view.
* **Improvement**: Topic counts in the forum sidebar are now precomputed from active topics only, avoiding repeated queries inside the sidebar loop.

### Authentication UI
* **Improvement**: Refined the default theme Blade templates for the login and registration pages.
* **Add**: Social login buttons for Facebook and Google now appear directly inside the login form when providers are configured.
* **Add**: Social login buttons were also added to the registration page for a smoother sign-up flow.
* **Fix**: Social auth actions are now shown conditionally based on available environment configuration instead of rendering empty placeholders.
* **Improvement**: Social login buttons now adapt their width automatically when only one provider is enabled.

### Profile & Follow System
* **Fix**: Resolved the profile follow action error on `/profile/{id}/follow` by storing the required `time_t` value in the `like` table instead of attempting to write to a non-existent legacy `date` field.

### Community Feed & AJAX Interaction Fixes
* **Fix**: Community posts loaded later via infinite scroll now keep their `comment`, `react`, and `share` actions working correctly on `/portal` and profile activity tabs.
* **Improvement**: Activity comment buttons now use a shared delegated frontend handler instead of per-post inline script blocks, so dynamically injected posts behave like the initial page render.
* **Improvement**: Activity post `react` and `share` menus now use dedicated activity-menu toggles instead of relying on the global dropdown initializer that only handled the first render reliably.
* **Fix**: Reaction user lists in community posts now open correctly on hover again, with matching positioning fixes for both LTR and RTL layouts.
* **Improvement**: Infinite scroll rendering now supports registered post-render callbacks instead of a single overwrite-prone hook, keeping directory and activity feed hydration compatible.

### Smart Feed & Ranking
* **Add**: Introduced a new `Smart Feed` algorithm for `/portal?filter=all` that ranks posts based on weighted signals: recency, views, reactions, and comments.
* **Improvement**: Post ranking now gives a massive boost to fresh content (especially posts from the last 1-6 hours) using a high-base power decay formula.
* **Add**: Added `Social Proof` boosts (+10 points) when a user's followed contacts have commented on a post.
* **Add**: Added `Following` boosts (+20 points) for posts authored by users the viewer follows.
* **Improvement**: Smart feed results are cached for 5 minutes, significantly improving page load performance while maintaining a dynamic discovery experience.

### Directory UI Polish
* **Improvement**: Refined the `default` theme directory banners on `/directory`, `/cat/{id}`, and `/directory/{id}` with a more balanced layout, cleaner content width, and a stronger background treatment.
* **Add**: Redesigned the "Add Site" page (`/add-site.html`) with a premium Superdesign look and automated metadata fetching (Title, Description, Tags) via AJAX.
* **Improvement**: Repositioned the `Visit Site` button inside directory listing cards so it now appears under the title and description.
* **Improvement**: Redesigned `directory-listing-stats-detail` on the directory detail page into clearer responsive stat tiles for visits, reactions, and comments.
* **Improvement**: Simplified the directory detail hero so the new stat ribbon becomes the primary visual metrics block without changing directory functionality.

### Site Ads Placement Fixes
* **Fix**: The `Home Page` ad slot from `/admin/site-ads` now renders on the public landing page `/` instead of remaining defined in admin only.
* **Fix**: The `Topic` ad slot now appears across supported standalone content detail pages, including forum topics, store product pages, and directory detail pages.
* **Fix**: The `Footer` ad slot now renders once at the bottom of all public non-admin pages that use the main site layout.
* **Improvement**: The landing page now overrides footer ad placement so the `Footer` slot appears above the landing footer without duplicating the ad block.
* **Improvement**: Site ad rendering now uses the shared ad partial consistently for the new placements, preserving the existing hide-when-empty behavior.

### Banner Ads & Serving
* **Fix**: Banner size values are now normalized to canonical `WxH` formats across storage, edit forms, and ad serving, so legacy values like `468`, `728`, `300`, and `160` continue to work without hiding eligible ads.
* **Fix**: User and admin banner edit pages now expose the same canonical size options, and banner moderation notifications now redirect to the current `/ads/banners/{id}/edit` flow.
* **Add**: `bn.php` now records banner impressions and avoids serving the same banner repeatedly to the same visitor on the same publisher during the configured repeat window.
* **Improvement**: When all matching banners are blocked by the repeat window, banner serving now falls back to the default placement instead of reusing the same advertiser immediately.
* **Add**: Admin settings now include a configurable banner repeat-window duration for repeat-avoidance behavior.

### Smart Ads
* **Add**: Introduced a full `Smart Ads` campaign system with dedicated `nsmart` credits, user management pages under `/ads/smart`, a smart embed code page, and a new `smart.php` serving endpoint.
* **Add**: Smart campaigns now analyze the advertiser landing page to extract headline, description, image, and topic keywords automatically, while also supporting manual country and device targeting.
* **Add**: Smart ad delivery now supports contextual matching using page metadata and viewport/device signals from the embed code, plus admin-wide management and configurable point-to-credit conversion.
* **Fix**: Smart ad reporting, `/state` compatibility, and click/impression drill-downs now work through the current smart campaign flow, including `report?smart_ad={id}` support.
* **Improvement**: Smart Ads translations now follow the active locale across user pages, admin pages, embed output, and all shipped languages instead of falling back to English.

### Ads Code UX & Responsive 2
* **Improvement**: `/b_code` now defaults to a shorter legacy-style quick snippet while keeping `Advanced Code` for token-aware and responsive delivery.
* **Add**: Banner `Responsive 2` was added to `/b_code` as an advanced smart placement that detects the slot size and serves the closest legal banner format automatically.
* **Improvement**: Banner `Responsive 2` now renders with a lighter native-style chrome and a compact `Ads by {site}` label instead of the earlier heavier overlay treatment.
* **Add**: `/l_code` now includes `Responsive 2` with both direct quick code and smarter width-aware code, plus adaptive compact, stacked, and wide layouts on `link.php`.
* **Improvement**: `Responsive 2` tabs in both `/b_code` and `/l_code` now carry a visible `beta` badge to signal the new experimental format.

### Home Dashboard Smart Ads Polish
* **Add**: `/home` now includes a dedicated Smart Ads stats card, summary panel, smart credit balance, and direct shortcuts for list, create, code, and stats actions.
* **Add**: Point conversion on `/home` now supports converting points into `Smart Ads` credits directly from the existing dashboard form.
* **Add**: Completely redesigned the promotion dashboard (`/ads/promote`) with a vibrant Superdesign gradient hero, card-based layouts, and a dedicated navigation sidebar.
* **Add**: Introduced the `/promote` URL shortcut as a professional alias for the main promotion hub.
* **Fix**: The Smart Ads stat card now keeps its background correctly in `RTL` mode by using the same mirrored background variable flow as the other dashboard cards.
* **Improvement**: Smart Ads action buttons on `/home` now match the existing dashboard button palette and sizing, so they stay visually consistent in both `LTR` and `RTL`.

### Locale Direction & RTL/LTR
* **Add**: Added centralized `locale_direction()` and `is_locale_rtl()` helpers so visual direction now follows the active Laravel locale consistently.
* **Improvement**: Top-level HTML `lang`, `dir`, `data-dir`, and direction classes now render from the active locale across the public theme, auth layout, admin layout, installer pages, visits pages, and the Laravel welcome page.
* **Add**: Persian (`fa`) now renders as a full RTL language alongside Arabic.
* **Fix**: Existing `?lang` switching behavior remains intact while still updating rendered page direction, session locale, and language cookie correctly.

### RTL Interface Polish
* **Add**: Added dedicated RTL override stylesheets for the default light theme, default dark theme, and the Duralux admin theme.
* **Fix**: Shared dropdown positioning now mirrors `left/right` offsets correctly in RTL, preventing message and notification dropdowns from being clipped off-screen.
* **Fix**: Frontend RTL spacing now better aligns section banners, form controls, dropdowns, compact sidebar items, and general left/right utility behavior.
* **Fix**: `/home` top dashboard stat cards now mirror only their background artwork in RTL without flipping text, numbers, or icons.
* **Fix**: `/forum` forum category icons and titles now have improved spacing and padding in both RTL and LTR for the redesigned forum tables.
* **Fix**: Sidebar `content-grid` translation logic now respects RTL so desktop navigation and chat offsets stay visually balanced.

### Admin RTL Polish
* **Add**: Added Duralux admin RTL shell overrides for header, breadcrumbs, dropdowns, and navigation alignment.
* **Fix**: Admin header and page container now anchor from the right in RTL, including correct `minimenu` collapsed widths.
* **Fix**: Admin sidebar collapse and expand behavior now matches LTR more closely in RTL, including toggler visibility and container expansion.

### Testing & Stability
* **Add**: Added notification center feature tests covering page rendering, AJAX pagination, and `mark all as read` behavior.
* **Add**: Added a dedicated feature test to verify profile follow requests create a valid timestamped follow record without database insert errors.
* **Add**: Added a dedicated feature test covering the redesigned forum category sidebar, including category ordering, active state, and topic count rendering.
* **Add**: Added a dedicated feature test covering `Home Page`, `Topic`, and `Footer` site ad slot rendering across public pages, dashboard pages, and admin pages.
* **Add**: Added locale direction feature tests covering English, Arabic, Persian, admin pages, installer pages, visits pages, and `?lang` session/cookie persistence.
* **Improvement**: Feature test coverage now includes forum topic flow fixes, forum sidebar rendering, and notification center behavior.

# v4.0.1
> **Patch Release** — Installer resilience & shared hosting compatibility fixes.

### 🔧 Installer & Fresh Install
* **Fix**: Auto-create `.env` from `.env.example` if missing on fresh install (`index.php`).
* **Fix**: Auto-create required `storage/` subdirectories on first run (`index.php`).
* **Fix**: Remove stale `storage/installed` marker during fresh install.
* **Fix**: Added `storage/installed` to `.gitignore` to prevent shipping with repo.
* **Fix**: Handle `MissingAppKeyException` gracefully — auto-generates `APP_KEY` and redirects to `/install` (`bootstrap/app.php`).
* **Fix**: Default `APP_KEY` added to `.env.example` to prevent crash before installer runs.
* **Fix**: Enable `APP_DEBUG=true` in `.env.example` for error visibility during install; automatically disabled after install completes.
* **Fix**: Exclude `install/*` routes from CSRF verification to prevent `419 Page Expired` errors during setup (`bootstrap/app.php`).
* **Fix**: Missing `directory` table creation added to database migrations and `processMigrate()` method.

### 🐛 Bug Fixes
* **Fix**: `Status` model — null pointer crash in `getGroupedReactionsAttribute()` when reaction option doesn't exist.
* **Fix**: All `Status` model `$appends` accessors (`date_formatted`, `reactions_count`, `comments_count`, `grouped_reactions`) wrapped in `try-catch` to prevent cascading failures.
* **Fix**: `PortalController` — replaced `CONVERT(... USING utf8mb4)` raw SQL with standard `LIKE` queries for MySQL compatibility on free hosting.
* **Fix**: `DirectoryController::index()` — separated categories from activities into independent `try-catch` blocks so categories still display when activities query fails.
* **Fix**: `DirectoryController::show()` — reject non-numeric IDs with 404 (prevents `/directory/store` and `/directory/ads` from crashing).
* **Fix**: `DirectoryController::store()` — wrapped in `try-catch` with error message fallback.
* **Fix**: `DirectoryController::category()` — wrapped in `try-catch` for resilience.
* **Fix**: `AdminController::index()` — wrapped GitHub API call and stats queries in `try-catch` to prevent dashboard crash on restricted hosting.
* **Fix**: `Directory` model — added `date` to `$fillable` array (was silently dropped on create).
* **Fix**: `InstallerController::finish()` — disable `APP_DEBUG` after installation for security.

# v4.0.0
> **Major Release** — Complete rewrite from plain PHP to the Laravel framework.

### 🏗️ Architecture
* **Rewrite**: Complete migration from plain PHP to **Laravel 12** framework (MVC architecture).
* **Add**: Blade templating engine replacing raw PHP templates (172 old templates → organized Blade views).
* **Add**: **Eloquent ORM** with 27 models replacing raw SQL queries.
* **Add**: 25 dedicated controllers replacing 31 standalone request handlers and 38 PHP files.
* **Add**: Proper routing system (`routes/web.php`) replacing direct PHP file access.
* **Add**: Middleware system (`AdminMiddleware`, `SetLocale`, `UpdateUserOnline`, `CheckSystemVersion`, `InstallerGuard`).
* **Add**: CSRF protection on all forms via Laravel middleware.
* **Add**: Database migrations and seeders for structured schema management.

### 🎨 UI & Theme
* **Add**: **Duralux** admin panel theme (Bootstrap 5) replacing the old admin panel templates.
* **Improvement**: Modern landing page inspired by Google AdSense with animated sections, scroll-reveal effects, and stats counters.
* **Add**: Dark mode / Light mode toggle across the entire application.
* **Add**: RTL (Right-to-Left) support for Arabic language.
* **Improvement**: Responsive design across all pages (desktop, tablet, mobile).
* **Add**: Theme system — support for multiple switchable themes from the admin panel.

### 🔐 Authentication & Security
* **Add**: Laravel authentication system (login, register, logout, remember me).
* **Improvement**: Support for logging in via **Email or Username** automatically.
* **Add**: Social login via **Google** and **Facebook** OAuth.
* **Add**: Mandatory **Terms & Conditions** and **Privacy Policy** agreement on registration.
* **Add**: CAPTCHA verification on registration to prevent bots.
* **Improvement**: Password hashing using bcrypt (Laravel `Hash` facade).
* **Delete**: Insecure `wasp.gq` share and login integration removed.

### 🌍 Multilingual System
* **Improvement**: Complete translation system rewrite using Laravel's localization architecture.
* **Add**: **9 Languages** fully translated: English, Arabic, French, Spanish, Portuguese, Persian, Turkish, German, and Italian.
* **Add**: **Dynamic Language Discovery** — system automatically detects and adds new language folders to the UI.
* **Add**: Language management from the admin panel (sync, edit terms, export).
* **Add**: Dynamic locale switching via `SetLocale` middleware with session and cookie persistence.

### 📊 Admin Panel
* **Improvement**: Complete admin dashboard redesign with **Duralux** theme.
* **Add**: Admin Header Controls: Language switcher dropdown and "Back to Site" button for quick navigation.
* **Add**: Admin product management page (grid layout, view/delete products).
* **Add**: Admin emoji management (add/delete custom emojis).
* **Add**: Admin navigation menu management (add, edit, delete, reorder menus).
* **Add**: Admin widget management (create, edit, delete sidebar widgets with positioning).
* **Add**: Admin language management (add/edit/delete translation languages and terms).
* **Add**: Admin reports management page.
* **Add**: Admin news management page.
* **Add**: Admin knowledgebase management.
* **Add**: Admin cookie notice settings (enable/disable, colors, position).
* **Add**: Admin system settings page (site name, description, maintenance mode).
* **Add**: Admin theme management (switch between installed themes).
* **Improvement**: Admin stats page with detailed analytics.
* **Improvement**: Admin user management with profile editing.

### 🏪 Store
* **Add**: Market Categories filtering (Script, Templates, Plugins) — click a category to filter products.
* **Add**: Live product counts per category replacing "SOON" badges.
* **Add**: "All Products" reset button when category filter is active.
* **Improvement**: Product cards with seller profile links and category display.

### 🛡️ Legal & Compliance
* **Add**: Static **Privacy Policy** page (`/privacy`) — fully translated (EN/AR).
* **Add**: Static **Terms & Conditions** page (`/terms`) — fully translated (EN/AR).
* **Add**: **GDPR / CCPA Cookie Consent** banner with admin-customizable settings (colors, position, enable/disable).

### 🔌 Plugin System
* **Add**: Plugin architecture — install, activate, deactivate, and delete plugins from the admin panel.
* **Add**: Plugin file upload and auto-discovery system.

### 📢 Promote Page
* **Add**: Dedicated promote page for Banner Ads, Text Ads, Link Ads, and Exchange Visits.
* **Improvement**: Ad creation and management with point-based pricing.

### 🏠 Portal & Widgets
* **Add**: Configurable portal widgets system (sidebar left, sidebar right, content area).
* **Add**: Admin widget form for creating custom HTML/content widgets.
* **Improvement**: Portal page with dynamic widget rendering.

### 📝 Community & Forum
* **Improvement**: Community posts with Blade templates and proper MVC structure.
* **Improvement**: Comment system with emoji support.
* **Improvement**: Reaction system (like, love, dislike, etc.).
* **Improvement**: Forum topics and categories with admin-manageable ordering.
* **Add**: Forum moderation system (pin, lock topics).
* **Add**: Global and category-specific forum moderators.
* **Add**: Forum file attachments support.

### 🔗 Directory
* **Improvement**: Site directory with categorized listings.
* **Add**: Admin directory category management (add, edit, delete, reorder).

### 💬 Messages & Notifications
* **Improvement**: Private messaging system with real-time chat refresh.
* **Improvement**: Notification system for ads, follows, reactions, and comments.

### 📦 Other Changes
* **Add**: Sitemap generation with `SitemapController`.
* **Add**: News section with admin management.
* **Improvement**: User profile pages with follow/unfollow, posts, and activity tabs.
* **Improvement**: Visit exchange system with proper tracking.
* **Add**: Referral system improvements.
* **Delete**: Legacy `wasp.php` integration.
* **Update**: Font Awesome, Bootstrap, and all frontend libraries to latest versions.

# v3.2.0
* **Fix**: Multiple security bugs.
* **Delete**: Share and login from `wasp.gq`
* **Add**: Share via telegram
* **Add**: Added `<nav>` to divide posts in the member's profile
* **Add**: Friends page (following from both sides)
* **Improvement**: Multi-language system
* **Fix**: Update system to the latest version
* **Fix**: Multiple page issue in the forum
* **Improvement**: Visit Exchange System
* **Add**: Auto-copy feature when clicking on text boxes in `b_code.php`, `l_code.php` and `r_code.php` pages
* **Improvement**: message list
* **Add**: notifications when ads are blocked, enabled, or removed
* **Add**: a copy link button for community posts
* **Add**: The ability to publish works in the store in exchange for points (PTS).
* **Improvement**: UI and security in state and template files
# v3.1.0
* **Improvement**: Site directory
* **Update**: privacy policy
* **Fix**: Error in advertisement manager template
* **Improvement**: admin_nav & admin_home template
* **Addition**: Follow all posts or posts from those you follow in the community
* **Improvement**: Private message template
* **Improvement**: Community posts and images template
* **Fix**: Bugs and vulnerabilities
* **Addition**: The ability to arrange forum categories
* **Added**: Description of forum categories
# v3.0.6-beta
* Fix: security bugs.
* Added: PTS History.
* Fix: Syntax error in language files.
* Fix: Error in site directory notifications.
* Added: Button to report ads.
* Improve the template /follow.php
* Change: Twitter logo
* Update: Font Awesome library to version v6.4.2
* Improvement: The appearance of forum topics in the portal
# v3.0.5-beta
* Fix: Message template and add `reloadChat()` to even see incoming messages without resorting to reloading the page.
* Fix: Wrongly translated dates.
* Improvement: List of ad banners.
* Fix: Error with writing editor in store knowledge base.
* Fix: Error when a white page appears when entering a non-existent member page.
* Fix: The problem of entering a member profile whose name consists of numbers only.
* Add: Earn points by posting in the community and participating in posts and comments.
* Add: Stats Box widgets.
* Fix: Problem showing posts.
# v3.0.4-beta
* Add: French language
* fix: some bugs
# v3.0.3-beta
* Improvement: multi-language
* Fix: Some errors in templates
* Add: Number of views for text ads
# v3.0.2-beta
* Addition: Find out who interacted with the posts in the community
* Fix: Some errors in templates
# v3.0.1-beta
* Fix: Error on product update page
* Addition: The ability to change the user's password from the admin panel
* Fix: Error in the number of posts by the user on his personal page
* Fix: Some errors in templates
* FIX: Error editing ads when activating manager mode
* Fix: Error showing some components about visitors assigned to members (such as: the number of points and the edit box for posts)
* Improvement: Multilingualism
* Fix: Error calculating the number of incoming notifications and messages
* Addition: The option to block educational links from members and visitors to the site
* Improvement: Posts from plugin codes
* Fix: Error in admin panel when long links or long names appear
* Add: The ability to add footer ads.
* Add: human verification code when registering a new account
* Add: Widgets (suggested users) can be controlled from the admin panel widgets page
# v3.0.0-beta
* ***[Update]*** : in general appearance (updated all templates and updated Bootstrap library to v4.4.1
* ***[Add]*** : Publish text posts
* ***[Addition]*** : The system for interacting with publications (👍❤️👎☺️😄😮😡😢)
* ***[Delete]*** : Sharing posts between members to be rebuilt again in future versions
* ***[Improvement]*** : posting images within the community and removing the (beta) tag
* ***[Improvement]*** : the store, adding sections and deleting the (beta) tag
* ***[Improvement]*** : notifications and messages system
* ***[Improvement]*** : the community comments system and adding the ability to comment on links
* ***[Update]*** : Forum and Website Directory
* ***[Update]*** : User's personal page
* ***[Update]*** : Sitemap and rebuilding
# v2.4.6
* Fix : A problem with responsive ad banners appearing
* Various fixes and script preparation for the transition to the 3rd generation
# v2.4.5
* Fix some bugs during installation
* Some minor tweaks
# v2.4.4
* Fix : the problem of editing posts.
* Fix : multiple pages.
* Improve : the appearance of posts.
* Addition: Automatic update of the latest "myads" updates.
