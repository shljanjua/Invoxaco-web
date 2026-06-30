<?php

namespace App\Services;

/**
 * Handles uploads of sellable product files (e-books, templates,
 * documents, archives). Files are stored OUTSIDE the public webroot
 * in storage/products and are only ever served through the
 * authenticated/tokenised download controller, never linked directly.
 */
class ProductFileUploader
{
    private const ALLOWED_MIME = [
        'application/pdf' => 'pdf',
        'application/epub+zip' => 'epub',
        'application/x-mobipocket-ebook' => 'mobi',
        'application/zip' => 'zip',
        'application/x-zip-compressed' => 'zip',
        'application/msword' => 'doc',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'docx',
        'application/vnd.ms-excel' => 'xls',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => 'xlsx',
        'application/vnd.ms-powerpoint' => 'ppt',
        'application/vnd.openxmlformats-officedocument.presentationml.presentation' => 'pptx',
        'text/plain' => 'txt',
        'text/csv' => 'csv',
        'image/png' => 'png',
        'image/jpeg' => 'jpg',
    ];

    private const MAX_BYTES = 100 * 1024 * 1024; // 100 MB

    public static function storageDir(): string
    {
        return __DIR__ . '/../../storage/products';
    }

    /**
     * Stores an uploaded product file.
     *
     * @return array{file_path:string,file_name:string,file_size:int,file_mime:string}
     */
    public static function store(array $file): array
    {
        if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
            throw new \RuntimeException('No file was uploaded or the upload failed.');
        }

        if (($file['size'] ?? 0) > self::MAX_BYTES) {
            throw new \RuntimeException('File is too large. Maximum size is 100MB.');
        }

        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->file($file['tmp_name']);

        if (!isset(self::ALLOWED_MIME[$mime])) {
            throw new \RuntimeException('Unsupported file type. Allowed: PDF, EPUB, MOBI, ZIP, Word, Excel, PowerPoint, TXT, CSV, PNG, JPG.');
        }

        $extension = self::ALLOWED_MIME[$mime];
        $stored = bin2hex(random_bytes(20)) . '.' . $extension;

        $dir = self::storageDir();
        if (!is_dir($dir)) {
            mkdir($dir, 0775, true);
        }

        $destination = $dir . '/' . $stored;

        if (!move_uploaded_file($file['tmp_name'], $destination)) {
            // Fallback for non-HTTP-uploaded files (e.g. seed scripts/tests)
            if (!@rename($file['tmp_name'], $destination)) {
                throw new \RuntimeException('Failed to save the uploaded file.');
            }
        }

        return [
            'file_path' => $stored,
            'file_name' => self::sanitizeName((string) ($file['name'] ?? $stored)),
            'file_size' => (int) ($file['size'] ?? filesize($destination)),
            'file_mime' => $mime,
        ];
    }

    public static function delete(?string $storedName): void
    {
        if (!$storedName) {
            return;
        }

        $path = self::storageDir() . '/' . basename($storedName);
        if (is_file($path)) {
            unlink($path);
        }
    }

    public static function absolutePath(string $storedName): string
    {
        return self::storageDir() . '/' . basename($storedName);
    }

    private static function sanitizeName(string $name): string
    {
        $name = basename($name);
        $name = preg_replace('/[^A-Za-z0-9._ \-]/', '', $name) ?: 'download';

        return mb_substr($name, 0, 180);
    }
}
