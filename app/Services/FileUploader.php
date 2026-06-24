<?php

namespace App\Services;

class FileUploader
{
    private const ALLOWED_MIME = [
        'image/png' => 'png',
        'image/jpeg' => 'jpg',
        'image/webp' => 'webp',
    ];

    private const MAX_BYTES = 2 * 1024 * 1024;

    public static function storeImage(array $file, string $subdir): ?string
    {
        if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
            return null;
        }

        if ($file['size'] > self::MAX_BYTES) {
            throw new \RuntimeException('File is too large. Maximum size is 2MB.');
        }

        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->file($file['tmp_name']);

        if (!isset(self::ALLOWED_MIME[$mime])) {
            throw new \RuntimeException('Only PNG, JPG, and WEBP images are allowed.');
        }

        $extension = self::ALLOWED_MIME[$mime];
        $filename = bin2hex(random_bytes(16)) . '.' . $extension;
        $destination = __DIR__ . '/../../public/uploads/' . $subdir . '/' . $filename;

        if (!move_uploaded_file($file['tmp_name'], $destination)) {
            throw new \RuntimeException('Failed to save the uploaded file.');
        }

        return $filename;
    }

    public static function storeDataUrlImage(string $dataUrl, string $subdir): ?string
    {
        if (!preg_match('/^data:image\/(png|jpe?g|webp);base64,(.+)$/', $dataUrl, $matches)) {
            return null;
        }

        $extension = $matches[1] === 'jpeg' ? 'jpg' : $matches[1];
        $binary = base64_decode($matches[2], true);

        if ($binary === false || $binary === '') {
            return null;
        }

        if (strlen($binary) > self::MAX_BYTES) {
            throw new \RuntimeException('Signature image is too large. Maximum size is 2MB.');
        }

        $filename = bin2hex(random_bytes(16)) . '.' . $extension;
        $destination = __DIR__ . '/../../public/uploads/' . $subdir . '/' . $filename;

        if (file_put_contents($destination, $binary) === false) {
            throw new \RuntimeException('Failed to save the signature image.');
        }

        return $filename;
    }

    public static function delete(string $subdir, ?string $filename): void
    {
        if (!$filename) {
            return;
        }

        $path = __DIR__ . '/../../public/uploads/' . $subdir . '/' . $filename;

        if (is_file($path)) {
            unlink($path);
        }
    }
}
