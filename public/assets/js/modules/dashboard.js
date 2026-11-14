(function () {
    'use strict';

    const root = document.querySelector('[data-dashboard-owner]');
    if (!root || typeof Chart === 'undefined') return;

    const rupiah = new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        maximumFractionDigits: 0,
    });

    const WARNA = ['#212529', '#0d6efd', '#198754', '#ffc107', '#6c757d'];

    const el = function (id) { return document.getElementById(id); };

    // "2026-11-14" -> "14/11"
    function labelTanggal(ymd) {
        const p = ymd.split('-');
        return p[2] + '/' + p[1];
    }

    function tampilKosong(wrapId, kosongId, kosong) {
        el(wrapId).classList.toggle('d-none', kosong);
        el(kosongId).classList.toggle('d-none', !kosong);
    }

    function gambarPendapatan(data) {
        const ada = data.values.some(function (v) { return v > 0; });
        tampilKosong('pendapatanWrap', 'pendapatanKosong', !ada);
        if (!ada) return;

        new Chart(el('chartPendapatan'), {
            type: 'line',
            data: {
                labels: data.labels.map(labelTanggal),
                datasets: [{
                    label: 'Pendapatan',
                    data: data.values,
                    borderColor: '#212529',
                    backgroundColor: 'rgba(33, 37, 41, 0.08)',
                    fill: true,
                    tension: 0.3,
                    pointRadius: data.values.length > 35 ? 0 : 3,
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: function (ctx) { return rupiah.format(ctx.parsed.y); },
                        },
                    },
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function (v) { return rupiah.format(v); },
                        },
                    },
                    x: { ticks: { maxTicksLimit: 12 } },
                },
            },
        });
    }

    function gambarPopuler(data) {
        const ada = data.values.length > 0;
        tampilKosong('populerWrap', 'populerKosong', !ada);
        if (!ada) return;

        new Chart(el('chartPopuler'), {
            type: 'doughnut',
            data: {
                labels: data.labels,
                datasets: [{
                    data: data.values,
                    backgroundColor: WARNA.slice(0, data.values.length),
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom' },
                    tooltip: {
                        callbacks: {
                            label: function (ctx) {
                                return ctx.label + ': ' + ctx.parsed + ' pemesanan';
                            },
                        },
                    },
                },
            },
        });
    }

    async function muat() {
        const url = '/owner/dashboard/data?periode=' + encodeURIComponent(root.dataset.periode);

        try {
            const res = await fetch(url, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                cache: 'no-store',
            });
            if (!res.ok) throw new Error('HTTP ' + res.status);

            const data = await res.json();
            gambarPendapatan(data.pendapatan);
            gambarPopuler(data.populer);
        } catch (e) {
            tampilKosong('pendapatanWrap', 'pendapatanKosong', true);
            tampilKosong('populerWrap', 'populerKosong', true);
            el('pendapatanKosong').textContent = 'Grafik gagal dimuat. Muat ulang halaman.';
            el('populerKosong').textContent = 'Grafik gagal dimuat. Muat ulang halaman.';
        }
    }

    muat();
})();