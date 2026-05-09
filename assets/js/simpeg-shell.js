(function () {
    var themeKey = 'simpeg-theme';

    function clearPageLeavingState() {
        document.body.classList.remove('page-leaving');
    }

    function getTheme() {
        try {
            return localStorage.getItem(themeKey) === 'dark' ? 'dark' : 'light';
        } catch (error) {
            return 'light';
        }
    }

    function updateThemeToggleState(isDark) {
        var toggles = document.querySelectorAll('[data-theme-toggle]');

        toggles.forEach(function (toggle) {
            toggle.setAttribute('aria-pressed', isDark ? 'true' : 'false');
        });

        document.querySelectorAll('[data-theme-toggle-text]').forEach(function (element) {
            element.textContent = isDark ? 'Light Mode' : 'Dark Mode';
        });

        document.querySelectorAll('[data-theme-toggle-icon]').forEach(function (element) {
            element.setAttribute('icon', isDark ? 'mdi:white-balance-sunny' : 'mdi:weather-night');
        });
    }

    function applyTheme(theme) {
        var isDark = theme === 'dark';
        document.documentElement.classList.toggle('theme-dark', isDark);

        try {
            localStorage.setItem(themeKey, isDark ? 'dark' : 'light');
        } catch (error) {
            // Ignore storage failures.
        }

        updateThemeToggleState(isDark);
    }

    function toggleTheme() {
        applyTheme(document.documentElement.classList.contains('theme-dark') ? 'light' : 'dark');
    }

    function bindThemeToggle() {
        document.querySelectorAll('[data-theme-toggle]').forEach(function (toggle) {
            toggle.addEventListener('click', toggleTheme);
        });
    }

    function bindSidebarToggle() {
        document.querySelectorAll('[data-sidebar-toggle]').forEach(function (button) {
            button.addEventListener('click', function () {
                document.body.classList.toggle('sidebar-open');
            });
        });

        document.querySelectorAll('[data-sidebar-overlay]').forEach(function (overlay) {
            overlay.addEventListener('click', function () {
                document.body.classList.remove('sidebar-open');
            });
        });
    }

    function bindLogoutLoading() {
        document.querySelectorAll('[data-logout-link]').forEach(function (link) {
            link.addEventListener('click', function (event) {
                if (document.body.classList.contains('page-leaving')) {
                    event.preventDefault();
                    return;
                }

                event.preventDefault();
                document.body.classList.add('page-leaving');

                if (window.Swal && typeof window.Swal.fire === 'function') {
                    window.Swal.fire({
                        title: 'Sedang keluar...',
                        text: 'Mohon tunggu sebentar.',
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        showConfirmButton: false,
                        didOpen: function () {
                            window.Swal.showLoading();
                        }
                    });
                }

                window.setTimeout(function () {
                    window.location.href = link.href;
                }, 380);
            });
        });
    }

    function bindPageTransition() {
        document.addEventListener('click', function (event) {
            var link = event.target.closest('a[href]');

            if (!link || document.body.classList.contains('page-leaving')) {
                return;
            }

            var href = link.getAttribute('href') || '';

            if (
                event.defaultPrevented ||
                link.target === '_blank' ||
                (link.target && link.target !== '' && link.target !== '_self') ||
                link.hasAttribute('download') ||
                link.hasAttribute('data-no-transition') ||
                href === '' ||
                href.charAt(0) === '#' ||
                href.indexOf('javascript:') === 0 ||
                link.hasAttribute('data-logout-link') ||
                link.getAttribute('data-toggle') === 'collapse' ||
                event.ctrlKey ||
                event.metaKey ||
                event.shiftKey ||
                event.altKey
            ) {
                return;
            }

            var targetUrl = new URL(link.href, window.location.href);

            if (targetUrl.origin !== window.location.origin) {
                return;
            }

            if (targetUrl.href === window.location.href) {
                return;
            }

            event.preventDefault();
            document.body.classList.add('page-leaving');

            window.setTimeout(function () {
                if (document.visibilityState === 'visible') {
                    clearPageLeavingState();
                }
            }, 1200);

            window.setTimeout(function () {
                window.location.href = targetUrl.href;
            }, 200);
        });
    }

    document.addEventListener('DOMContentLoaded', function () {
        clearPageLeavingState();
        applyTheme(getTheme());
        bindThemeToggle();
        bindSidebarToggle();
        bindLogoutLoading();
        bindPageTransition();
    });

    window.addEventListener('pageshow', clearPageLeavingState);
    window.addEventListener('focus', function () {
        window.setTimeout(clearPageLeavingState, 50);
    });
})();
