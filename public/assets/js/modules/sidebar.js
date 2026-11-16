(function () {
    'use strict';

    const root = document.documentElement;
    const KEY = 'sf_sidebar_collapsed';
    const desktop = window.matchMedia('(min-width: 992px)');

    const toggles = document.querySelectorAll('[data-sf-toggle]');
    const backdrop = document.querySelector('[data-sf-backdrop]');

    function tersimpan() {
        try { return localStorage.getItem(KEY); } catch (e) { return null; }
    }

    function simpan(tutup) {
        try { localStorage.setItem(KEY, tutup ? '1' : '0'); } catch (e) { /* abaikan */ }
    }

    function atur(tutup, ingat) {
        root.classList.toggle('sf-collapsed', tutup);

        toggles.forEach(function (btn) {
            btn.setAttribute('aria-expanded', tutup ? 'false' : 'true');
        });

        // Pilihan hanya diingat di desktop
        if (ingat && desktop.matches) simpan(tutup);
    }

    function awal() {
        // Desktop: ikut pilihan terakhir (default terbuka). Layar kecil: default tertutup.
        atur(desktop.matches ? tersimpan() === '1' : true, false);
    }

    toggles.forEach(function (btn) {
        btn.addEventListener('click', function () {
            atur(!root.classList.contains('sf-collapsed'), true);
        });
    });

    if (backdrop) {
        backdrop.addEventListener('click', function () { atur(true, false); });
    }

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && !desktop.matches && !root.classList.contains('sf-collapsed')) {
            atur(true, false);
        }
    });

    desktop.addEventListener('change', awal);

    awal();

    // Aktifkan animasi setelah keadaan awal terpasang (menghindari kedip saat load)
    requestAnimationFrame(function () {
        requestAnimationFrame(function () { root.classList.add('sf-ready'); });
    });
})();