(function () {
    'use strict';

    const root = document.querySelector('[data-tracking]');
    if (!root) return;

    // Sudah di tahap akhir: tidak ada yang perlu dipantau
    if (root.dataset.status === 'selesai') return;

    const INTERVAL_MS = 30000;
    const url = '/tracking/data?kode=' + encodeURIComponent(root.dataset.kode);

    async function cek() {
        if (document.hidden) return;

        try {
            const res = await fetch(url, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                cache: 'no-store',
            });
            if (!res.ok) return;

            const data = await res.json();
            const pembayaran = data.status_pembayaran || '';

            if (data.status !== root.dataset.status || pembayaran !== root.dataset.pembayaran) {
                window.location.reload();
            }
        } catch (e) {
            // Gagal sesaat (koneksi putus): coba lagi pada putaran berikutnya
        }
    }

    setInterval(cek, INTERVAL_MS);
})();