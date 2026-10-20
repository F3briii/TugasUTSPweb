const toggleBtn = document.getElementById('toggleBtn');
const body = document.body;

toggleBtn.addEventListener('click', function () {
    if (body.getAttribute('data-theme') === 'dark') {
        body.setAttribute('data-theme', 'light');
        toggleBtn.textContent = '🌙';
    } else {
        body.setAttribute('data-theme', 'dark');
        toggleBtn.textContent = '☀️';
    }
});

$(document).ready(function () {
    $('#formBarang').submit(function (event) {
        const nama = $('#nama_barang').val().trim();
        const kategori = $('#kategori').val();
        const jumlah = $('#jumlah_stok').val();
        const harga = $('#harga').val();

        if (nama === '' || kategori === '' || jumlah === '' || harga === '') {
            alert('Semua data harus diisi!');
            event.preventDefault();
            return;
        }

        if (Number(jumlah) < 0 || Number(harga) < 0) {
            alert('Jumlah stok dan harga tidak boleh negatif!');
            event.preventDefault();
            return;
        }

        if (!confirm('Apakah kamu yakin ingin menyimpan barang ini?')) {
            event.preventDefault();
        }
    });
});