<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Surat Tugas</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: "Times New Roman", serif;
            background-color: white;
            padding: 15mm 25mm 25mm 25mm;
        }
        .a4-page {
            width: 100%;
            margin: 0 auto;
            line-height: 1.4;
            font-size: 12pt;
            color: #000;
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
            margin-top: 8px;
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
            margin-top: 10px;
        }
        .judul h3 {
            display: inline-block;
            border-bottom: 2px solid black;
            padding-bottom: 2px;
            margin: 0;
        }
        .isi {
            margin-top: 10px;
            text-align: justify;
        }
        .data-table {
            margin-left: 40px;
            margin-top: 4px;
            margin-bottom: 4px;
        }
        .data-table td {
            padding: 0px 5px;
            vertical-align: top;
        }
        .ttd {
            margin-top: 15px;
            text-align: right;
        }
        .ttd .place-time {
            margin-right: 40px;
        }
        .ttd .position {
            margin-right: 0px;
        }
        .ttd .signer {
            margin-right: 30px;
        }
        .ttd .signer-id {
            margin-right: 40px;
        }
        .ttd .qr-code {
            width: 80px;
            height: auto;
            margin-top: 5px;
            margin-bottom: 5px;
            margin-right: 70px;
            display: inline-block;
        }
        @media print {
            body { padding: 0; }
            .a4-page {
                box-shadow: none;
                margin: 0;
                page-break-after: always;
            }
        }
    </style>
</head>
<body>

<div class="a4-page">
    <div class="kop-surat">
        <h4>KEMENTERIAN AGAMA</h4>
        <h4>LEMBAGA PENDAMPING PROSES PRODUK HALAL (LP3H)</h4>
        <h4>UIN SUNAN KALIJAGA YOGYAKARTA</h4>
        <p class="address">Jl. Marsda Adisucipto, Daerah Istimewa Yogyakarta 55281</p>
        <p class="contact">Email: halalcenter@uin-suka.ac.id | HP. 085229084845 | Website: halalcenter.uin-suka.ac.id</p>
    </div>

    <div class="line-separator">
        <hr class="line-thin">
        <hr class="line-thick">
        <hr class="line-thin">
    </div>

    <div class="judul">
        <h3>SURAT TUGAS</h3>
        <p style="margin-top: 6px;">No. {{ $nomorSurat ?? 'Z-177/LP3H-UINSK/X/2025' }}</p>
    </div>

    <div class="isi">
        <p>
            Dalam rangka Program Sertifikasi Halal Gratis (SEHATI) Tahun 2025 bagi Pelaku Usaha Mikro Kecil 
            yang diselenggarakan oleh BPJPH Kemenag RI, maka saya yang bertanda tangan di bawah ini:
        </p>

        <table class="data-table">
            <tr>
                <td width="130">Nama</td>
                <td width="10">:</td>
                <td>{{ $ketua->nama ?? 'Dr. Diky Faqih Maulana, M.H' }}</td>
            </tr>
            <tr>
                <td>NIP</td>
                <td>:</td>
                <td>{{ $ketua->nip ?? '199702100000001101' }}</td>
            </tr>
            <tr>
                <td>Jabatan</td>
                <td>:</td>
                <td>Ketua Lembaga Pendamping PPH UIN Sunan Kalijaga</td>
            </tr>
        </table>

        <p style="margin-top: 6px;">
            Memberikan tugas dan tanggung jawab kepada:
        </p>

        <table class="data-table">
            <tr>
                <td width="130">Nama</td>
                <td width="10">:</td>
                <td>{{ $pendamping->nama ?? 'Reni Okta Nia, S.Kom' }}</td>
            </tr>
            <tr>
                <td>No Registrasi</td>
                <td>:</td>
                <td>{{ $pendamping->no_registrasi ?? '2507001378' }}</td>
            </tr>
            <tr>
                <td>Jabatan</td>
                <td>:</td>
                <td>Pendamping PPH UIN Sunan Kalijaga Yogyakarta</td>
            </tr>
            <tr>
                <td>Alamat</td>
                <td>:</td>
                <td>{{ $pendamping->alamat ?? 'Pedungan, Denpasar Selatan, Denpasar - Bali' }}</td>
            </tr>
            <tr>
                <td>No. Hp</td>
                <td>:</td>
                <td>{{ $pendamping->no_hp ?? '082172168687' }}</td>
            </tr>
        </table>

        <p style="margin-top: 6px;">
            Untuk melakukan pendampingan sertifikasi halal gratis di wilayah {{ $wilayah ?? 'Bali' }}, dengan masa penugasan 
            tanggal {{ $tanggal_mulai ?? '06 Oktober' }} – {{ $tanggal_selesai ?? '29 Desember 2025' }}.
        </p>

        <p style="margin-top: 6px;">
            Demikian surat tugas ini agar dilaksanakan dengan tanggung jawab dan sebagaimana mestinya.
        </p>
    </div>

    <div class="ttd">
        <p class="place-time">Yogyakarta, {{ $tanggal ?? '06 Oktober 2025' }}</p>
        <p class="position">Ketua LP3H UIN Sunan Kalijaga Yogyakarta,</p>
        
        @if(isset($ttdBase64) && $ttdBase64)
            <img src="{{ $ttdBase64 }}" class="qr-code" alt="QR Code TTD">
        @else
            <br><br><br><br>
        @endif
        
        <p class="signer">{{ $ketua->nama ?? 'Dr. Diky Faqih Maulana, M.H.' }}</p>
        <p class="signer-id">NIP. {{ $ketua->nip ?? '199702100000001101' }}</p>
    </div>

</div>

</body>
</html>
