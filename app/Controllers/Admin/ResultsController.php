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

        $stats       = (new \App\Services\AnalyticsService())->getSurveyStats($surveyId);
        $respondents = (new RespondentModel())->getCompletedForSurvey($surveyId);

        return view('admin/results/index', [
            'survey'      => $survey,
            'stats'       => $stats,
            'respondents' => $respondents,
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
}
