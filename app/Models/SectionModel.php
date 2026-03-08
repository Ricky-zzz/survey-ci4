<?php

namespace App\Models;

use CodeIgniter\Model;

class SectionModel extends Model
{
    protected $table         = 'sections';
    protected $primaryKey    = 'id';
    protected $allowedFields = ['survey_id', 'title', 'description', 'is_respondent_info', 'order_sequence'];
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = '';

    protected $validationRules = [
        'survey_id'      => 'required|integer',
        'title'          => 'required|max_length[255]',
        'order_sequence' => 'required|integer',
    ];

    public function getSectionsForSurvey(int $surveyId): array
    {
        return $this->where('survey_id', $surveyId)
                    ->orderBy('order_sequence', 'ASC')
                    ->findAll();
    }

    public function getSectionsWithQuestions(int $surveyId): array
    {
        $sections      = $this->getSectionsForSurvey($surveyId);
        $questionModel = new QuestionModel();

        foreach ($sections as &$section) {
            $section['questions'] = $questionModel->getQuestionsWithOptions($section['id']);
        }

        return $sections;
    }

    public function getNextOrder(int $surveyId): int
    {
        $result = $this->selectMax('order_sequence')
                       ->where('survey_id', $surveyId)
                       ->first();

        return ($result['order_sequence'] ?? -1) + 1;
    }
}
