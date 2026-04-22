<?php
// 1. KONFIGURASI DATABASE
$host = "localhost";
$user = "root";
$pass = "";
$db   = "samuelkelontong";

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("Koneksi ke database gagal: " . mysqli_connect_error());
}

// 2. LOGIKA PEMROSESAN DATA (CRUD)
$pesan = "";

// Proses Simpan atau Update
if (isset($_POST['save'])) {
    $kode   = mysqli_real_escape_string($conn, $_POST['kodebrg']);
    $nama   = mysqli_real_escape_string($conn, $_POST['namabrg']);
    $satuan = mysqli_real_escape_string($conn, $_POST['satuan']);
    $harga  = $_POST['harga'];
    $stok   = $_POST['stok'];

    if ($_POST['status'] == 'edit') {
        $query = "UPDATE tbbarang SET namabrg='$nama', satuan='$satuan', harga='$harga', stok='$stok' WHERE kodebrg='$kode'";
        $pesan = mysqli_query($conn, $query) ? "Data berhasil diupdate!" : "Gagal update data.";
    } else {
        $query = "INSERT INTO tbbarang VALUES ('$kode','$nama','$satuan','$harga','$stok')";
        $pesan = mysqli_query($conn, $query) ? "Data berhasil disimpan!" : "Gagal simpan data (Kode Barang mungkin sudah ada).";
    }
}

// Proses Hapus
if (isset($_GET['hapus'])) {
    $kode = $_GET['hapus'];
    mysqli_query($conn, "DELETE FROM tbbarang WHERE kodebrg='$kode'");
    header("Location: index.php"); // Refresh halaman setelah hapus
}

// Persiapan Data untuk Edit
$edit_data = ['kodebrg'=>'','namabrg'=>'','satuan'=>'','harga'=>'','stok'=>''];
$status = 'tambah';
if (isset($_GET['edit'])) {
    $status = 'edit';
    $kode = $_GET['edit'];
    $res = mysqli_query($conn, "SELECT * FROM tbbarang WHERE kodebrg='$kode'");
    if (mysqli_num_rows($res) > 0) {
        $edit_data = mysqli_fetch_assoc($res);
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Samuel Kelontong - Inventory System</title>
    <style>
        /* Tampilan Mode CSS Abu dan Silver */
        body { 
            font-family: 'Segoe UI', Arial, sans-serif; 
            background-color: #d1d1d1; 
            margin: 0; 
            padding: 40px 20px; 
            color: #333; 
        }
        .container { 
            max-width: 900px; 
            margin: auto; 
            background: #ffffff; 
            padding: 30px; 
            border-radius: 15px; 
            box-shadow: 0 15px 35px rgba(0,0,0,0.2); 
            border-top: 8px solid #757575;
        }
        h2 { 
            text-align: center; 
            color: #424242; 
            text-transform: uppercase; 
            margin-bottom: 30px;
            letter-spacing: 1.5px;
        }
        
        /* Alert Message */
        .alert { padding: 10px; background: #e0e0e0; border-left: 5px solid #757575; margin-bottom: 20px; font-size: 14px; }

        /* Form Design */
        .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 20px; }
        .full-width { grid-column: span 2; }
        label { font-weight: bold; font-size: 13px; color: #616161; }
        input, select { 
            width: 100%; padding: 12px; margin-top: 5px; 
            border: 1px solid #bdbdbd; border-radius: 6px; 
            box-sizing: border-box; background: #fafafa;
        }
        input:focus { outline: none; border-color: #757575; background: #fff; }

        /* Button Styling */
        .btn { padding: 12px 25px; border: none; border-radius: 6px; cursor: pointer; font-weight: bold; transition: 0.3s; text-transform: uppercase; }
        .btn-save { background: linear-gradient(135deg, #424242, #757575); color: white; width: 100%; box-shadow: 0 4px 10px rgba(0,0,0,0.2); }
        .btn-save:hover { background: #212121; transform: translateY(-2px); }
        .btn-cancel { background: #bdbdbd; color: #424242; text-decoration: none; display: inline-block; text-align: center; margin-top: 10px; font-size: 12px; }

        /* Table Styling */
        .table-container { margin-top: 40px; overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; background: #fff; }
        th { background: #f5f5f5; color: #424242; padding: 15px; text-align: left; border-bottom: 2px solid #bdbdbd; }
        td { padding: 15px; border-bottom: 1px solid #eeeeee; font-size: 14px; }
        tr:hover { background-color: #f9f9f9; }
        
        /* Action Buttons */
        .btn-edit { color: #1976d2; text-decoration: none; margin-right: 10px; font-weight: bold; }
        .btn-hapus { color: #d32f2f; text-decoration: none; font-weight: bold; }
        .btn-edit:hover, .btn-hapus:hover { text-decoration: underline; }

        .price { font-family: 'Courier New', Courier, monospace; font-weight: bold; color: #444; }
    </style>
</head>
<body>

<div class="container">
    <h2>Data Barang Samuel Kelontong</h2>
    
    <?php if($pesan) echo "<div class='alert'>$pesan</div>"; ?>

    <form method="POST">
        <input type="hidden" name="status" value="<?= $status ?>">
        <div class="form-grid">
            <div>
                <label>Kode Barang</label>
                <input type="text" name="kodebrg" value="<?= $edit_data['kodebrg'] ?>" required <?= $status == 'edit' ? 'readonly' : '' ?> placeholder="BRG-XXXX">
            </div>
            <div>
                <label>Nama Barang</label>
                <input type="text" name="namabrg" value="<?= $edit_data['namabrg'] ?>" required>
            </div>
            <div>
                <label>Satuan</label>
                <select name="satuan">
                    <option value="Pcs" <?= $edit_data['satuan'] == 'Pcs' ? 'selected' : '' ?>>Pcs</option>
                    <option value="Box" <?= $edit_data['satuan'] == 'Box' ? 'selected' : '' ?>>Box</option>
                    <option value="Botol" <?= $edit_data['satuan'] == 'Botol' ? 'selected' : '' ?>>Botol</option>
                    <option value="Kg" <?= $edit_data['satuan'] == 'Kg' ? 'selected' : '' ?>>Kg</option>
                </select>
            </div>
            <div>
                <label>Stok</label>
                <input type="number" name="stok" value="<?= $edit_data['stok'] ?>" required>
            </div>
            <div class="full-width">
                <label>Harga Jual (Rp)</label>
                <input type="number" name="harga" value="<?= $edit_data['harga'] ?>" required>
            </div>
        </div>
        <button type="submit" name="save" class="btn btn-save">
            <?= $status == 'edit' ? 'Update Barang' : 'Simpan Barang Baru' ?>
        </button>
        <?php if($status == 'edit'): ?>
            <a href="index.php" class="btn-cancel">Batal Edit / Tambah Baru</a>
        <?php endif; ?>
    </form>

    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Kode</th>
                    <th>Nama Barang</th>
                    <th>Satuan</th>
                    <th>Harga</th>
                    <th>Stok</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $query = mysqli_query($conn, "SELECT * FROM tbbarang ORDER BY kodebrg ASC");
                if (mysqli_num_rows($query) == 0) {
                    echo "<tr><td colspan='6' style='text-align:center;'>Belum ada data barang.</td></tr>";
                } else {
                    while ($row = mysqli_fetch_array($query)) {
                        $harga_format = number_format($row['harga'], 0, ',', '.');
                        echo "<tr>
                            <td><strong>{$row['kodebrg']}</strong></td>
                            <td>{$row['namabrg']}</td>
                            <td>{$row['satuan']}</td>
                            <td class='price'>Rp $harga_format</td>
                            <td>{$row['stok']}</td>
                            <td>
                                <a href='index.php?edit={$row['kodebrg']}' class='btn-edit'>Edit</a>
                                <a href='index.php?hapus={$row['kodebrg']}' class='btn-hapus' onclick='return confirm(\"Hapus data barang {$row['namabrg']}?\")'>Hapus</a>
                            </td>
                        </tr>";
                    }
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>