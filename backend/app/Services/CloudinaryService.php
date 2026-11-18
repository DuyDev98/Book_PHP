<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class CloudinaryService
{
    /**
     * Tải ảnh lên Cloudinary nếu có cấu hình API, ngược lại lưu vào public storage
     */
    public function uploadImage(UploadedFile $file, string $folder = 'bookstore'): string
    {
        $cloudName = config('services.cloudinary.cloud_name') ?? env('CLOUDINARY_CLOUD_NAME');
        $apiKey = config('services.cloudinary.api_key') ?? env('CLOUDINARY_API_KEY');
        $apiSecret = config('services.cloudinary.api_secret') ?? env('CLOUDINARY_API_SECRET');
        $uploadPreset = config('services.cloudinary.upload_preset') ?? env('CLOUDINARY_UPLOAD_PRESET');

        // Nếu người dùng đã cung cấp API Cloudinary
        if ($cloudName && ($uploadPreset || ($apiKey && $apiSecret))) {
            try {
                $endpoint = "https://api.cloudinary.com/v1_1/{$cloudName}/image/upload";
                
                $data = [
                    'folder' => $folder,
                ];

                if ($uploadPreset) {
                    $data['upload_preset'] = $uploadPreset;
                } elseif ($apiKey && $apiSecret) {
                    $timestamp = time();
                    $data['timestamp'] = $timestamp;
                    $data['api_key'] = $apiKey;
                    // Chuỗi tạo chữ ký signature
                    $signatureString = "folder={$folder}&timestamp={$timestamp}{$apiSecret}";
                    $data['signature'] = sha1($signatureString);
                }

                $response = Http::attach(
                    'file',
                    file_get_contents($file->getRealPath()),
                    $file->getClientOriginalName()
                )->post($endpoint, $data);

                if ($response->successful()) {
                    $json = $response->json();
                    return $json['secure_url'] ?? $json['url'];
                }

                Log::error('Cloudinary upload error: ' . $response->body());
            } catch (\Exception $e) {
                Log::error('Cloudinary upload exception: ' . $e->getMessage());
            }
        }

        // Fallback lưu local an toàn trong public/uploads
        $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('uploads/books', $filename, 'public');
        return url('storage/' . $path);
    }
}
