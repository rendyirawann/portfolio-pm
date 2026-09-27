{{--
    Resolves the colour theme before any CSS is parsed, so the first paint is
    already correct and there is no flash of the wrong theme.

    Dark is the default (portfolio palette). The choice lives in the same localStorage key that
    the admin panel's theme switcher writes, so picking dark there carries
    over to the sign-in and error screens.
--}}
<script>
    (function () {
        var KEY = 'data-bs-theme';
        var MIGRATED = 'app:theme-default-dark';
        var mode = 'dark';

        try {
            // Earlier builds forced dark on the sign-in screens, which left a
            // stale 'dark' in storage that would otherwise stick forever.
            // Clear it once; every explicit choice after that is respected.
            if (!localStorage.getItem(MIGRATED)) {
                localStorage.removeItem(KEY);
                localStorage.setItem(MIGRATED, '1');
            }

            var saved = localStorage.getItem(KEY);

            if (saved) {
                mode = saved;
            }

            if (mode === 'system') {
                mode = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
            }
        } catch (e) {
            /* Private mode / storage disabled — dark it is. */
        }

        document.documentElement.setAttribute('data-bs-theme', mode === 'dark' ? 'dark' : 'light');
    })();
</script>
