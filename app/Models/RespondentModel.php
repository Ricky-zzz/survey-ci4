<?php

namespace App\Models;

use CodeIgniter\Model;

class RespondentModel extends Model
{
    protected $table         = 'respondents';
    protected $primaryKey    = 'id';
    protected $allowedFields = ['survey_id', 'fullname', 'email', 'address', 'age', 'submitted_at'];
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    public function getForSurvey(int $surveyId): array
    {
        return $this->where('survey_id', $surveyId)
                    ->orderBy('created_at', 'DESC')
                    ->findAll();
    }

    public function getCompletedForSurvey(int $surveyId): array
    {
        return $this->where('survey_id', $surveyId)
                    ->where('submitted_at IS NOT NULL')
                    ->orderBy('submitted_at', 'DESC')
                    ->findAll();
    }

    public function getCompletionStats(int $surveyId): array
    {
        $total     = $this->where('survey_id', $surveyId)->countAllResults(false);
        $completed = $this->where('survey_id', $surveyId)
                          ->where('submitted_at IS NOT NULL')
                          ->countAllResults();

        return [
            'total'     => $total,
            'completed' => $completed,
            'pending'   => $total - $completed,
        ];
    }

    public function getCompletedForSurveyFiltered(int $surveyId, ?int $ageMin = null, ?int $ageMax = null, ?string $address = null): array
    {
        $query = $this->where('survey_id', $surveyId)
                      ->where('submitted_at IS NOT NULL');

        if ($ageMin !== null) {
            $query = $query->where('age >=', $ageMin);
        }

        if ($ageMax !== null) {
            $query = $query->where('age <=', $ageMax);
        }

        if ($address !== null && $address !== '') {
            $query = $query->like('address', $address);
        }

        return $query->orderBy('submitted_at', 'DESC')
                     ->findAll();
    }
}
