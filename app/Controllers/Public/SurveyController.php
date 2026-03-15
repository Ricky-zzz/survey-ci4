<?php

namespace App\Controllers\Public;

use App\Controllers\BaseController;
use App\Models\SurveyModel;
use App\Services\FileUploaderService;
use App\Services\ResponseService;

class SurveyController extends BaseController
{
    private SurveyModel     $surveyModel;
    private ResponseService $responseService;

    public function __construct()
    {
        $this->surveyModel     = new SurveyModel();
        $this->responseService = new ResponseService();
    }

    /** GET /s */
    public function index()
    {
        $surveys = $this->surveyModel
            ->where('is_public', 1)
            ->where('is_active', 1)
            ->orderBy('created_at', 'DESC')
            ->findAll();

        return view('public/index', ['surveys' => $surveys]);
    }

    /** GET /s/{id} */
    public function show(int $id)
    {
        $this->getPublicSurvey($id);
        $full = $this->surveyModel->getSurveyWithSections($id);

        return view('public/survey', ['survey' => $full]);
    }

    /** POST /s/{id}/submit */
    public function submit(int $id)
    {
        $this->getPublicSurvey($id);
        $responses = $this->request->getPost('responses') ?? [];
        $files     = $this->request->getFiles();
        
        // Extract demographics from form
        $demographicsInput = $this->request->getPost('demographics') ?? [];
        $demographics = [
            'fullname' => trim($demographicsInput['fullname'] ?? ''),
            'email'    => trim($demographicsInput['email'] ?? ''),
            'address'  => trim($demographicsInput['address'] ?? ''),
            'age'      => !empty($demographicsInput['age']) ? (int)$demographicsInput['age'] : null,
        ];

        // Validate demographics
        $errors = [];
        if (empty($demographics['email'])) {
            $errors['demographics_email'] = 'Email is required';
        }
        if (empty($demographics['address'])) {
            $errors['demographics_address'] = 'Address is required';
        }
        if ($demographics['age'] === null || $demographics['age'] === 0) {
            $errors['demographics_age'] = 'Age is required';
        }
        if (!empty($errors)) {
            return redirect()->back()
                ->with('validation_errors', $errors)
                ->withInput();
        }

        $errors = $this->responseService->validateResponses($id, $responses, $files);
        if (! empty($errors)) {
            return redirect()->back()
                ->with('validation_errors', $errors)
                ->withInput();
        }

        $respondentId = $this->responseService->createRespondent($id, $demographics);
        $this->responseService->saveResponses($respondentId, $responses);

        $fileService = new FileUploaderService();
        $files       = $this->request->getFiles();

        if (! empty($files['file_response'])) {
            foreach ($files['file_response'] as $questionId => $file) {
                if ($file instanceof \CodeIgniter\HTTP\Files\UploadedFile && $file->isValid()) {
                    $fileService->uploadFile($file, $respondentId, (int) $questionId);
                }
            }
        }

        $this->responseService->completeRespondent($respondentId);
        session()->setFlashdata('respondent_id', $respondentId);

        return redirect()->to(base_url('s/' . $id . '/thank-you'));
    }

    /** GET /s/{id}/thank-you */
    public function thankYou(int $id)
    {
        helper('survey');
        
        $survey      = $this->getPublicSurvey($id);
        $respondentId = (int) session()->getFlashdata('respondent_id');
        $answers     = [];
        $files       = [];

        if ($respondentId > 0) {
            $full    = $this->surveyModel->getSurveyWithSections($id);
            $rawAnswers = $this->responseService->getAnswersKeyed($respondentId);

            // For each section, find scale/multiple_choice questions and copy options to those that lack them
            foreach ($full['sections'] as $sIdx => $section) {
                $questionsWithOptions = [];
                $questionsByType = ['scale' => [], 'multiple_choice' => []];
                
                // First pass: collect questions and their options
                foreach ($section['questions'] as $qIdx => $q) {
                    if (in_array($q['type'], ['scale', 'multiple_choice'])) {
                        $questionsByType[$q['type']][] = $qIdx;
                        if (!empty($q['options'])) {
                            $questionsWithOptions[$q['type']] = $q['options'];
                        }
                    }
                }
                
                // Second pass: assign options to questions that lack them
                foreach (['scale', 'multiple_choice'] as $type) {
                    if (isset($questionsWithOptions[$type])) {
                        foreach ($questionsByType[$type] as $qIdx) {
                            if (empty($full['sections'][$sIdx]['questions'][$qIdx]['options'])) {
                                $full['sections'][$sIdx]['questions'][$qIdx]['options'] = $questionsWithOptions[$type];
                            }
                        }
                    }
                }
            }

            foreach ($full['sections'] as $section) {
                foreach ($section['questions'] as $q) {
                    if (isset($rawAnswers[$q['id']])) {
                        $answers[$q['id']] = resolveAnswerLabel($q, $rawAnswers[$q['id']]);
                    }
                    
                    // For file uploads, fetch from files table
                    if ($q['type'] === 'file_upload') {
                        $fileModel = new \App\Models\FileModel();
                        $uploadedFiles = $fileModel->getByRespondentAndQuestion($respondentId, $q['id']);
                        if (! empty($uploadedFiles)) {
                            $files[$q['id']] = $uploadedFiles;
                        }
                    }
                }
            }

            return view('public/thank_you', [
                'survey'      => $full,
                'answers'     => $answers,
                'files'       => $files,
                'respondentId'=> $respondentId,
            ]);
        }

        return view('public/thank_you', ['survey' => $survey, 'answers' => [], 'files' => [], 'respondentId' => 0]);
    }

    private function getPublicSurvey(int $id): array
    {
        $survey = $this->surveyModel->where('id', $id)->where('is_active', 1)->first();
        if (! $survey) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        if (! $survey['is_public']) {
            $validated = session()->get('passkey_access') ?? [];
            if (! in_array((int) $id, $validated)) {
                session()->setFlashdata('passcode_error', 'This survey requires a passcode to access.');
                redirect()->to(base_url('/'))->send();
                exit;
            }
        }

        return $survey;
    }
}
