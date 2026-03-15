<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\FileModel;
use App\Models\QuestionModel;
use App\Models\RespondentModel;
use App\Models\ResponseModel;
use App\Models\SurveyModel;

class ResultsController extends BaseController
{
    public function index(int $surveyId)
    {
        $survey = (new SurveyModel())->getSurveyWithSections($surveyId);
        if (! $survey) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        // Get filter params from query string
        $ageMin  = $this->request->getVar('age_min') ? (int) $this->request->getVar('age_min') : null;
        $ageMax  = $this->request->getVar('age_max') ? (int) $this->request->getVar('age_max') : null;
        $address = $this->request->getVar('address') ? trim($this->request->getVar('address')) : null;

        $stats       = (new \App\Services\AnalyticsService())->getSurveyStats($surveyId);
        $respondents = (new RespondentModel())->getCompletedForSurveyFiltered($surveyId, $ageMin, $ageMax, $address);

        return view('admin/results/index', [
            'survey'      => $survey,
            'stats'       => $stats,
            'respondents' => $respondents,
            'filters'     => [
                'age_min' => $ageMin,
                'age_max' => $ageMax,
                'address' => $address,
            ],
        ]);
    }

    public function show(int $surveyId, int $respondentId)
    {
        helper('survey');

        $survey     = (new SurveyModel())->getSurveyWithSections($surveyId);
        $respondent = (new RespondentModel())->find($respondentId);

        if (! $survey || ! $respondent) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $responses = (new ResponseModel())->getByRespondentKeyed($respondentId);
        $files     = (new FileModel())->getByRespondent($respondentId);

        foreach ($survey['sections'] as $sIdx => $section) {
            $questionsWithOptions = [];
            $questionsByType = ['scale' => [], 'multiple_choice' => []];
            
            foreach ($section['questions'] as $qIdx => $q) {
                if (in_array($q['type'], ['scale', 'multiple_choice'])) {
                    $questionsByType[$q['type']][] = $qIdx;
                    if (!empty($q['options'])) {
                        $questionsWithOptions[$q['type']] = $q['options'];
                    }
                }
            }
            
            foreach (['scale', 'multiple_choice'] as $type) {
                if (isset($questionsWithOptions[$type])) {
                    foreach ($questionsByType[$type] as $qIdx) {
                        if (empty($survey['sections'][$sIdx]['questions'][$qIdx]['options'])) {
                            $survey['sections'][$sIdx]['questions'][$qIdx]['options'] = $questionsWithOptions[$type];
                        }
                    }
                }
            }
        }

        $resolvedResponses = [];
        foreach ($survey['sections'] as $section) {
            foreach ($section['questions'] as $q) {
                if (isset($responses[$q['id']])) {
                    $resolvedResponses[$q['id']] = resolveAnswerLabel($q, $responses[$q['id']]);
                }
            }
        }

        $filesByQuestion = [];
        foreach ($files as $file) {
            $filesByQuestion[$file['question_id']][] = $file;
        }

        return view('admin/results/show', [
            'survey'          => $survey,
            'respondent'      => $respondent,
            'responses'       => $resolvedResponses,
            'filesByQuestion' => $filesByQuestion,
        ]);
    }

    public function deleteRespondent(int $surveyId, int $respondentId)
    {
        (new RespondentModel())->delete($respondentId);
        return redirect()->to(base_url('admin/surveys/' . $surveyId . '/results'))
                         ->with('success', 'Response deleted.');
    }

    public function analytics(int $surveyId)
    {
        $survey = (new SurveyModel())->getSurveyWithSections($surveyId);
        if (! $survey) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        // Get filter params from query string
        $ageMin  = $this->request->getVar('age_min') ? (int) $this->request->getVar('age_min') : null;
        $ageMax  = $this->request->getVar('age_max') ? (int) $this->request->getVar('age_max') : null;
        $address = $this->request->getVar('address') ? trim($this->request->getVar('address')) : null;

        $stats       = (new \App\Services\AnalyticsService())->getSurveyStats($surveyId);
        $respondents = (new RespondentModel())->getCompletedForSurveyFiltered($surveyId, $ageMin, $ageMax, $address);

        // Get analytics for this survey
        $analyticsService = new \App\Services\AnalyticsService();
        $questionAnalytics = [];
        
        foreach ($survey['sections'] as $section) {
            foreach ($section['questions'] as $q) {
                $questionAnalytics[$q['id']] = [
                    'question' => $q,
                    'stats' => $analyticsService->getQuestionStats($q['id']),
                ];
            }
        }

        return view('admin/results/analytics', [
            'survey'              => $survey,
            'stats'               => $stats,
            'respondents'         => $respondents,
            'questionAnalytics'   => $questionAnalytics,
            'filters'             => [
                'age_min' => $ageMin,
                'age_max' => $ageMax,
                'address' => $address,
            ],
        ]);
    }

    public function textResponses(int $surveyId, int $questionId)
    {
        helper('survey');

        $survey = (new SurveyModel())->getSurveyWithSections($surveyId);
        $question = (new QuestionModel())->find($questionId);

        if (! $survey || ! $question) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        // Get filter params from query string
        $ageMin  = $this->request->getVar('age_min') ? (int) $this->request->getVar('age_min') : null;
        $ageMax  = $this->request->getVar('age_max') ? (int) $this->request->getVar('age_max') : null;
        $address = $this->request->getVar('address') ? trim($this->request->getVar('address')) : null;

        // Get respondents matching filters
        $respondents = (new RespondentModel())->getCompletedForSurveyFiltered($surveyId, $ageMin, $ageMax, $address);
        $respondentIds = array_column($respondents, 'id');

        // Get responses for this question from filtered respondents
        $responses = [];
        if (!empty($respondentIds)) {
            $responses = (new ResponseModel())->whereIn('respondent_id', $respondentIds)
                                              ->where('question_id', $questionId)
                                              ->findAll();
        }

        // Enrich responses with respondent data
        $respondentMap = array_column($respondents, null, 'id');
        $enrichedResponses = [];
        foreach ($responses as $response) {
            $enrichedResponses[] = [
                'respondent' => $respondentMap[$response['respondent_id']] ?? [],
                'answer' => $response['answer_value'],
            ];
        }

        return view('admin/results/text_responses', [
            'survey'    => $survey,
            'question'  => $question,
            'responses' => $enrichedResponses,
            'filters'   => [
                'age_min' => $ageMin,
                'age_max' => $ageMax,
                'address' => $address,
            ],
        ]);
    }

    public function fileResponses(int $surveyId, int $questionId)
    {
        $survey = (new SurveyModel())->getSurveyWithSections($surveyId);
        $question = (new QuestionModel())->find($questionId);

        if (! $survey || ! $question) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        // Get filter params from query string
        $ageMin  = $this->request->getVar('age_min') ? (int) $this->request->getVar('age_min') : null;
        $ageMax  = $this->request->getVar('age_max') ? (int) $this->request->getVar('age_max') : null;
        $address = $this->request->getVar('address') ? trim($this->request->getVar('address')) : null;

        // Get respondents matching filters
        $respondents = (new RespondentModel())->getCompletedForSurveyFiltered($surveyId, $ageMin, $ageMax, $address);
        $respondentIds = array_column($respondents, 'id');

        // Get files for this question from filtered respondents
        $files = [];
        if (!empty($respondentIds)) {
            $files = (new FileModel())->whereIn('respondent_id', $respondentIds)
                                      ->where('question_id', $questionId)
                                      ->findAll();
        }

        // Enrich files with respondent data
        $respondentMap = array_column($respondents, null, 'id');
        $enrichedFiles = [];
        foreach ($files as $file) {
            $enrichedFiles[] = [
                'respondent' => $respondentMap[$file['respondent_id']] ?? [],
                'file' => $file,
            ];
        }

        return view('admin/results/file_responses', [
            'survey'    => $survey,
            'question'  => $question,
            'files'     => $enrichedFiles,
            'filters'   => [
                'age_min' => $ageMin,
                'age_max' => $ageMax,
                'address' => $address,
            ],
        ]);
    }
}
