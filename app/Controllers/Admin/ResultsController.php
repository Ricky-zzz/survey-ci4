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

        $ageAnalytics     = $this->buildAgeDistribution($respondents);
        $addressAnalytics = $this->buildAddressDistribution($respondents);

        return view('admin/results/analytics', [
            'survey'              => $survey,
            'stats'               => $stats,
            'respondents'         => $respondents,
            'questionAnalytics'   => $questionAnalytics,
            'ageAnalytics'        => $ageAnalytics,
            'addressAnalytics'    => $addressAnalytics,
            'filters'             => [
                'age_min' => $ageMin,
                'age_max' => $ageMax,
                'address' => $address,
            ],
        ]);
    }

    private function buildAgeDistribution(array $respondents): array
    {
        $ranges = [
            ['label' => '1-21', 'min' => 1, 'max' => 21],
            ['label' => '22-35', 'min' => 22, 'max' => 35],
            ['label' => '36-59', 'min' => 36, 'max' => 59],
            ['label' => '60+', 'min' => 60, 'max' => null],
        ];

        $counts = array_fill(0, count($ranges), 0);
        $unknown = 0;

        foreach ($respondents as $respondent) {
            $ageValue = $respondent['age'] ?? null;
            if ($ageValue === null || $ageValue === '') {
                $unknown++;
                continue;
            }

            $age = (int) $ageValue;
            foreach ($ranges as $idx => $range) {
                $min = $range['min'];
                $max = $range['max'];

                if ($age >= $min && ($max === null || $age <= $max)) {
                    $counts[$idx]++;
                    break;
                }
            }
        }

        $items = [];
        $total = array_sum($counts) + $unknown;

        foreach ($ranges as $idx => $range) {
            $count = $counts[$idx];
            $items[] = [
                'label'   => $range['label'],
                'count'   => $count,
                'percent' => $total > 0 ? round(($count / $total) * 100) : 0,
            ];
        }

        if ($unknown > 0) {
            $items[] = [
                'label'   => 'Unknown',
                'count'   => $unknown,
                'percent' => $total > 0 ? round(($unknown / $total) * 100) : 0,
            ];
        }

        return [
            'total' => $total,
            'items' => $items,
        ];
    }

    private function buildAddressDistribution(array $respondents): array
    {
        $counts = [];
        $labels = [];
        $unknown = 0;

        foreach ($respondents as $respondent) {
            $address = isset($respondent['address']) ? trim((string) $respondent['address']) : '';
            if ($address === '') {
                $unknown++;
                continue;
            }

            $key = $this->normalizeAddressKey($address);
            if ($key === '') {
                $unknown++;
                continue;
            }

            if (! isset($labels[$key])) {
                $labels[$key] = $this->normalizeAddressLabel($address);
            }

            if (! isset($counts[$key])) {
                $counts[$key] = 0;
            }

            $counts[$key]++;
        }

        arsort($counts);

        $items = [];
        $total = array_sum($counts) + $unknown;
        foreach ($counts as $key => $count) {
            $items[] = [
                'label'   => $labels[$key] ?? 'Unknown',
                'count'   => $count,
                'percent' => $total > 0 ? round(($count / $total) * 100) : 0,
            ];
        }

        if ($unknown > 0) {
            $items[] = [
                'label'   => 'Unknown',
                'count'   => $unknown,
                'percent' => $total > 0 ? round(($unknown / $total) * 100) : 0,
            ];
        }

        return [
            'total' => $total,
            'items' => $items,
        ];
    }

    private function normalizeAddressKey(string $address): string
    {
        $key = strtolower($address);
        $key = preg_replace('/[^a-z0-9\s]/i', ' ', $key);
        $key = preg_replace('/\s+/', ' ', trim($key));

        return $key;
    }

    private function normalizeAddressLabel(string $address): string
    {
        $label = preg_replace('/\s*,\s*/', ', ', trim($address));
        $label = preg_replace('/\s+/', ' ', $label);

        return $label;
    }

    public function textResponses(int $surveyId, int $questionId)
    {
        helper('survey');

        $survey = (new SurveyModel())->getSurveyWithSections($surveyId);
        $question = (new QuestionModel())->find($questionId);

        if (! $survey || ! $question) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $ageMin  = $this->request->getVar('age_min') ? (int) $this->request->getVar('age_min') : null;
        $ageMax  = $this->request->getVar('age_max') ? (int) $this->request->getVar('age_max') : null;
        $address = $this->request->getVar('address') ? trim($this->request->getVar('address')) : null;

        $respondents = (new RespondentModel())->getCompletedForSurveyFiltered($surveyId, $ageMin, $ageMax, $address);
        $respondentIds = array_column($respondents, 'id');

        $responses = [];
        if (!empty($respondentIds)) {
            $responses = (new ResponseModel())->whereIn('respondent_id', $respondentIds)
                                              ->where('question_id', $questionId)
                                              ->findAll();
        }

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
