<?php

namespace App\Models;

use CodeIgniter\Model;

class SurveyModel extends Model
{
    protected $table         = 'surveys';
    protected $primaryKey    = 'id';
    protected $allowedFields = ['name', 'description', 'is_public', 'is_active', 'passkey', 'created_by'];
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules = [
        'name'       => 'required|max_length[255]',
        'created_by' => 'required|integer',
    ];

    public function getSurveyWithSections(int $surveyId): ?array
    {
        $survey = $this->find($surveyId);
        if (! $survey) {
            return null;
        }

        $sectionModel = new SectionModel();
        $survey['sections'] = $sectionModel->getSectionsWithQuestions($surveyId);

        return $survey;
    }

    public function generatePasskey(): string
    {
        return bin2hex(random_bytes(10));
    }

    public function getByAdmin(int $adminId): array
    {
        return $this->where('created_by', $adminId)
                    ->orderBy('created_at', 'DESC')
                    ->findAll();
    }
}
