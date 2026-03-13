<?php

namespace App\Services;

use App\Models\QuestionModel;
use App\Models\RespondentModel;
use App\Models\ResponseModel;
use App\Models\SectionModel;

class ResponseService
{
    private ResponseModel  $responseModel;
    private QuestionModel  $questionModel;
    private RespondentModel $respondentModel;

    public function __construct()
    {
        $this->responseModel   = new ResponseModel();
        $this->questionModel   = new QuestionModel();
        $this->respondentModel = new RespondentModel();
    }

    /**
     * Validate required questions have been answered.
     * Returns array of validation errors keyed by question_id.
     * Pass both $responses (POST data) and $files (FILES array) for file upload validation.
     */
    public function validateResponses(int $surveyId, array $responses, array $files = []): array
    {
        $sectionModel = new SectionModel();
        $sections     = $sectionModel->getSectionsForSurvey($surveyId);
        $errors       = [];

        foreach ($sections as $section) {
            $questions = $this->questionModel->getBySection((int) $section['id']);
            foreach ($questions as $question) {
                if (! $question['required']) {
                    continue;
                }
                $qid    = (int) $question['id'];
                
                // For file uploads, check in $files['file_response']
                if ($question['type'] === 'file_upload') {
                    $hasFile = ! empty($files['file_response'][$qid])
                        && ($files['file_response'][$qid] instanceof \CodeIgniter\HTTP\Files\UploadedFile)
                        && $files['file_response'][$qid]->isValid();
                    
                    if (! $hasFile) {
                        $errors[$qid] = esc($question['question_text']) . ' (required)';
                    }
                } else {
                    // For text, radio, checkbox, etc., check in $responses
                    $answer = $responses[$qid] ?? null;
                    if ($answer === null || $answer === '') {
                        $errors[$qid] = esc($question['question_text']) . ' (required)';
                    }
                }
            }
        }

        return $errors;
    }

    /**
     * Persist all non-file responses for a respondent.
     */
    public function saveResponses(int $respondentId, array $responses): array
    {
        foreach ($responses as $questionId => $answer) {
            $this->responseModel->saveResponse(
                $respondentId,
                (int) $questionId,
                $answer !== '' ? (string) $answer : null
            );
        }

        return ['success' => true];
    }

    /**
     * Mark a respondent as having submitted their survey.
     */
    public function completeRespondent(int $respondentId): void
    {
        $this->respondentModel->update($respondentId, [
            'submitted_at' => date('Y-m-d H:i:s'),
        ]);
    }

    /**
     * Create a new respondent record with demographics and return id.
     */
    public function createRespondent(int $surveyId, array $demographics = []): int
    {
        $data = ['survey_id' => $surveyId];
        
        if (!empty($demographics)) {
            $data['fullname'] = $demographics['fullname'] ?? null;
            $data['email']    = $demographics['email'] ?? null;
            $data['address']  = $demographics['address'] ?? null;
            $data['age']      = $demographics['age'] ?? null;
        }
        
        return (int) $this->respondentModel->insert($data);
    }

    /**
     * Return responses for a respondent keyed by question_id.
     */
    public function getAnswersKeyed(int $respondentId): array
    {
        return $this->responseModel->getByRespondentKeyed($respondentId);
    }
}
