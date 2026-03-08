<?php

namespace App\Models;

use CodeIgniter\Model;

class ResponseModel extends Model
{
    protected $table         = 'responses';
    protected $primaryKey    = 'id';
    protected $allowedFields = ['respondent_id', 'question_id', 'answer_value'];
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules = [
        'respondent_id' => 'required|integer',
        'question_id'   => 'required|integer',
    ];

    public function getByRespondent(int $respondentId): array
    {
        return $this->where('respondent_id', $respondentId)->findAll();
    }

    public function getByQuestion(int $questionId): array
    {
        return $this->where('question_id', $questionId)->findAll();
    }

    public function saveResponse(int $respondentId, int $questionId, ?string $answer): bool
    {
        $existing = $this->where('respondent_id', $respondentId)
                         ->where('question_id', $questionId)
                         ->first();

        if ($existing) {
            return $this->update($existing['id'], ['answer_value' => $answer]);
        }

        return (bool) $this->insert([
            'respondent_id' => $respondentId,
            'question_id'   => $questionId,
            'answer_value'  => $answer,
        ]);
    }

    public function getByRespondentKeyed(int $respondentId): array
    {
        $rows   = $this->getByRespondent($respondentId);
        $keyed  = [];
        foreach ($rows as $row) {
            $keyed[$row['question_id']] = $row['answer_value'];
        }
        return $keyed;
    }
}
