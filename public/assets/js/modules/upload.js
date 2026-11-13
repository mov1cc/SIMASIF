(function () {
    'use strict';

    // Preview file sebelum diunggah (hanya bantuan UX, server tetap memvalidasi).
    document.querySelectorAll('[data-upload-input]').forEach(function (input) {
        const box = input.parentElement.querySelector('[data-upload-preview]');
        if (!box) return;

        const maks = Number(input.dataset.maksByte || 0);
        let urlSementara = null;

        function bersihkan() {
            if (urlSementara) {
                URL.revokeObjectURL(urlSementara);
                urlSementara = null;
            }
            box.textContent = '';
        }

        function ukuran(byte) {
            if (byte < 1024) return byte + ' B';
            if (byte < 1048576) return (byte / 1024).toFixed(1) + ' KB';
            return (byte / 1048576).toFixed(1) + ' MB';
        }

        input.addEventListener('change', function () {
            bersihkan();

            const file = input.files && input.files[0];
            if (!file) return;

            const info = document.createElement('div');
            info.className = 'small text-muted';
            info.textContent = file.name + ' (' + ukuran(file.size) + ')';

            if (maks > 0 && file.size > maks) {
                info.className = 'small text-danger';
                info.textContent += ' - melebihi batas ' + ukuran(maks) + '.';
                input.value = '';
                box.appendChild(info);
                return;
            }

            if (file.type.indexOf('image/') === 0) {
                urlSementara = URL.createObjectURL(file);
                const img = document.createElement('img');
                img.src = urlSementara;
                img.alt = '';
                img.className = 'rounded border mb-1 d-block';
                img.style.maxHeight = '160px';
                box.appendChild(img);
            }

            box.appendChild(info);
        });
    });
})();