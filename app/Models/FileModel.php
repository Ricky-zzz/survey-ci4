<?php

namespace App\Models;

use CodeIgniter\Model;

class FileModel extends Model
{
    protected $table         = 'files';
    protected $primaryKey    = 'id';
    protected $allowedFields = [
        'respondent_id', 'question_id', 'file_path',
        'original_filename', 'file_size', 'file_type',
    ];
    protected $useTimestamps = true;
    protected $createdField  = 'uploaded_at';
    protected $updatedField  = '';

    public function getByRespondent(int $respondentId): array
    {
        return $this->where('respondent_id', $respondentId)->findAll();
    }

    public function getByQuestion(int $questionId): array
    {
        return $this->where('question_id', $questionId)->findAll();
    }

    public function getByRespondentAndQuestion(int $respondentId, int $questionId): array
    {
        return $this->where('respondent_id', $respondentId)
                    ->where('question_id', $questionId)
                    ->findAll();
    }
}
