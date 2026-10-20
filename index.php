<?php
require "koneksi.php";

$pesan = "";
$jenisPesan = "";

if (isset($_POST["simpan"])) {
    $nama_barang = trim($_POST["nama_barang"]);
    $kategori = trim($_POST["kategori"]);
    $jumlah_stok = $_POST["jumlah_stok"];
    $harga = $_POST["harga"];

    if ($nama_barang == "" || $kategori == "" || $jumlah_stok == "" || $harga == "") {
        $pesan = "Semua data harus diisi!";
        $jenisPesan = "danger";
    } elseif (!is_numeric($jumlah_stok) || !is_numeric($harga) || $jumlah_stok < 0 || $harga < 0) {
        $pesan = "Jumlah stok dan harga harus berupa angka positif!";
        $jenisPesan = "danger";
    } else {
        $jumlah_stok = (int) $jumlah_stok;
        $harga = (int) $harga;

        $query = $conn->prepare(
            "INSERT INTO barang (nama_barang, kategori, jumlah_stok, harga)
             VALUES (?, ?, ?, ?)"
        );

        $query->bind_param("ssii", $nama_barang, $kategori, $jumlah_stok, $harga);

        if ($query->execute()) {
        header("Location: index.php?status=berhasil");
        exit;
        } else {
        $pesan = "Data barang gagal ditambahkan!";
        $jenisPesan = "danger";
}

        $query->close();
    }
}

$queryStatistik = $conn->query(
    "SELECT COUNT(*) AS total_barang, SUM(jumlah_stok) AS total_stok
     FROM barang"
);

$statistik = $queryStatistik->fetch_assoc();

$total_barang = $statistik["total_barang"];
$total_stok = $statistik["total_stok"] ?? 0;

$queryBarang = $conn->query("SELECT * FROM barang ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Manajemen Stok Barang</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>

<body data-theme="dark">

    <header>
        <nav class="navbar navbar-expand-lg navbar-dark">
            <div class="container">
                <a class="logo" href="#">Stok Kopdes<span> Merah Putih</span></a>

                <button type="button" id="toggleBtn" class="btn btn-outline-light">
                    🌙
                </button>
            </div>
        </nav>
    </header>

    <main class="container halaman-utama">

        <section class="bagian-judul">
            <p class="teks-keterangan">Kelola data dan persediaan barang dengan mudah.</p>
        </section>

        <?php if ($pesan != "") { ?>
            <div class="alert alert-<?php echo $jenisPesan; ?>">
                <?php echo htmlspecialchars($pesan); ?>
            </div>
        <?php } ?>

        <section class="row bagian-statistik">
            <div class="col">
                <div class="card kotak-statistik">
                    <h5>Total Jenis Barang</h5>
                    <h2><?php echo $total_barang; ?></h2>
                </div>
            </div>

            <div class="col">
                <div class="card kotak-statistik">
                    <h5>Total Seluruh Stok</h5>
                    <h2><?php echo $total_stok; ?></h2>
                </div>
            </div>
        </section>

        <section class="card form-barang">
            <h2 class="judul-bagian">Tambah Barang</h2>

            <form action="index.php" method="POST" id="formBarang">
                <div class="input-barang">
                    <label for="nama_barang" class="form-label">Nama Barang</label>
                    <input type="text" class="form-control" id="nama_barang"
                           name="nama_barang" required>
                </div>

                <div class="input-barang">
                    <label for="kategori" class="form-label">Kategori</label>
                    <select class="form-select" id="kategori" name="kategori" required>
                        <option value="">Pilih kategori</option>
                        <option value="Makanan">Makanan</option>
                        <option value="Minuman">Minuman</option>
                        <option value="Peralatan">Peralatan</option>
                        <option value="Lainnya">Lainnya</option>
                    </select>
                </div>

                <div class="input-barang">
                    <label for="jumlah_stok" class="form-label">Jumlah Stok</label>
                    <input type="number" class="form-control" id="jumlah_stok"
                           name="jumlah_stok" min="0" required>
                </div>

                <div class="input-barang">
                    <label for="harga" class="form-label">Harga Barang (Rp)</label>
                    <input type="number" class="form-control" id="harga"
                           name="harga" min="0" required>
                </div>

                <button type="submit" name="simpan" class="btn btn-primary">
                    Simpan Barang
                </button>
            </form>
        </section>

        <section class="card bagian-tabel">
            <h2 class="judul-bagian">Data Stok Barang</h2>

            <div class="table-responsive">
                <table class="table tabel-barang">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Barang</th>
                            <th>Kategori</th>
                            <th>Jumlah</th>
                            <th>Harga</th>
                            <th>Status</th>
                            <th>Tanggal</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php if ($queryBarang->num_rows > 0) { ?>
                            <?php $no = 1; ?>

                            <?php while ($barang = $queryBarang->fetch_assoc()) { ?>
                                <tr>
                                    <td><?php echo $no++; ?></td>
                                    <td><?php echo htmlspecialchars($barang["nama_barang"]); ?></td>
                                    <td><?php echo htmlspecialchars($barang["kategori"]); ?></td>
                                    <td><?php echo $barang["jumlah_stok"]; ?></td>
                                    <td>Rp<?php echo number_format($barang["harga"], 0, ",", "."); ?></td>

                                    <td>
                                        <?php if ($barang["jumlah_stok"] < 5) { ?>
                                            <span class="badge bg-danger">Stok Menipis</span>
                                        <?php } else { ?>
                                            <span class="badge bg-success">Stok Aman</span>
                                        <?php } ?>
                                    </td>

                                    <td><?php echo htmlspecialchars($barang["tanggal"]); ?></td>
                                </tr>
                            <?php } ?>

                        <?php } else { ?>
                            <tr>
                                <td colspan="7" class="pesan-kosong">
                                    Belum ada data barang.
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </section>

    </main>

    <footer class="footer-halaman">
        <p>Mohamad Febri Pratama - 252410103039</p>
    </footer>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="script.js"></script>
</body>
</html>