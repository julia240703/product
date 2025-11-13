<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Process\Process;

class PdfCompressorService
{
    /** Opsi default (boleh override lewat argumen $options di compress()) */
    private array $defaults = [
        'target_bytes' => 2 * 1024 * 1024, // 2 MB
        'color_dpi'    => 96,
        'jpeg_q'       => 55,              // 45–60 umumnya aman
        'min_try'      => 1024,            // skip file sangat kecil
        'use_rasterize_fallback' => true,  // perlu ext-imagick terpasang
        'raster_dpi'   => 110,
        'raster_q'     => 55,
    ];

    /**
     * Kompres PDF → simpan ke storage/app/public/pdf/xxxx.pdf
     * return "pdf/xxxx.pdf" (path relatif untuk disimpan ke DB).
     */
    public function compress(UploadedFile $file, array $options = []): string
    {
        $opt = array_merge($this->defaults, $options);

        ini_set('memory_limit', '1024M');
        set_time_limit(300);

        Storage::makeDirectory('public/pdf');
        Storage::makeDirectory('public/pdf_tmp');

        $rawPath = $file->store('public/pdf_tmp');       // ex: public/pdf_tmp/xxx.pdf
        $input   = storage_path('app/'.$rawPath);        // absolut

        $outputName  = 'compressed_'.uniqid().'.pdf';
        $relativeOut = 'pdf/'.$outputName;               // simpan ke DB
        $finalOut    = storage_path('app/public/'.$relativeOut);

        $origSize = @filesize($input) ?: 0;
        if ($origSize > 0 && $origSize < $opt['min_try']) {
            @copy($input, $finalOut);
            @unlink($input);
            return $relativeOut;
        }

        // --- Ghostscript binary ---
        $gs = $this->resolveGs();
        if (!$gs) {
            Log::warning('Ghostscript not found. Saving original.');
            @copy($input, $finalOut);
            @unlink($input);
            return $relativeOut;
        }

        // --- Passes (agresif → paksa re-encode → sedang) ---
        $passes = [
            // PASS 1 — agresif standar (+bicubic)
            [
                "-dPDFSETTINGS=/screen",
                "-dDownsampleColorImages=true", "-dColorImageDownsample=true",
                "-dColorImageDownsampleType=/Bicubic",
                "-dColorImageResolution={$opt['color_dpi']}",
                "-dDownsampleGrayImages=true", "-dGrayImageDownsample=true",
                "-dGrayImageDownsampleType=/Bicubic",
                "-dGrayImageResolution={$opt['color_dpi']}",
                "-dDownsampleMonoImages=true", "-dMonoImageDownsample=true",
                "-dMonoImageResolution=300",
                "-dJPEGQ={$opt['jpeg_q']}",
            ],
            // PASS 2 — PAKSA re-encode (paling efektif utk PDF berisi foto)
            [
                "-dPDFSETTINGS=/screen",
                "-dAutoFilterColorImages=false", "-dEncodeColorImages=true",
                "-dColorImageFilter=/DCTEncode",
                "-dColorImageDownsample=true", "-dColorImageDownsampleType=/Bicubic",
                "-dColorImageResolution=".max(84, min(120, (int)$opt['color_dpi'])),
                "-dAutoFilterGrayImages=false", "-dEncodeGrayImages=true",
                "-dGrayImageFilter=/DCTEncode",
                "-dGrayImageDownsample=true", "-dGrayImageDownsampleType=/Bicubic",
                "-dGrayImageResolution=".max(84, min(120, (int)$opt['color_dpi'])),
                "-dDownsampleMonoImages=true", "-dMonoImageDownsample=true",
                "-dMonoImageResolution=300",
                "-dEncodeMonoImages=true",
                "-dMonoImageFilter=/CCITTFaxEncode",
                "-dJPEGQ=".max(40, min(60, (int)$opt['jpeg_q'] - 5)),
                "-dColorConversionStrategy=RGB", "-dProcessColorModel=/DeviceRGB",
            ],
            // PASS 3 — sedang/fallback
            [
                "-dPDFSETTINGS=/ebook",
                "-dDownsampleColorImages=true", "-dColorImageDownsample=true",
                "-dColorImageDownsampleType=/Bicubic",
                "-dColorImageResolution=".max(96, min(150, (int)$opt['color_dpi'] + 24)),
                "-dDownsampleGrayImages=true", "-dGrayImageDownsample=true",
                "-dGrayImageDownsampleType=/Bicubic",
                "-dGrayImageResolution=".max(96, min(150, (int)$opt['color_dpi'] + 24)),
                "-dDownsampleMonoImages=true", "-dMonoImageDownsample=true",
                "-dMonoImageResolution=300",
                "-dJPEGQ=".max(45, min(70, (int)$opt['jpeg_q'] + 5)),
            ],
        ];

        $common = [
            $gs,
            "-sDEVICE=pdfwrite",
            "-dCompatibilityLevel=1.4",
            "-dNOPAUSE", "-dQUIET", "-dBATCH",
            "-dDetectDuplicateImages=true",
            "-dCompressFonts=true", "-dSubsetFonts=true",
            "-dAutoRotatePages=/None",
            "-sColorConversionStrategy=RGB", "-dProcessColorModel=/DeviceRGB",
            "-dFastWebView=true", // linearized
        ];

        $bestFile = null;
        $bestSize = PHP_INT_MAX;

        $env = ['PATH' => $this->buildPathEnv(getenv('PATH'))];

        foreach ($passes as $opts) {
            $tmp  = storage_path('app/public/pdf/tmp_'.uniqid().'.pdf');
            $args = array_merge($common, $opts, ["-sOutputFile=".$tmp, $input]);

            $proc = new Process($args, null, $env);
            $proc->setTimeout(240);
            $proc->run();

            if (!$proc->isSuccessful() || !file_exists($tmp)) {
                @unlink($tmp);
                continue;
            }

            $sz = @filesize($tmp) ?: PHP_INT_MAX;
            if ($sz < $bestSize) {
                if ($bestFile && file_exists($bestFile)) @unlink($bestFile);
                $bestFile = $tmp;
                $bestSize = $sz;
            }
            if ($sz <= $opt['target_bytes']) break; // cukup kecil
        }

        $usedRasterize = false;

        // Jika belum mengecil & > target → rasterize (opsional, butuh Imagick)
        if ($opt['use_rasterize_fallback']
            && $bestSize >= $origSize
            && $origSize > $opt['target_bytes']
            && extension_loaded('imagick')) {

            $ok = $this->rasterizeWithImagick($input, $finalOut, (int)$opt['raster_dpi'], (int)$opt['raster_q']);
            if ($ok && file_exists($finalOut)) {
                $usedRasterize = true;
                $bestSize = @filesize($finalOut) ?: $bestSize;
            }
        }

        // Finalisasi jika belum rasterize
        if (!$usedRasterize) {
            if ($bestFile && file_exists($bestFile)) {
                if ($origSize > 0 && $bestSize >= $origSize) {
                    @copy($input, $finalOut); // pakai asli bila kompres tidak lebih kecil
                } else {
                    @copy($bestFile, $finalOut);
                }
            } else {
                @copy($input, $finalOut);
            }
        }

        Log::info('PDF compress result', [
            'used_gs'        => $gs,
            'orig_bytes'     => $origSize,
            'best_bytes'     => $bestSize === PHP_INT_MAX ? null : $bestSize,
            'used_rasterize' => $usedRasterize,
            'final_file'     => $finalOut,
        ]);

        // Cleanup
        @unlink($input);
        if ($bestFile && file_exists($bestFile)) @unlink($bestFile);

        return $relativeOut;
    }

    /** Rasterize per halaman (pasti mengecil; butuh ext-imagick + ghostscript) */
    private function rasterizeWithImagick(string $src, string $dest, int $dpi, int $jpegQuality): bool
    {
        try {
            $images = new \Imagick();
            $images->setResolution($dpi, $dpi);
            $images->readImage($src);                   // baca semua halaman
            $images->setImageFormat('pdf');

            foreach ($images as $img) {
                $img->setImageCompression(\Imagick::COMPRESSION_JPEG);
                $img->setImageCompressionQuality($jpegQuality);
                $img->setImageAlphaChannel(\Imagick::ALPHACHANNEL_REMOVE);
                $img->setImageColorspace(\Imagick::COLORSPACE_RGB);
            }
            $images->writeImages($dest, true);
            $images->clear();
            $images->destroy();
            return true;
        } catch (\Throwable $e) {
            Log::error('Rasterize Imagick failed: '.$e->getMessage());
            return false;
        }
    }

    /** Cari Ghostscript tanpa .env */
    private function resolveGs(): ?string
    {
        // 1) via PATH
        foreach (['gswin64c.exe','gswin32c.exe','gs.exe','gs'] as $bin) {
            $found = $this->which($bin);
            if ($found) return $found;
        }
        // 2) lokasi umum (Linux/Mac/Windows)
        foreach ([
            '/usr/bin/gs','/usr/local/bin/gs','/opt/homebrew/bin/gs',
            'C:\Program Files\gs\gs10.06.0\bin\gswin64c.exe', // ← ditambahkan
            'C:\Program Files\gs\gs10.03.1\bin\gswin64c.exe',
            'C:\Program Files\gs\gs10.02.1\bin\gswin64c.exe',
            'C:\Program Files\gs\gs10.01.2\bin\gswin64c.exe',
        ] as $p) {
            if (is_file($p) && is_readable($p)) return $p;
        }
        // 3) coba lagi dengan PATH yang ditambah
        return $this->which('gs', $this->buildPathEnv(getenv('PATH')));
    }

    private function which(string $bin, ?string $pathEnv = null): ?string
    {
        $paths = explode(PATH_SEPARATOR, $pathEnv ?? (string) getenv('PATH'));
        foreach ($paths as $p) {
            $file = rtrim($p, DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR.$bin;
            if (is_file($file) && (is_executable($file) || strtoupper(substr(PHP_OS,0,3))==='WIN')) {
                return $file;
            }
        }
        return null;
    }

    private function buildPathEnv(?string $base): string
    {
        $base = $base ?: '';
        $extra = [
            '/usr/bin','/usr/local/bin','/opt/homebrew/bin',
            'C:\Program Files\gs\gs10.06.0\bin',        // ← ditambahkan
            'C:\Program Files\gs\gs10.03.1\bin',
            'C:\Program Files\gs\gs10.02.1\bin',
            'C:\Program Files\gs\gs10.01.2\bin',
        ];
        $parts = array_filter(array_unique(array_merge(explode(PATH_SEPARATOR,$base), $extra)));
        return implode(PATH_SEPARATOR, $parts);
    }
}