<!-- Breadcrumb -->
<nav class="breadcrumb-wrapper">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?= site_url('dashboard') ?>">Home</a></li>
        <li class="breadcrumb-item active">Surat Rekomendasi</li>
        <li class="breadcrumb-item active"><?= $title ?></li>
    </ol>
</nav>

<div class="card">
    <div class="card-header">
        <h3><?= $title ?></h3>
    </div>
    <div class="card-body">
        <div class="p-4 text-center" style="background:#f7fafc;border-radius:8px;border:1px dashed #cbd5e0;color:#4a5568">
            <div style="font-size:34px;font-weight:700;color:#2d3748;letter-spacing:1px;line-height:1.2"><?= $placeholder_text ?></div>
        </div>
    </div>
</div>
