<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Surat Pengantar</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
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
            border: none; border-top: 1px solid #000; margin: 0;
        }
        .line-thick {
            border: none; border-top: 3px solid #000; margin: 2px 0;
        }
        .meta {
            margin-top: 8px;
            display:flex;
            justify-content: space-between;
            align-items: flex-start;
        }
        .meta .left {
            width: 60%;
        }
        .meta .right {
            width: 35%; text-align: left;
        }
        .meta p {
            margin: 2px 0;
        }
        .hal {
            margin-top: 4px;
        }
        .kepada {
            margin-top: 8px;
            margin-left: 40px;
        }
        .salutation {
            margin-top: 8px;
            margin-left: 40px;
        }
        .isi {
            margin-top: 8px;
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
        .indent {
            text-indent: 5mm;
        }
        .closing {
            margin-top: 8px;
            text-align: justify;
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
            <h4>KEMENTERIAN AGAMA REPUBLIK INDONESIA</h4>
            <h3>HALAL CENTER</h3>
            <h4>UIN SUNAN KALIJAGA YOGYAKARTA</h4>
            <p class="address">Jl. Marsda Adisucipto, Daerah Istimewa Yogyakarta 55281</p>
            <p class="contact">Email: halalcenter@uin-suka.ac.id | Telp: (0274) 512474 | Website: halalcenter.uin-suka.ac.id</p>
        </div>

        <div class="line-separator">
            <hr class="line-thin">
            <hr class="line-thick">
            <hr class="line-thin">
        </div>

        <div class="meta">
            <div class="left">
                <p>Nomor : {{ $nomorSurat ?? 'Z-178/LP3H-UINSK/X/2025' }}</p>
                <p class="hal">Hal : Surat Pengantar Pendampingan SEHATI {{ date('Y') }}</p>
            </div>
        </div>

        <div class="kepada">
            <p>Kepada Yth:</p>
            <p>Bapak/Ibu {{ $tujuan_kepada ?? 'Pemilik Dapur SPPG' }}</p>
            <p>{{ $daerah ?? 'Kabupaten Sragen' }}</p>
            <p style="margin-top:6px;">Di Tempat</p>
        </div>

        <div class="salutation">
            <p>Assalamualaikum Warahmatullahi Wabarakatuh</p>
        </div>

        <div class="isi">
            <p class="indent">
                Salam sejahtera, semoga Bapak/Ibu {{ $tujuan_kepada ?? 'Pemilik Dapur SPPG' }} selalu dalam lindungan Tuhan YME. Aamiin.
            </p>

            <p class="indent" style="margin-top:4px;">
                Berkaitan dengan Program Sertifikat Halal Gratis (SEHATI) sebanyak 1 Juta Pelaku Usaha Mikro Kecil (UMK) 
                di Tahun 2025 sebagai Program Prioritas Pemerintah yang mengacu pada UU No. 33 Tahun 2014 tentang Jaminan Produk Halal 
                dan Peraturan Pemerintah No. 39 Tahun 2021 tentang Penyelenggaraan Bidang Jaminan Produk Halal, 
                maka kami Lembaga Pendamping PPH UIN Sunan Kalijaga Menugaskan kepada:
            </p>

            <table class="data-table">
                <tr>
                    <td style="width:130px;">Nama</td>
                    <td style="width:10px;">:</td>
                    <td>{{ $pendamping->nama ?? 'Sumantri. ST' }}</td>
                </tr>
                <tr>
                    <td>No Registrasi</td>
                    <td>:</td>
                    <td>{{ $pendamping->no_registrasi ?? '2205002019' }}</td>
                </tr>
                <tr>
                    <td>Jabatan</td>
                    <td>:</td>
                    <td>Pendamping PPH UIN Sunan Kalijaga Yogyakarta</td>
                </tr>
                <tr>
                    <td>Alamat</td>
                    <td>:</td>
                    <td>{{ $pendamping->alamat ?? 'Jono RT 04 Jono, Tanon, Sragen, Jawa Tengah' }}</td>
                </tr>
                <tr>
                    <td>No. Hp</td>
                    <td>:</td>
                    <td>{{ $pendamping->no_hp ?? '081228228874' }}</td>
                </tr>
            </table>

            <p style="margin-top:4px;">
                Agar dapat melaksanakan <strong>Sosialisasi dan Pendampingan Sertifikasi Halal</strong> di lingkungan yang Bapak/Ibu pimpin.
            </p>

            <div class="closing">
                <p style="text-indent: 5mm;">Demikian surat pengantar ini kami sampaikan, atas perhatian dan kerjasamanya disampaikan terimakasih</p>
            </div>

            <div style="margin-top:4px;">
                <p>Wassalamualaikum Warahmatullahi Wabarakatuh</p>
            </div>
        </div>

        <div class="ttd">
            <p class="place-time">Yogyakarta, {{ $tanggal ?? '07 Oktober 2025' }}</p>
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
