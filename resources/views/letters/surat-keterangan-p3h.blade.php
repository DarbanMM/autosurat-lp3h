<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Surat Keterangan</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: "Times New Roman", serif;
            background-color: white;
            padding: 15mm 25mm 25mm 25mm;
        }
        .a4-page {
            width: 100%;
            margin: 0 auto;
            line-height: 1.6;
            font-size: 12pt;
        }
        .kop-surat {
            text-align: center;
        }
        .kop-surat h4 {
            margin: 0;
        }
        .kop-surat h3 {
            margin: 0;
            font-size: 14pt;
        }
        .kop-surat p {
            margin: 2px 0;
        }
        .kop-surat .address {
            font-size: 9pt;
        }
        .kop-surat .contact {
            font-size: 8pt;
        }
        .line-separator {
            margin-top: 5px;
        }
        .line-thin {
            border: none;
            border-top: 1px solid black;
            margin: 0;
        }
        .line-thick {
            border: none;
            border-top: 3px solid black;
            margin: 2px 0;
        }
        .judul {
            text-align: center;
            margin-top: 20px;
        }
        .judul h3 {
            margin: 0;
            text-decoration: underline;
        }
        .isi {
            margin-top: 20px;
            text-align: justify;
        }
        .data-table {
            margin-left: 40px;
        }
        .data-table td {
            padding: 0px 5px;
            vertical-align: top;
        }
        .indent {
            text-indent: 5mm;
        }
        .closing {
            margin-top: 12px;
            text-align: justify;
        }
        .ttd {
            margin-top: 50px;
            text-align: right;
        }
        .ttd .place-time {
            margin-right: 0px;
        }
        .ttd .position {
            margin-right: 60px;
        }
        .ttd .signer {
            margin-right: 10px;
        }
        .ttd .signer-id {
            margin-right: 0px;
        }
        .ttd .qr-code {
            width: 80px;
            height: auto;
            margin-top: 5px;
            margin-bottom: 5px;
            margin-right: 35px;
            display: inline-block;
        }
        @media print {
            body {
                padding: 0;
            }
            .a4-page {
                box-shadow: none;
            }
        }
    </style>
</head>
<body>
@php
    $logoUinPath = resource_path('views/letters/Logo/Logo UIN Suka.png');
    $logoUin = file_exists($logoUinPath) ? 'data:image/png;base64,' . base64_encode(file_get_contents($logoUinPath)) : '';

    $logoHalalPath = resource_path('views/letters/Logo/Logo Halal Center.png');
    $logoHalal = file_exists($logoHalalPath) ? 'data:image/png;base64,' . base64_encode(file_get_contents($logoHalalPath)) : '';
@endphp

<div class="a4-page">

<table style="width: 100%; border: none; margin-bottom: 0px; padding: 0;">
    <tr>
        <td style="width: 15%; text-align: left; vertical-align: middle;">
            @if($logoUin) <img src="{{ $logoUin }}" style="max-height: 80px; max-width: 100%;"> @endif
        </td>
        <td style="width: 70%; text-align: center; vertical-align: middle;" class="kop-surat">
            <h4>KEMENTERIAN AGAMA REPUBLIK INDONESIA</h4>
            <h3>HALAL CENTER</h3>
            <h4>UIN SUNAN KALIJAGA YOGYAKARTA</h4>
            <p class="address">Jl. Marsda Adisucipto, Daerah Istimewa Yogyakarta 55281</p>
            <p class="contact">Email: halalcenter@uin-suka.ac.id | Telp: (0274) 512474 | Website: halalcenter.uin-suka.ac.id</p>
        </td>
        <td style="width: 15%; text-align: right; vertical-align: middle;">
            @if($logoHalal) <img src="{{ $logoHalal }}" style="max-height: 80px; max-width: 100%;"> @endif
        </td>
    </tr>
</table>

<div class="line-separator">
    <hr class="line-thin">
    <hr class="line-thick">
    <hr class="line-thin">
</div>

<div class="judul">
    <h4 style="border-bottom: 2px solid black; display: inline-block;">
    SURAT KETERANGAN </h4>
    <p>No. {{ $nomorSurat ?? 'K-01/HC-UINSK/III/2023' }}</p>
</div>

<div class="isi">
    <p class="indent">
        Dalam rangka Program Sertifikasi Halal Gratis (SEHATI) Tahun 2023 bagi Pelaku Usaha Mikro Kecil 
        yang diselenggarakan oleh BPJPH Kemenag RI, maka saya yang bertanda tangan di bawah ini:
    </p>

    <table class="data-table">
        <tr>
            <td>Nama</td>
            <td>:</td>
            <td>{{ $ketua->nama ?? 'Dr. Imelda Fajriati, M.Si' }}</td>
        </tr>
        <tr>
            <td>NIP</td>
            <td>:</td>
            <td>{{ $ketua->nip ?? '197507252000032001' }}</td>
        </tr>
        <tr>
            <td>Jabatan</td>
            <td>:</td>
            <td>{{ $ketua->jabatan ?? 'Ketua Lembaga Pendamping PPH / Halal Center UIN Sunan Kalijaga' }}</td>
        </tr>
    </table>

    <p>Menerangkan bahwa:</p>

    <table class="data-table">
        <tr>
            <td>Nama</td>
            <td>:</td>
            <td>{{ $pendamping->nama ?? 'Nurhidayah' }}</td>
        </tr>
        <tr>
            <td>No Registrasi</td>
            <td>:</td>
            <td>{{ $pendamping->no_registrasi ?? '2204001838' }}</td>
        </tr>
        <tr>
            <td>Jabatan</td>
            <td>:</td>
            <td>Pendamping PPH UIN Sunan Kalijaga Yogyakarta</td>
        </tr>
        <tr>
            <td>Alamat</td>
            <td>:</td>
            <td>{{ $pendamping->alamat ?? 'Bulugede 01/01 Patebon, Kendal, Jawa Tengah' }}</td>
        </tr>
    </table>

    <p>
        Telah mengundurkan diri mulai tanggal {{ $tanggal ?? '17 Maret 2023' }} dari jabatannya sebagai anggota aktif 
        Pendamping PPH Halal Center UIN Sunan Kalijaga Yogyakarta.
    </p>

    <div class="closing">
        <p>Demikian surat ini dibuat agar dapat digunakan sebagaimana mestinya.</p>
    </div>
</div>

<div class="ttd">
    <p class="place-time">Yogyakarta, {{ $tanggal ?? '17 Maret 2022' }}</p>
    <p class="position">Ketua,</p>
    
    @if(isset($ttdBase64) && $ttdBase64)
        <img src="{{ $ttdBase64 }}" class="qr-code" alt="QR Code TTD">
    @else
        <br><br><br><br>
    @endif
    
    <p class="signer">{{ $ketua->nama ?? 'Dr. Imelda Fajriati, M.Si' }}</p>
    <p class="signer-id">NIP. {{ $ketua->nip ?? '197507252000032001' }}</p>
</div>

</div>
</body>
</html>
