import './bootstrap';

import * as bootstrap from 'bootstrap';
import AOS from 'aos';

// Exposed on window because resources/views/profile/edit.blade.php calls
// `new bootstrap.Modal(...)` from a plain inline <script> tag, not a module —
// same as when Bootstrap's JS was loaded from the CDN as a global <script>.
window.bootstrap = bootstrap;

// Moved from layouts/app.blade.php's inline <script>. Same config as
// before, now running as a module import instead of the AOS CDN <script>.
document.addEventListener('DOMContentLoaded', function () {
    AOS.init({
        mirror: true, // Enable reverse animations
        once: false, // Whether animation should happen only once
        offset: 120, // Offset (in px) from the original trigger point
        duration: 1000, // Duration of animation
        easing: 'ease-in-out', // Default easing for AOS animations
        anchorPlacement: 'top-bottom', // Defines which position of the element regarding to window should trigger the animation
    });
});

// Re-show the full-page loader (see layouts/app.blade.php + app.css) right
// before a *real* full-page navigation, so the brief blank gap between
// unload and the next page's first paint gets a branded overlay instead of
// a flash of white. The initial hide-on-load is handled separately by a
// small inline script in the layout itself (kept independent of this
// bundle on purpose — see that script's comment).
//
// Both listeners are on `document` (bubble phase), which — regardless of
// script load order — always runs *after* any listener attached directly
// to the link/form itself (target phase fires first). So checking
// e.defaultPrevented here correctly skips every AJAX flow that already
// called preventDefault() on its own form/element (add-to-cart, checkout
// quantity updates, rating submission's client-side guard, etc.) without
// this file needing to know about any of them individually.
document.addEventListener('DOMContentLoaded', function () {
    const loader = document.getElementById('page-loader');
    if (!loader) return;

    function showLoader() {
        loader.classList.remove('page-loader-hidden');
    }

    document.addEventListener('click', function (e) {
        if (e.defaultPrevented || e.button !== 0 || e.metaKey || e.ctrlKey || e.shiftKey || e.altKey) {
            return;
        }

        const link = e.target.closest('a[href]');
        if (!link) return;

        const href = link.getAttribute('href');
        if (!href || href.startsWith('#') || href.startsWith('javascript:') || href.startsWith('mailto:') || href.startsWith('tel:')) {
            return;
        }
        if (link.hasAttribute('download') || link.target === '_blank') return;

        // Same-page hash link (e.g. footer's "Beranda#persona-section" when
        // already on that page) — no real navigation, so no loader.
        if (link.origin === window.location.origin && link.pathname === window.location.pathname && href.includes('#')) {
            return;
        }

        showLoader();
    });

    document.addEventListener('submit', function (e) {
        if (e.defaultPrevented) return;
        if (e.target instanceof HTMLFormElement && e.target.target === '_blank') return;
        showLoader();
    });

    // Coming back via the browser's back/forward cache restores the page
    // exactly as it was — including a hidden loader — so nothing to do;
    // but if it restores mid-navigation (rare), make sure it isn't stuck.
    window.addEventListener('pageshow', function (e) {
        if (e.persisted) {
            loader.classList.add('page-loader-hidden');
        }
    });
});
