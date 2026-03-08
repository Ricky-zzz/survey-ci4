<?php

namespace App\Services;

use App\Models\FileModel;

class FileUploaderService
{
    private array  $allowedExtensions = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'jpg', 'jpeg', 'png'];
    private int    $maxFileSize       = 5242880; // 5 MB
    private string $uploadPath;

    public function __construct()
    {
        $this->uploadPath = FCPATH . 'uploads/survey-responses/';
    }

    public function validateFile(\CodeIgniter\HTTP\Files\UploadedFile $file): array
    {
        $errors = [];

        if (! $file->isValid()) {
            $errors[] = $file->getErrorString();
            return $errors;
        }

        $ext = strtolower($file->getClientExtension());
        if (! in_array($ext, $this->allowedExtensions, true)) {
            $errors[] = 'File type not allowed. Allowed: ' . implode(', ', $this->allowedExtensions);
        }

        if ($file->getSize() > $this->maxFileSize) {
            $errors[] = 'File size exceeds 5 MB limit.';
        }

        return $errors;
    }

    public function uploadFile(\CodeIgniter\HTTP\Files\UploadedFile $file, int $respondentId, int $questionId): array
    {
        $errors = $this->validateFile($file);
        if (! empty($errors)) {
            return ['success' => false, 'errors' => $errors];
        }

        $ext      = strtolower($file->getClientExtension());
        $newName  = 'r' . $respondentId . '_q' . $questionId . '_' . time() . '_' . random_int(1000, 9999) . '.' . $ext;

        if (! $file->move($this->uploadPath, $newName)) {
            return ['success' => false, 'errors' => ['Failed to move uploaded file.']];
        }

        $fileModel = new FileModel();
        $fileId    = $fileModel->insert([
            'respondent_id'     => $respondentId,
            'question_id'       => $questionId,
            'file_path'         => 'uploads/survey-responses/' . $newName,
            'original_filename' => $file->getName(),
            'file_size'         => $file->getSize(),
            'file_type'         => $file->getClientMimeType(),
        ]);

        return [
            'success'   => true,
            'file_id'   => $fileId,
            'file_path' => 'uploads/survey-responses/' . $newName,
            'filename'  => $file->getName(),
        ];
    }

    public function deleteFile(int $fileId): array
    {
        $fileModel = new FileModel();
        $file      = $fileModel->find($fileId);

        if (! $file) {
            return ['success' => false, 'error' => 'File not found.'];
        }

        $fullPath = FCPATH . $file['file_path'];
        if (is_file($fullPath)) {
            unlink($fullPath);
        }

        $fileModel->delete($fileId);

        return ['success' => true];
    }
}
