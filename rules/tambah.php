    <?php
    include "../koneksi.php";

    // Ambil list gejala untuk checkboxes
    $gejala_list = mysqli_query($conn, "SELECT * FROM gejala ORDER BY kode_gejala ASC");

    // Ambil list part untuk dropdown
    $part_list = mysqli_query($conn, "SELECT * FROM part_kendaraan ORDER BY kode_part ASC");

    $error = '';
    if (isset($_POST['submit'])) {
        $kode_rule = strtoupper(trim($_POST['kode_rule']));
        $id_part = intval($_POST['id_part']);
        $keputusan = trim($_POST['keputusan']);
        $selected_gejala = isset($_POST['gejala']) ? $_POST['gejala'] : []; // Array of id_gejala

        if (empty($kode_rule) || $id_part <= 0 || empty($keputusan) || empty($selected_gejala)) {
            $error = 'Semua field wajib diisi dan minimal 1 gejala harus dipilih!';
        } else {
            // Cek apakah kode_rule sudah digunakan
            $check_stmt = mysqli_prepare($conn, "SELECT id_rule FROM rules WHERE kode_rule = ?");
            mysqli_stmt_bind_param($check_stmt, "s", $kode_rule);
            mysqli_stmt_execute($check_stmt);
            mysqli_stmt_store_result($check_stmt);

            if (mysqli_stmt_num_rows($check_stmt) > 0) {
                $error = 'Kode Rule sudah terdaftar, silakan gunakan kode lain.';
            } else {
                // Simpan rule baru (satu baris untuk setiap gejala terpilih)
                $success = true;
                $stmt = mysqli_prepare($conn, "INSERT INTO rules (kode_rule, id_gejala, id_part, keputusan, status) VALUES (?, ?, ?, ?, ?)");
                
                foreach ($selected_gejala as $id_g) {
                    $id_g_int = intval($id_g);
                    mysqli_stmt_bind_param($stmt, "siiss", $kode_rule, $id_g_int, $id_part, $keputusan, $status);
                    if (!mysqli_stmt_execute($stmt)) {
                        $success = false;
                    }
                }
                mysqli_stmt_close($stmt);

                if ($success) {
                    header("Location: index.php?status=sukses_tambah");
                    exit;
                } else {
                    $error = 'Gagal menyimpan data rule.';
                }
            }
            mysqli_stmt_close($check_stmt);
        }
    }

    include "../templates/header.php";
    ?>

    <div class="row mb-4">
        <div class="col-md-8 mx-auto">
            <a href="index.php" class="btn btn-outline-secondary btn-sm mb-3">
                <i class="bi bi-arrow-left"></i> Kembali ke Daftar
            </a>
            <h2>Tambah Rule Baru</h2>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card shadow-sm border-0 p-4">
                <?php if ($error != ''): ?>
                    <div class="alert alert-danger" role="alert">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i> <?php echo htmlspecialchars($error); ?>
                    </div>
                <?php endif; ?>

                <form method="POST" action="">
                    <div class="mb-3">
                        <label for="kode_rule" class="form-label fw-semibold">Kode Rule</label>
                        <input type="text" name="kode_rule" id="kode_rule" class="form-control" placeholder="masukan kode rule" required value="<?php echo isset($_POST['kode_rule']) ? htmlspecialchars($_POST['kode_rule']) : ''; ?>">
                    </div>

                    <div class="mb-3">
                        <label for="id_part" class="form-label fw-semibold">Pilih Part Kendaraan Terkait</label>
                        <select name="id_part" id="id_part" class="form-select" required>
                            <option value="" disabled selected></option>
                            <?php while ($pt = mysqli_fetch_assoc($part_list)): ?>
                                <option value="<?php echo $pt['id_part']; ?>" <?php echo (isset($_POST['id_part']) && $_POST['id_part'] == $pt['id_part']) ? 'selected' : ''; ?>>
                                    [<?php echo htmlspecialchars($pt['kode_part']); ?>] <?php echo htmlspecialchars($pt['nama_part']); ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <!--
                    <div class="mb-3">
                        <label for="status" class="form-label fw-semibold">Status Uji Kelayakan</label>
                        <select name="status" id="status" class="form-select" required>
                            <option value="TIDAK LOLOS" <?php echo (isset($_POST['status']) && $_POST['status'] == 'TIDAK LOLOS') ? 'selected' : ''; ?>>TIDAK LOLOS</option>
                            <option value="LOLOS" <?php echo (isset($_POST['status']) && $_POST['status'] == 'LOLOS') ? 'selected' : ''; ?>>LOLOS</option>
                        </select>
                    </div>
                    -->
                    <div class="mb-3">
                        <label for="keputusan" class="form-label fw-semibold">Hasil Diagnosa</label>
                        <textarea name="keputusan" id="keputusan" class="form-control" rows="3" placeholder=><?php echo isset($_POST['keputusan']) ? htmlspecialchars($_POST['keputusan']) : ''; ?></textarea>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold d-block">Pilih Gejala</label>
                        <div class="border rounded p-3 bg-light" style="max-height: 250px; overflow-y: auto;">
                            <?php if (mysqli_num_rows($gejala_list) > 0): ?>
                                <?php while ($gj = mysqli_fetch_assoc($gejala_list)): ?>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" name="gejala[]" value="<?php echo $gj['id_gejala']; ?>" id="gj_<?php echo $gj['id_gejala']; ?>" <?php echo (isset($_POST['gejala']) && in_array($gj['id_gejala'], $_POST['gejala'])) ? 'checked' : ''; ?>>
                                        <label class="form-check-label text-dark fw-medium small" style="cursor: pointer;" for="gj_<?php echo $gj['id_gejala']; ?>">
                                            <span class="badge bg-warning text-dark me-1"><?php echo htmlspecialchars($gj['kode_gejala']); ?></span> - <?php echo htmlspecialchars($gj['nama_gejala']); ?>
                                        </label>
                                    </div>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <p class="text-muted mb-0 small">Belum ada data gejala terdaftar.</p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="mt-4 text-end">
                        <button type="reset" class="btn btn-light me-2">Reset</button>
                        <button type="submit" name="submit" class="btn btn-primary px-4">
                            <i class="bi bi-save"></i> Simpan Rule
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <?php
    include "../templates/footer.php";
    ?>
