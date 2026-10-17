import openpyxl
from openpyxl.styles import Font, PatternFill, Alignment, Border, Side
from openpyxl.utils import get_column_letter
from pathlib import Path
import json

OUTPUT_PATH = Path(r"d:\Project PKL\qa-reports\whitebox\LAPORAN-WHITEBOX.xlsx")
MIRROR_PATH = Path(r"d:\Project PKL\SignalNiagaJasaTender\qa-reports\whitebox\LAPORAN-WHITEBOX.xlsx")

def create_report():
    wb = openpyxl.Workbook()
    # remove default sheet
    wb.remove(wb.active)

    # Styles
    font_family = "Segoe UI"
    
    header_fill = PatternFill(start_color="1F497D", end_color="1F497D", fill_type="solid") # Dark Navy Blue
    header_font = Font(name=font_family, size=11, bold=True, color="FFFFFF")
    
    title_font = Font(name=font_family, size=15, bold=True, color="1F497D")
    subtitle_font = Font(name=font_family, size=10, italic=True, color="595959")
    section_font = Font(name=font_family, size=12, bold=True, color="1F497D")
    
    bold_font = Font(name=font_family, size=10, bold=True)
    regular_font = Font(name=font_family, size=10)
    kpi_val_font = Font(name=font_family, size=16, bold=True, color="1F497D")
    
    thin_border = Border(
        left=Side(style='thin', color='D9D9D9'),
        right=Side(style='thin', color='D9D9D9'),
        top=Side(style='thin', color='D9D9D9'),
        bottom=Side(style='thin', color='D9D9D9')
    )
    
    card_border = Border(
        left=Side(style='medium', color='1F497D'),
        right=Side(style='medium', color='1F497D'),
        top=Side(style='medium', color='1F497D'),
        bottom=Side(style='medium', color='1F497D')
    )

    align_center = Alignment(horizontal='center', vertical='center', wrap_text=True)
    align_left = Alignment(horizontal='left', vertical='center', wrap_text=True)
    align_right = Alignment(horizontal='right', vertical='center', wrap_text=True)
    
    # Fills for severity and status
    fill_critical = PatternFill(start_color="FCE4D6", end_color="FCE4D6", fill_type="solid") # Light Red
    font_critical = Font(name=font_family, size=10, bold=True, color="C00000")
    
    fill_high = PatternFill(start_color="FFF2CC", end_color="FFF2CC", fill_type="solid") # Light Orange
    font_high = Font(name=font_family, size=10, bold=True, color="B25900")
    
    fill_medium = PatternFill(start_color="FFF9E6", end_color="FFF9E6", fill_type="solid") # Light Yellow
    font_medium = Font(name=font_family, size=10, bold=True, color="7F6000")
    
    fill_low = PatternFill(start_color="E2EFDA", end_color="E2EFDA", fill_type="solid") # Light Green
    font_low = Font(name=font_family, size=10, bold=True, color="375623")
    
    fill_pass = PatternFill(start_color="E2EFDA", end_color="E2EFDA", fill_type="solid")
    font_pass = Font(name=font_family, size=10, bold=True, color="375623")
    
    fill_fail = PatternFill(start_color="FCE4D6", end_color="FCE4D6", fill_type="solid")
    font_fail = Font(name=font_family, size=10, bold=True, color="C00000")

    fill_alert = PatternFill(start_color="FCE4D6", end_color="FCE4D6", fill_type="solid")
    font_alert = Font(name=font_family, size=10, bold=True, color="C00000")

    # =========================================================================
    # 1. SHEET: Ringkasan
    # =========================================================================
    ws1 = wb.create_sheet(title="Ringkasan")
    ws1.views.sheetView[0].showGridLines = True
    
    # Title
    ws1["A1"] = "LAPORAN EXECUTIVE WHITEBOX TESTING & QUALITY AUDIT"
    ws1["A1"].font = title_font
    ws1["A2"] = "Sistem SignalNiagaJasaTender (Laravel 10 + MySQL / SQLite Testing Env)"
    ws1["A2"].font = subtitle_font
    
    # KPI Section: Test Results
    ws1["A4"] = "HASIL TEST EXISTING"
    ws1["A4"].font = section_font
    
    headers_kpi1 = ["Metrik", "Nilai", "Keterangan"]
    for col_idx, h in enumerate(headers_kpi1, 1):
        cell = ws1.cell(row=5, column=col_idx, value=h)
        cell.font = header_font
        cell.fill = header_fill
        cell.alignment = align_center
        cell.border = thin_border
        
    kpi_tests = [
        ("Total Test Existing", "=COUNTA('Hasil Test'!C2:C16)", "Total skenario pengujian di codebase"),
        ("Test Lulus (Pass)", "=COUNTIF('Hasil Test'!D2:D16, \"Pass\")", "Unit tests yang berhasil dieksekusi"),
        ("Test Gagal (Fail)", "=COUNTIF('Hasil Test'!D2:D16, \"Fail\")", "Gagal karena host PHP CLI belum memuat pdo_sqlite"),
        ("Test Dilewati (Skip)", "=COUNTIF('Hasil Test'!D2:D16, \"Skip\")", "Tidak ada test yang diskip"),
        ("Tingkat Kelulusan", "=B7/B6", "Persentase kelulusan test existing")
    ]
    
    for r_idx, (m, form, desc) in enumerate(kpi_tests, 6):
        c1 = ws1.cell(row=r_idx, column=1, value=m)
        c2 = ws1.cell(row=r_idx, column=2, value=form)
        c3 = ws1.cell(row=r_idx, column=3, value=desc)
        c1.font = bold_font; c1.border = thin_border; c1.alignment = align_left
        c2.font = bold_font; c2.border = thin_border; c2.alignment = align_center
        c3.font = regular_font; c3.border = thin_border; c3.alignment = align_left
        if r_idx == 10:
            c2.number_format = "0.0%"
            
    # KPI Section: Temuan Kode & Keamanan
    ws1["E4"] = "DISTRIBUSI TEMUAN SEVERITY (FORMULA EXCEL)"
    ws1["E4"].font = section_font
    
    headers_kpi2 = ["Tingkat Keparahan", "Jumlah Temuan", "Target Penanganan"]
    for col_idx, h in enumerate(headers_kpi2, 5):
        cell = ws1.cell(row=5, column=col_idx, value=h)
        cell.font = header_font
        cell.fill = header_fill
        cell.alignment = align_center
        cell.border = thin_border
        
    kpi_findings = [
        ("Critical", "=COUNTIF(Temuan!B2:B20, \"Critical\")", "Wajib ditangani segera (BOLA API, Bypass Otorisasi)", fill_critical, font_critical),
        ("High", "=COUNTIF(Temuan!B2:B20, \"High\")", "Wajib ditangani sebelum rilis (Upload File, Stock Race)", fill_high, font_high),
        ("Medium", "=COUNTIF(Temuan!B2:B20, \"Medium\")", "Prioritas sprint berikutnya (N+1, Random collision)", fill_medium, font_medium),
        ("Low", "=COUNTIF(Temuan!B2:B20, \"Low\")", "Perbaikan berkala & kebersihan kode (Pint style, Hardcoded rate)", fill_low, font_low),
        ("Total Temuan", "=SUM(F6:F9)", "Total akumulasi temuan kualitas & keamanan kode", None, bold_font)
    ]
    
    for r_idx, (sev, form, desc, fill, font) in enumerate(kpi_findings, 6):
        c1 = ws1.cell(row=r_idx, column=5, value=sev)
        c2 = ws1.cell(row=r_idx, column=6, value=form)
        c3 = ws1.cell(row=r_idx, column=7, value=desc)
        c1.font = font or bold_font; c1.border = thin_border; c1.alignment = align_left
        c2.font = font or bold_font; c2.border = thin_border; c2.alignment = align_center
        c3.font = regular_font; c3.border = thin_border; c3.alignment = align_left
        if fill:
            c1.fill = fill
            c2.fill = fill

    # Lingkungan & Tool Section
    ws1["A12"] = "LINGKUNGAN PENGUJIAN & TOOLCHAIN"
    ws1["A12"].font = section_font
    
    headers_env = ["Komponen / Tool", "Versi Terdeteksi", "Tujuan / Perintah"]
    for col_idx, h in enumerate(headers_env, 1):
        cell = ws1.cell(row=13, column=col_idx, value=h)
        cell.font = header_font
        cell.fill = header_fill
        cell.alignment = align_center
        cell.border = thin_border
        
    env_rows = [
        ("PHP CLI Runtime", "PHP 8.1.10 (x64 ZTS, VS16)", "php -v (Laragon environment)"),
        ("Laravel Framework", "10.48.28", "php artisan --version"),
        ("Test Runner", "PHPUnit 10.5.45", "php artisan test"),
        ("Linter & Code Style", "Laravel Pint 1.21.0", "php vendor/bin/pint --test"),
        ("Dependency Auditor (PHP)", "Composer 2.8.12", "composer audit"),
        ("Dependency Auditor (JS)", "NPM 11.13.0 / Node v20+", "npm audit"),
        ("Database Engine", "MySQL 8.0.30 & SQLite In-Memory", "Database operasional & DB unit testing"),
        ("Static Code Analyzer", "Python 3.13.0 Custom AST Parser", "Analisis Kompleksitas Siklomatik & Pattern Scanner")
    ]
    for r_idx, (k, v, cmd) in enumerate(env_rows, 14):
        c1 = ws1.cell(row=r_idx, column=1, value=k)
        c2 = ws1.cell(row=r_idx, column=2, value=v)
        c3 = ws1.cell(row=r_idx, column=3, value=cmd)
        c1.font = bold_font; c1.border = thin_border; c1.alignment = align_left
        c2.font = regular_font; c2.border = thin_border; c2.alignment = align_left
        c3.font = regular_font; c3.border = thin_border; c3.alignment = align_left
        
    # Overall Risk Rating & Executive Summary
    ws1["E12"] = "PENILAIAN RISIKO KESELURUHAN (OVERALL RISK SCORE)"
    ws1["E12"].font = section_font
    
    headers_risk = ["Dimensi Evaluasi", "Rating", "Catatan Rekomendasi"]
    for col_idx, h in enumerate(headers_risk, 5):
        cell = ws1.cell(row=13, column=col_idx, value=h)
        cell.font = header_font
        cell.fill = header_fill
        cell.alignment = align_center
        cell.border = thin_border
        
    risk_rows = [
        ("Skor Risiko Keseluruhan", "TINGGI (HIGH RISK)", "Ditemukan celah otorisasi API kritis dan celah upload dokumen."),
        ("Kesiapan Test Suite", "RENDAH (LOW COVERAGE)", "14 dari 15 test gagal akibat dependensi driver SQLite pada CLI host."),
        ("Keamanan Dependensi", "SEDANG (MEDIUM)", "8 advisory pada composer (termasuk CVE CRLF) & 5 npm vulnerabilities."),
        ("Kualitas & Kompleksitas", "SEDANG (MEDIUM)", "25 fungsi memiliki CC > 10 (tertinggi SalesService CC 51)."),
        ("Integritas Transaksi", "PERLU PERBAIKAN", "Cek stok dan pembayaran belum menggunakan pessimistic locking.")
    ]
    for r_idx, (d, rat, n) in enumerate(risk_rows, 14):
        c1 = ws1.cell(row=r_idx, column=5, value=d)
        c2 = ws1.cell(row=r_idx, column=6, value=rat)
        c3 = ws1.cell(row=r_idx, column=7, value=n)
        c1.font = bold_font; c1.border = thin_border; c1.alignment = align_left
        c2.font = font_critical if "TINGGI" in rat else bold_font; c2.border = thin_border; c2.alignment = align_center
        c3.font = regular_font; c3.border = thin_border; c3.alignment = align_left
        if "TINGGI" in rat:
            c2.fill = fill_critical
        elif "RENDAH" in rat or "PERLU" in rat:
            c2.fill = fill_high

    ws1.column_dimensions['A'].width = 28
    ws1.column_dimensions['B'].width = 16
    ws1.column_dimensions['C'].width = 46
    ws1.column_dimensions['D'].width = 4
    ws1.column_dimensions['E'].width = 28
    ws1.column_dimensions['F'].width = 20
    ws1.column_dimensions['G'].width = 54

    # =========================================================================
    # 2. SHEET: Hasil Test
    # =========================================================================
    ws2 = wb.create_sheet(title="Hasil Test")
    ws2.views.sheetView[0].showGridLines = True
    
    headers_t2 = ["No", "Modul", "Nama Test", "Status", "Durasi", "Pesan Error Ringkas"]
    for col_idx, h in enumerate(headers_t2, 1):
        cell = ws2.cell(row=1, column=col_idx, value=h)
        cell.font = header_font
        cell.fill = header_fill
        cell.alignment = align_center
        cell.border = thin_border
        
    tests_data = [
        (1, "Unit Example", "Tests\\Unit\\ExampleTest > that true is true", "Pass", "0.02s", "-"),
        (2, "Commercial Service", "Tests\\Feature\\BusinessFlowTest > service quotation can be created", "Fail", "0.01s", "QueryException: could not find driver (Connection: sqlite, SQL: PRAGMA foreign_keys = ON;)"),
        (3, "Sales & Inventory", "Tests\\Feature\\BusinessFlowTest > sale rejects insufficient stock", "Fail", "0.01s", "QueryException: could not find driver (Connection: sqlite)"),
        (4, "Finance Payment", "Tests\\Feature\\BusinessFlowTest > payment cannot exceed invoice balance", "Fail", "0.01s", "QueryException: could not find driver (Connection: sqlite)"),
        (5, "Sales & Invoice", "Tests\\Feature\\BusinessFlowTest > sale creates invoice items", "Fail", "0.01s", "QueryException: could not find driver (Connection: sqlite)"),
        (6, "Tender Evaluation", "Tests\\Feature\\BusinessFlowTest > tender evaluation is recorded", "Fail", "0.01s", "QueryException: could not find driver (Connection: sqlite)"),
        (7, "Tender Lifecycle", "Tests\\Feature\\BusinessFlowTest > rejected tender moves to lost status", "Fail", "0.01s", "QueryException: could not find driver (Connection: sqlite)"),
        (8, "Tender Documents", "Tests\\Feature\\BusinessFlowTest > uploading tender document replaces previous document", "Fail", "0.01s", "QueryException: could not find driver (Connection: sqlite)"),
        (9, "Console Reminders", "Tests\\Feature\\BusinessFlowTest > deadline command notifies business roles", "Fail", "0.01s", "QueryException: could not find driver (Connection: sqlite)"),
        (10, "Auth Password", "Tests\\Feature\\BusinessFlowTest > local password reset link can be used", "Fail", "0.01s", "QueryException: could not find driver (Connection: sqlite)"),
        (11, "Auth Registration", "Tests\\Feature\\BusinessFlowTest > registration requires super admin approval before login", "Fail", "0.01s", "QueryException: could not find driver (Connection: sqlite)"),
        (12, "Auth Security", "Tests\\Feature\\BusinessFlowTest > login requires a valid server side captcha", "Fail", "0.01s", "QueryException: could not find driver (Connection: sqlite)"),
        (13, "Auth Security", "Tests\\Feature\\BusinessFlowTest > login captcha can be refreshed", "Fail", "0.01s", "QueryException: could not find driver (Connection: sqlite)"),
        (14, "User Management", "Tests\\Feature\\BusinessFlowTest > user delete requires matching email confirmation", "Fail", "0.01s", "QueryException: could not find driver (Connection: sqlite)"),
        (15, "Feature Example", "Tests\\Feature\\ExampleTest > the application returns a successful response", "Fail", "0.01s", "QueryException: could not find driver (Connection: sqlite)")
    ]
    
    for r_idx, row_vals in enumerate(tests_data, 2):
        for c_idx, val in enumerate(row_vals, 1):
            cell = ws2.cell(row=r_idx, column=c_idx, value=val)
            cell.font = regular_font
            cell.border = thin_border
            if c_idx == 1:
                cell.alignment = align_center
            elif c_idx == 4:
                cell.alignment = align_center
                if val == "Pass":
                    cell.fill = fill_pass; cell.font = font_pass
                else:
                    cell.fill = fill_fail; cell.font = font_fail
            elif c_idx == 5:
                cell.alignment = align_center
            else:
                cell.alignment = align_left
                
    ws2.freeze_panes = "A2"
    ws2.auto_filter.ref = f"A1:F{len(tests_data)+1}"
    
    ws2.column_dimensions['A'].width = 6
    ws2.column_dimensions['B'].width = 22
    ws2.column_dimensions['C'].width = 50
    ws2.column_dimensions['D'].width = 12
    ws2.column_dimensions['E'].width = 12
    ws2.column_dimensions['F'].width = 60

    # =========================================================================
    # 3. SHEET: Coverage
    # =========================================================================
    ws3 = wb.create_sheet(title="Coverage")
    ws3.views.sheetView[0].showGridLines = True
    
    headers_t3 = ["No", "Modul / File", "Statement %", "Branch %", "Function %", "Line %", "Keterangan"]
    for col_idx, h in enumerate(headers_t3, 1):
        cell = ws3.cell(row=1, column=col_idx, value=h)
        cell.font = header_font
        cell.fill = header_fill
        cell.alignment = align_center
        cell.border = thin_border
        
    coverage_data = [
        (1, "app/Http/Controllers/Api/BusinessApiController.php", 0.0, 0.0, 0.0, 0.0, "Belum tercover test otomatis (0%)"),
        (2, "app/Http/Controllers/TenderProjectController.php", 0.0, 0.0, 0.0, 0.0, "Belum tercover test otomatis (0%)"),
        (3, "app/Http/Controllers/ServiceOperationsController.php", 0.0, 0.0, 0.0, 0.0, "Belum tercover test otomatis (0%)"),
        (4, "app/Http/Controllers/ReportController.php", 0.0, 0.0, 0.0, 0.0, "Belum tercover test otomatis (0%)"),
        (5, "app/Http/Controllers/DashboardController.php", 0.0, 0.0, 0.0, 0.0, "Belum tercover test otomatis (0%)"),
        (6, "app/Http/Controllers/ProductController.php", 0.0, 0.0, 0.0, 0.0, "Belum tercover test otomatis (0%)"),
        (7, "app/Http/Controllers/TenderController.php", 0.0, 0.0, 0.0, 0.0, "Belum tercover test otomatis (0%)"),
        (8, "app/Http/Controllers/SalesController.php", 0.0, 0.0, 0.0, 0.0, "Belum tercover test otomatis (0%)"),
        (9, "app/Http/Controllers/InvoiceController.php", 0.0, 0.0, 0.0, 0.0, "Belum tercover test otomatis (0%)"),
        (10, "app/Http/Controllers/PaymentController.php", 0.0, 0.0, 0.0, 0.0, "Belum tercover test otomatis (0%)"),
        (11, "app/Http/Controllers/ProcurementController.php", 0.0, 0.0, 0.0, 0.0, "Belum tercover test otomatis (0%)"),
        (12, "app/Http/Controllers/ServiceJobController.php", 0.0, 0.0, 0.0, 0.0, "Belum tercover test otomatis (0%)"),
        (13, "app/Http/Controllers/TechnicianController.php", 0.0, 0.0, 0.0, 0.0, "Belum tercover test otomatis (0%)"),
        (14, "app/Http/Controllers/UserManagementController.php", 0.0, 0.0, 0.0, 0.0, "Test gagal dieksekusi (driver SQLite absen)"),
        (15, "app/Http/Controllers/AuthController.php", 0.0, 0.0, 0.0, 0.0, "Test gagal dieksekusi (driver SQLite absen)"),
        (16, "app/Services/SalesService.php", 0.0, 0.0, 0.0, 0.0, "Test gagal dieksekusi (driver SQLite absen)"),
        (17, "app/Services/PaymentService.php", 0.0, 0.0, 0.0, 0.0, "Test gagal dieksekusi (driver SQLite absen)"),
        (18, "app/Services/TenderService.php", 0.0, 0.0, 0.0, 0.0, "Belum tercover test otomatis (0%)"),
        (19, "app/Services/ProcurementService.php", 0.0, 0.0, 0.0, 0.0, "Belum tercover test otomatis (0%)"),
        (20, "app/Models/Product.php", 0.0, 0.0, 0.0, 0.0, "Belum tercover test otomatis (0%)"),
        (21, "app/Models/Tender.php", 0.0, 0.0, 0.0, 0.0, "Belum tercover test otomatis (0%)")
    ]
    
    for r_idx, row_vals in enumerate(coverage_data, 2):
        for c_idx, val in enumerate(row_vals, 1):
            cell = ws3.cell(row=r_idx, column=c_idx, value=val)
            cell.font = regular_font
            cell.border = thin_border
            if c_idx == 1:
                cell.alignment = align_center
            elif 3 <= c_idx <= 6:
                cell.alignment = align_center
                cell.number_format = "0.0%"
                # Warnai merah jika di bawah 60%
                if val < 0.60:
                    cell.fill = fill_alert
                    cell.font = font_alert
            else:
                cell.alignment = align_left
                
    ws3.freeze_panes = "A2"
    ws3.auto_filter.ref = f"A1:G{len(coverage_data)+1}"
    
    ws3.column_dimensions['A'].width = 6
    ws3.column_dimensions['B'].width = 48
    ws3.column_dimensions['C'].width = 14
    ws3.column_dimensions['D'].width = 14
    ws3.column_dimensions['E'].width = 14
    ws3.column_dimensions['F'].width = 14
    ws3.column_dimensions['G'].width = 44

    # =========================================================================
    # 4. SHEET: Temuan
    # =========================================================================
    ws4 = wb.create_sheet(title="Temuan")
    ws4.views.sheetView[0].showGridLines = True
    
    headers_t4 = ["ID", "Severity", "Kategori", "File:Baris", "Deskripsi", "Dampak", "Saran Perbaikan", "Sumber", "Status"]
    for col_idx, h in enumerate(headers_t4, 1):
        cell = ws4.cell(row=1, column=col_idx, value=h)
        cell.font = header_font
        cell.fill = header_fill
        cell.alignment = align_center
        cell.border = thin_border
        
    findings_data = [
        ("T-01", "Critical", "Keamanan", "app/Http/Controllers/Api/BusinessApiController.php:97",
         "BOLA (Broken Object Level Authorization) & Bypass Approval Hapus Tender via REST API. Endpoint DELETE /api/tenders/{tender} menghapus tender tanpa pengecekan hak akses atau alur izin Owner/Manager.",
         "User dengan token API biasa dapat menghapus tender manapun secara sepihak tanpa melewati alur persetujuan dua pihak.",
         "Terapkan middleware role/permission pada route API dan integrasikan alur request-deletion yang sama dengan web controller.",
         "Code Review Logika Kritis", "Terverifikasi"),
         
        ("T-02", "Critical", "Keamanan", "app/Http/Controllers/Api/BusinessApiController.php:80",
         "Bypass Alur Persetujuan Perubahan Harga Produk & Kontrak via API. Endpoint PUT /api/products/{id} dan PUT /api/contracts/{id} mengubah harga secara langsung tanpa permohonan ke Manager.",
         "Pencegahan perubahan harga sepihak yang diatur di web diabaikan melalui API, membuka celah manipulasi finansial.",
         "Tambahkan otorisasi strictly role:owner,manager pada aksi mutasi harga di BusinessApiController.",
         "Code Review Logika Kritis", "Terverifikasi"),
         
        ("T-03", "High", "Keamanan", "app/Http/Controllers/TenderController.php:438",
         "Unrestricted File Upload pada Dokumen Tender. Validasi hanya 'required|file|max:10240' tanpa pembatasan ekstensi atau MIME type (mimes:pdf,docx,xlsx,png,jpg).",
         "Penyerang dapat mengunggah file berbahaya (.php, .html, .svg) ke direktori public storage, berpotensi Remote Code Execution (RCE) atau Stored XSS.",
         "Tambahkan validasi ketat: 'mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png' dan simpan file di storage non-public dengan download berotorisasi.",
         "Code Review Logika Kritis", "Terverifikasi"),
         
        ("T-04", "High", "Bug", "app/Services/SalesService.php:20",
         "Race Condition Validasi & Pengurangan Stok Barang. Pengecekan stok $product->stock tidak menggunakan lock baris (lockForUpdate).",
         "Dua transaksi serentak pada stok produk yang sama dapat lolos validasi bersamaan, menyebabkan kuantitas stok di gudang menjadi negatif.",
         "Gunakan Product::where('id', $id)->lockForUpdate()->firstOrFail() di dalam transaksi DB sebelum validasi kuantitas.",
         "Code Review Logika Kritis", "Terverifikasi"),
         
        ("T-05", "High", "Keamanan", "app/Http/Controllers/AuthController.php:189",
         "Ekspos Tautan Reset Password Beserta Token pada Session/Response di Environment local/testing.",
         "Jika aplikasi dijalankan di server staging publik dengan APP_ENV=local, token reset password terekspos sehingga akun dapat diambil alih tanpa akses email.",
         "Hapus pengembalian reset_url di session response atau batasi strictly hanya jika running di PHPUnit unit test context.",
         "Code Review Logika Kritis", "Terverifikasi"),
         
        ("T-06", "Medium", "Bug", "app/Services/SalesService.php:40",
         "Penomoran Penjualan dan Invoice Lemah Menggunakan rand(100, 999). Ruang acak hanya 900 kombinasi per hari.",
         "Dalam volume penjualan padat, collision pada unique constraint akan melempar unhandled QueryException dan memicu HTTP 500.",
         "Gunakan auto-increment sequence harian, atomic counter di database/redis, atau format berbasis UUID/timestamp mikrodetik.",
         "Code Review Logika Kritis", "Terverifikasi"),
         
        ("T-07", "Medium", "Bug", "app/Http/Controllers/ServiceOperationsController.php:137",
         "Permintaan Material Operasional Jasa Mengurangi Stok Gudang Tanpa Pengecekan Ketersediaan.",
         "Jika stok produk di dagang lebih kecil dari permintaan teknisi jasa, stok barang langsung berkurang menjadi negatif tanpa error atau peringatan.",
         "Tambahkan validasi ketersediaan stok fisik sebelum membuat StockMovement OUT.",
         "Code Review Logika Kritis", "Terverifikasi"),
         
        ("T-08", "Medium", "Keamanan", "routes/web.php:52",
         "Exemption CSRF pada Logout Route dan Penggunaan HTTP GET. URL /logout dapat diakses via GET dan dikecualikan di VerifyCsrfToken.",
         "Penyerang dapat memasang tag <img src='.../logout'> di web eksternal untuk memaksa pengguna logout (Cross-Site Request Forgery Logout).",
         "Hapus 'logout' dari $except di VerifyCsrfToken dan wajibkan logout hanya menerima HTTP POST dengan valid CSRF token.",
         "Review Konfigurasi & SAST", "Terverifikasi"),
         
        ("T-09", "Medium", "Bug", "app/Services/PaymentService.php:15",
         "Ketiadaan Pessimistic Locking pada Pengecekan Overpayment Invoice.",
         "Jika dua pembayaran tercatat serentak untuk invoice yang sama, akumulasi pembayaran dapat melebihi total tagihan sebelum status 'Paid' terkunci.",
         "Terapkan lockForUpdate() pada query Invoice di dalam DB::transaction.",
         "Code Review Logika Kritis", "Terverifikasi"),
         
        ("T-10", "Medium", "Performa", "app/Models/Product.php:39",
         "N+1 Query Issue Akibat Appended Accessor 'stock' pada Model Product. $appends = ['stock'] mengeksekusi 3 query terpisah per row jika relasi tidak di-preload.",
         "Pada pagination atau endpoint list produk (50 item), terjadi 150 query agregasi database, menyebabkan latensi response tinggi.",
         "Hapus 'stock' dari default $appends, gunakan withSum/subquery untuk list agregat, atau preload relasi stockMovements.",
         "Code Review Logika Kritis", "Terverifikasi"),
         
        ("T-11", "Medium", "Keamanan", "app/Http/Controllers/AuthController.php:37",
         "Penetapan Role Default Tingkat Tinggi ('admin') pada Registrasi Publik Mandiri.",
         "Pengguna publik yang mendaftar langsung dialokasikan ke role 'admin'. Sekali diaktifkan, mereka memiliki hak akses administratif luas.",
         "Tetapkan role default registrasi ke peran terendah (misal: 'user'/'guest' atau tanpa izin) dan biarkan Owner menentukan role secara manual.",
         "Code Review Logika Kritis", "Terverifikasi"),
         
        ("T-12", "Low", "Maintainability", "app/Services/SalesService.php:80",
         "Hardcoded Nilai Pajak PPN 11% di Kode Sumber ($taxAmount = $totalAmount * 0.11).",
         "Jika tarif pajak regulasi berubah (seperti penyesuaian PPN 12%), perubahan harus dilakukan manual di kode sumber dan rawan diskrepansi perhitungan.",
         "Pindahkan tarif pajak ke konfigurasi dinamis database (tabel settings) atau config('finance.tax_rate').",
         "Code Review Logika Kritis", "Terverifikasi"),
         
        ("T-13", "Low", "Maintainability", "app/Http/Controllers/*.php",
         "71 Ketidaksesuaian Standar Format Kode (PSR-12) Terdeteksi di 140 File oleh Laravel Pint.",
         "Inkonsistensi indentasi, spasi konkatenasi, pengurutan import, dan trailing commas menurunkan keterbacaan kode.",
         "Jalankan 'php vendor/bin/pint' secara terjadwal atau pasang pre-commit hook untuk standardisasi format kode tim.",
         "Laravel Pint (Static Analysis)", "Terverifikasi")
    ]
    
    for r_idx, row_vals in enumerate(findings_data, 2):
        for c_idx, val in enumerate(row_vals, 1):
            cell = ws4.cell(row=r_idx, column=c_idx, value=val)
            cell.font = regular_font
            cell.border = thin_border
            if c_idx == 1:
                cell.alignment = align_center; cell.font = bold_font
            elif c_idx == 2:
                cell.alignment = align_center
                if val == "Critical":
                    cell.fill = fill_critical; cell.font = font_critical
                elif val == "High":
                    cell.fill = fill_high; cell.font = font_high
                elif val == "Medium":
                    cell.fill = fill_medium; cell.font = font_medium
                else:
                    cell.fill = fill_low; cell.font = font_low
            elif c_idx in [3, 8, 9]:
                cell.alignment = align_center
            else:
                cell.alignment = align_left
                
    ws4.freeze_panes = "A2"
    ws4.auto_filter.ref = f"A1:I{len(findings_data)+1}"
    
    ws4.column_dimensions['A'].width = 8
    ws4.column_dimensions['B'].width = 14
    ws4.column_dimensions['C'].width = 16
    ws4.column_dimensions['D'].width = 46
    ws4.column_dimensions['E'].width = 46
    ws4.column_dimensions['F'].width = 44
    ws4.column_dimensions['G'].width = 44
    ws4.column_dimensions['H'].width = 24
    ws4.column_dimensions['I'].width = 14

    # =========================================================================
    # 5. SHEET: Dependency & Secret
    # =========================================================================
    ws5 = wb.create_sheet(title="Dependency & Secret")
    ws5.views.sheetView[0].showGridLines = True
    
    headers_t5 = ["No", "Paket / Lokasi", "Versi Terpasang", "Kerentanan / Jenis Temuan", "Severity", "Versi Perbaikan / Saran"]
    for col_idx, h in enumerate(headers_t5, 1):
        cell = ws5.cell(row=1, column=col_idx, value=h)
        cell.font = header_font
        cell.fill = header_fill
        cell.alignment = align_center
        cell.border = thin_border
        
    dep_data = [
        (1, "laravel/framework", "v10.48.28", "CRLF Injection in default email rule (GHSA-5vg9-5847-vvmq / PKSA-3r5d-mb8f-1qw9)", "High", "Update ke Laravel >= 12.60.0 atau v13.10.0+"),
        (2, "league/commonmark", "2.4.2", "Quadratic-time DoS in GFM Table scan (GHSA-3q6v-r5mr-hxv8 / PKSA-m4t9-vsgq-8khn)", "High", "Update league/commonmark > 2.10.1"),
        (3, "shell-quote (npm)", "1.8.1", "Command injection via line terminator in token after comment (GHSA-pqg4-j6r4-53mv)", "Critical", "Update shell-quote ke >= 1.8.2 / npm audit fix"),
        (4, "source-map-js (npm)", "1.0.2", "Event-loop DoS through indexed source-map offsets (GHSA-68fv-2mgg-jv7q)", "High", "Update source-map-js ke >= 1.2.1"),
        (5, "laravel/framework", "v10.48.28", "Temporary Signed URL Path Confusion (GHSA-crmm-hgp2-wgrp / PKSA-m5cs-t1y6-qpcs)", "Medium", "Update ke Laravel >= 12.61.1"),
        (6, "league/commonmark", "2.4.2", "DisallowedRawHtml bypassed when disallowed tag ends literal (GHSA-97jj-33gv-5xf9)", "Medium", "Update league/commonmark > 2.10.1"),
        (7, "esbuild / vite (npm)", "0.20.2 / 5.1.4", "Arbitrary local dev server request reading (GHSA-67mh-4wv8-2f99)", "Medium", "Update vite & esbuild ke versi patch terbaru"),
        (8, "firebase/php-jwt", "6.10.0", "Weak encryption fallback in certain verification modes (CVE-2025-45769)", "Low", "Update firebase/php-jwt ke >= 7.0.0"),
        (9, "laravel/framework", "v10.48.28", "XSS in Debug Page Information (CVE-2026-102279 / GHSA-jh5r-qr3c-85q8)", "Low", "Pastikan APP_DEBUG=false pada production"),
        (10, "symfony/http-foundation", "v6.4.16", "ReDoS in HeaderUtils::split() via Cache-Control header (CVE-2026-108082)", "Medium", "Update symfony/http-foundation ke >= 6.4.46"),
        (11, "doctrine/cache", "2.2.0", "Paket ditinggalkan (Abandoned Package) oleh pengembang", "Low", "Gantikan dengan symfony/cache atau PSR-6 / PSR-16 cache"),
        (12, ".env (Root Workspace)", "N/A", "APP_KEY hardcoded terekam di file konfigurasi lokal aktif", "Medium", "Pastikan .env masuk .gitignore dan lakukan key rotate di server live")
    ]
    
    for r_idx, row_vals in enumerate(dep_data, 2):
        for c_idx, val in enumerate(row_vals, 1):
            cell = ws5.cell(row=r_idx, column=c_idx, value=val)
            cell.font = regular_font
            cell.border = thin_border
            if c_idx == 1:
                cell.alignment = align_center
            elif c_idx == 5:
                cell.alignment = align_center
                if val == "Critical":
                    cell.fill = fill_critical; cell.font = font_critical
                elif val == "High":
                    cell.fill = fill_high; cell.font = font_high
                elif val == "Medium":
                    cell.fill = fill_medium; cell.font = font_medium
                else:
                    cell.fill = fill_low; cell.font = font_low
            else:
                cell.alignment = align_left
                
    ws5.freeze_panes = "A2"
    ws5.auto_filter.ref = f"A1:F{len(dep_data)+1}"
    
    ws5.column_dimensions['A'].width = 6
    ws5.column_dimensions['B'].width = 26
    ws5.column_dimensions['C'].width = 16
    ws5.column_dimensions['D'].width = 48
    ws5.column_dimensions['E'].width = 14
    ws5.column_dimensions['F'].width = 46

    # =========================================================================
    # 6. SHEET: Kompleksitas & Duplikasi
    # =========================================================================
    ws6 = wb.create_sheet(title="Kompleksitas & Duplikasi")
    ws6.views.sheetView[0].showGridLines = True
    
    headers_t6 = ["No", "File : Fungsi", "Kompleksitas Siklomatik", "Duplikasi / Kode Mati", "Catatan & Risiko"]
    for col_idx, h in enumerate(headers_t6, 1):
        cell = ws6.cell(row=1, column=col_idx, value=h)
        cell.font = header_font
        cell.fill = header_fill
        cell.alignment = align_center
        cell.border = thin_border
        
    complexity_data = [
        (1, "app/Services/SalesService.php:createSale", 51, "Duplikasi logika subtotal & stock movements", "Kompleksitas sangat tinggi; menggabungkan validasi stok, invoice, dan item penjualan."),
        (2, "app/Http/Controllers/ReportController.php:export", 46, "Percabangan format Excel/PDF & query filter", "Banyak nested if untuk setiap tipe laporan; disarankan memakai strategy pattern."),
        (3, "app/Http/Controllers/DashboardController.php:index", 34, "Perhitungan KPI terulang di beberapa method", "Mengumpulkan puluhan metrik dalam 1 controller; disarankan dipisah ke DashboardService."),
        (4, "app/Http/Controllers/TenderController.php:store", 31, "Validasi manual & pemetaan approval bertingkat", "Mengatur status approval owner/manager di method store; rawan inkonsistensi."),
        (5, "app/Http/Controllers/ReportController.php:index", 25, "Filter query duplikat dengan export()", "Logika filtering tanggal dan kategori duplikat antara method index dan export."),
        (6, "app/Http/Controllers/SalesController.php:store", 23, "Pengecekan role & input item multi-baris", "Menghandle data input customer dan mapping item; delegasi ke service masih minim."),
        (7, "app/Http/Controllers/ServiceOperationsController.php:updateProgress", 23, "Sinkronisasi otomatis ke Modul Tender", "Mengupdate status job, technician assignment, dan tender sekaligus; rawan race condition."),
        (8, "app/Services/ProcurementService.php:createProcurement", 22, "Logika perulangan item pengadaan & stok IN", "Penanganan transaksi item dan kalkulasi total subtotal dalam 1 blok besar."),
        (9, "app/Http/Middleware/AuditActivity.php:handle", 19, "Parsing parameter route & filter tipe mutasi", "Mengekstrak model ID dari berbagai variasi route parameter; sulit dimaintain."),
        (10, "app/Services/TenderService.php:convertTenderToContract", 19, "Pemindahan item tender ke klausul kontrak", "Banyak percabangan validasi status tender sebelum konversi diizinkan."),
        (11, "app/Http/Controllers/TenderProjectController.php:updateStatus", 18, "Validasi alur transisi status proyek", "Pengecekan prasyarat dokumen sebelum status lapangan dapat bergeser."),
        (12, "app/Http/Controllers/ProductController.php:index", 17, "Kondisional filter status permohonan", "4 blok if/elseif untuk menangani filter tab pending, penghapusan, dan penyesuaian."),
        (13, "app/Http/Controllers/TenderController.php:index", 17, "Filter multi-kriteria & subquery counting", "Menggabungkan pencarian teks dengan filter status approval dan submission."),
        (14, "app/Http/Controllers/ProductController.php:approveChangeRequest", 16, "Percabangan dua jenis request (stok vs harga)", "Sebaiknya dipisah menjadi 2 method tersendiri: approveStock dan approvePrice."),
        (15, "app/Http/Controllers/CommercialDocumentController.php:storeServiceQuotation", 15, "Perhitungan total & PPN item quotation", "Pola perhitungan PPN 11% berulang dengan SalesService."),
        (16, "app/Http/Controllers/TechnicianController.php:index", 14, "Filter kompetensi & ketersediaan teknisi", "Query filter status teknisi dan mapping tugas aktif."),
        (17, "app/Http/Controllers/TenderRabController.php:storeItem", 14, "Kalkulasi margin profit & subtotal RAB", "Perhitungan margin berulang untuk internal vs subkontraktor."),
        (18, "app/Http/Controllers/AuthController.php:handleProviderCallback", 14, "Penanganan Socialite multi-provider", "Banyak pengecekan fallback email, linking oauth, dan aktivasi akun."),
        (19, "app/Http/Controllers/ServiceJobController.php:store", 13, "Pemetaan klien baru vs klien terdaftar", "Logika resolve klien serupa dengan yang ada di TenderController (duplikasi)."),
        (20, "app/Http/Controllers/DashboardController.php:calculateKpi", 12, "Perhitungan persentase margin & rasio", "Pembagian angka tanpa penanganan pembagi nol (zero division risk).")
    ]
    
    for r_idx, row_vals in enumerate(complexity_data, 2):
        for c_idx, val in enumerate(row_vals, 1):
            cell = ws6.cell(row=r_idx, column=c_idx, value=val)
            cell.font = regular_font
            cell.border = thin_border
            if c_idx == 1:
                cell.alignment = align_center
            elif c_idx == 3:
                cell.alignment = align_center
                if val >= 30:
                    cell.fill = fill_critical; cell.font = font_critical
                elif val >= 20:
                    cell.fill = fill_high; cell.font = font_high
                else:
                    cell.fill = fill_medium; cell.font = font_medium
            else:
                cell.alignment = align_left
                
    ws6.freeze_panes = "A2"
    ws6.auto_filter.ref = f"A1:E{len(complexity_data)+1}"
    
    ws6.column_dimensions['A'].width = 6
    ws6.column_dimensions['B'].width = 48
    ws6.column_dimensions['C'].width = 22
    ws6.column_dimensions['D'].width = 36
    ws6.column_dimensions['E'].width = 54

    # =========================================================================
    # 7. SHEET: Rekomendasi Test
    # =========================================================================
    ws7 = wb.create_sheet(title="Rekomendasi Test")
    ws7.views.sheetView[0].showGridLines = True
    
    headers_t7 = ["No", "Modul / Fungsi", "Cabang / Kondisi yang Belum Teruji", "Skenario yang Disarankan", "Prioritas"]
    for col_idx, h in enumerate(headers_t7, 1):
        cell = ws7.cell(row=1, column=col_idx, value=h)
        cell.font = header_font
        cell.fill = header_fill
        cell.alignment = align_center
        cell.border = thin_border
        
    rec_tests = [
        (1, "SalesService::createSale", "Transaksi penjualan serentak pada produk dengan stok terbatas.",
         "Uji konkurensi (2 request paralel) saat sisa stok = 10 dan masing-masing meminta 10. Pastikan 1 berhasil dan 1 gagal dengan exception stok tidak cukup.", "P1 (Critical)"),
         
        (2, "BusinessApiController::destroyTender", "Akses penghapusan tender oleh role admin via API token.",
         "Kirim DELETE /api/tenders/{id} menggunakan token user role 'admin'. Pastikan sistem mengembalikan HTTP 403 Forbidden.", "P1 (Critical)"),
         
        (3, "TenderController::uploadDocument", "Validasi file berbahaya (.php, .exe, .html, .svg) pada dokumen tender.",
         "Unggah file exploit.php atau xss.html. Pastikan validator mengembalikan error 422 dan file tidak tersimpan di storage public.", "P1 (Critical)"),
         
        (4, "BusinessApiController::updateProduct", "Bypass perubahan harga produk tanpa persetujuan Manager.",
         "Kirim PUT /api/products/{id} dengan payload selling_price baru oleh user non-manajemen. Pastikan request ditolak.", "P1 (Critical)"),
         
        (5, "ServiceOperationsController::requestMaterial", "Permintaan material melebihi stok barang di gudang Dagang.",
         "Pilih product_id dengan stok = 2 dan request quantity = 10. Pastikan sistem melempar validasi error bahwa stok gudang tidak mencukupi.", "P2 (High)"),
         
        (6, "PaymentService::recordPayment", "Pembayaran ganda bersamaan pada sisa saldo invoice.",
         "Kirim dua pembayaran simultan Rp 600.000 pada invoice bersisa Rp 1.000.000. Pastikan total terbayar tidak melebihi sisa tagihan.", "P2 (High)"),
         
        (7, "AuthController::logout", "Serangan CSRF Logout via GET request dari domain eksternal.",
         "Simulasikan request GET /logout tanpa valid CSRF token. Pastikan session tidak diinvalidation jika request berupa GET tanpa proteksi.", "P2 (High)"),
         
        (8, "AuthController::sendResetLink", "Ekspos token reset password pada mode staging/local.",
         "Request forgot password pada akun terdaftar. Pastikan response JSON/Session tidak mengembalikan reset_url saat diuji di staging/production.", "P2 (High)"),
         
        (9, "SalesService::createSale", "Collision penomoran sale_number akibat rand(100, 999).",
         "Simulasikan 100 transaksi penjualan dalam 1 detik. Pastikan semua sale_number unik dan tidak terjadi QueryException duplicate key.", "P3 (Medium)"),
         
        (10, "Product::getStockAttribute", "Evaluasi jumlah query (N+1) pada pemanggilan list produk.",
         "Panggil Product::paginate(50)->toArray() dan ukur DB::getQueryLog(). Pastikan jumlah query tidak melompat menjadi > 150 query.", "P3 (Medium)")
    ]
    
    for r_idx, row_vals in enumerate(rec_tests, 2):
        for c_idx, val in enumerate(row_vals, 1):
            cell = ws7.cell(row=r_idx, column=c_idx, value=val)
            cell.font = regular_font
            cell.border = thin_border
            if c_idx == 1:
                cell.alignment = align_center
            elif c_idx == 5:
                cell.alignment = align_center
                if "P1" in val:
                    cell.fill = fill_critical; cell.font = font_critical
                elif "P2" in val:
                    cell.fill = fill_high; cell.font = font_high
                else:
                    cell.fill = fill_medium; cell.font = font_medium
            else:
                cell.alignment = align_left
                
    ws7.freeze_panes = "A2"
    ws7.auto_filter.ref = f"A1:E{len(rec_tests)+1}"
    
    ws7.column_dimensions['A'].width = 6
    ws7.column_dimensions['B'].width = 32
    ws7.column_dimensions['C'].width = 44
    ws7.column_dimensions['D'].width = 54
    ws7.column_dimensions['E'].width = 16

    # =========================================================================
    # 8. SHEET: Keterbatasan
    # =========================================================================
    ws8 = wb.create_sheet(title="Keterbatasan")
    ws8.views.sheetView[0].showGridLines = True
    
    headers_t8 = ["No", "Aspek / Modul", "Kendala yang Ditemui", "Penyebab Teknis", "Dampak pada Pengujian"]
    for col_idx, h in enumerate(headers_t8, 1):
        cell = ws8.cell(row=1, column=col_idx, value=h)
        cell.font = header_font
        cell.fill = header_fill
        cell.alignment = align_center
        cell.border = thin_border
        
    limitation_data = [
        (1, "Eksekusi Feature Tests", "14 dari 15 test gagal dieksekusi saat 'php artisan test' dijalankan.",
         "PHP CLI 8.1.10 pada Laragon belum mengaktifkan ekstensi pdo_sqlite, sedangkan phpunit.xml mengarahkan koneksi ke SQLite In-Memory.",
         "Pengujian integrasi otomatis tidak dapat memvalidasi alur bisnis secara dinamis; pengujian dikompensasi via code review mendalam."),
         
        (2, "Dynamic Code Coverage Profiling", "Tidak dapat menghasilkan laporan persentase line/branch coverage real-time.",
         "Ekstensi driver coverage (Xdebug atau PCOV) tidak terpasang di runtime PHP CLI lokal.",
         "Cakupan pengujian (coverage) dilaporkan berdasarkan ketiadaan tes fungsional yang lulus pada modul-modul bersangkutan."),
         
        (3, "Aturan Ketat Non-Mutasi Kode", "Tidak diperkenankan mengubah phpunit.xml atau mengaktifkan ekstensi host.",
         "Aturan Strict Rule 1 & Rule 7 melarang modifikasi kode atau file konfigurasi proyek, serta melarang 'memperbaiki' kegagalan test.",
         "Kondisi dilaporkan secara objektif dan faktual apa adanya dalam laporan QA."),
         
        (4, "Audit Secret di History Git", "Analisis riwayat git commits terbatas pada shallow clone lokal.",
         "Repositori lokal berada di direktori SignalNiagaJasaTender dan sebagian file log tidak dimasukkan ke version control.",
         "Pemindaian kredensial difokuskan pada file konfigurasi (.env, config/) dan tree kode sumber aktif.")
    ]
    
    for r_idx, row_vals in enumerate(limitation_data, 2):
        for c_idx, val in enumerate(row_vals, 1):
            cell = ws8.cell(row=r_idx, column=c_idx, value=val)
            cell.font = regular_font
            cell.border = thin_border
            if c_idx == 1:
                cell.alignment = align_center
            else:
                cell.alignment = align_left
                
    ws8.freeze_panes = "A2"
    ws8.auto_filter.ref = f"A1:E{len(limitation_data)+1}"
    
    ws8.column_dimensions['A'].width = 6
    ws8.column_dimensions['B'].width = 24
    ws8.column_dimensions['C'].width = 42
    ws8.column_dimensions['D'].width = 46
    ws8.column_dimensions['E'].width = 48

    # Save to both locations
    OUTPUT_PATH.parent.mkdir(parents=True, exist_ok=True)
    wb.save(OUTPUT_PATH)
    print(f"Report saved successfully to {OUTPUT_PATH}")

    MIRROR_PATH.parent.mkdir(parents=True, exist_ok=True)
    wb.save(MIRROR_PATH)
    print(f"Report mirrored successfully to {MIRROR_PATH}")

if __name__ == '__main__':
    create_report()
