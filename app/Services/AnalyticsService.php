<?php

namespace App\Services;

use App\Models\QuestionModel;
use App\Models\QuestionOptionModel;
use App\Models\RespondentModel;
use App\Models\ResponseModel;

class AnalyticsService
{
    private ResponseModel         $responseModel;
    private RespondentModel       $respondentModel;
    private QuestionModel         $questionModel;
    private QuestionOptionModel   $questionOptionModel;

    public function __construct()
    {
        $this->responseModel       = new ResponseModel();
        $this->respondentModel     = new RespondentModel();
        $this->questionModel       = new QuestionModel();
        $this->questionOptionModel = new QuestionOptionModel();
    }

    public function getSurveyStats(int $surveyId): array
    {
        $stats = $this->respondentModel->getCompletionStats($surveyId);

        return [
            'total_respondents' => $stats['total'],
            'completed'         => $stats['completed'],
            'pending'           => $stats['pending'],
            'completion_rate'   => $stats['total'] > 0
                ? round(($stats['completed'] / $stats['total']) * 100)
                : 0,
        ];
    }

    public function getQuestionStats(int $questionId): array
    {
        $question  = $this->questionModel->find($questionId);
        if (! $question) {
            return [];
        }

        $responses = $this->responseModel->getByQuestion($questionId);

        return match ($question['type']) {
            QuestionModel::TYPE_SCALE           => $this->getScaleStats($responses, $questionId),
            QuestionModel::TYPE_MULTIPLE_CHOICE => $this->getChoiceStats($responses, $questionId),
            QuestionModel::TYPE_YESNO           => $this->getYesNoStats($responses),
            default                             => ['count' => count($responses)],
        };
    }

    private function getScaleStats(array $responses, int $questionId): array
    {
        $values = array_filter(array_column($responses, 'answer_value'));
        $distribution = array_count_values($values);

        // Get all options for this question
        $options = $this->questionOptionModel->getForQuestion($questionId);
        
        // Build full distribution with all options (0 if not selected)
        $fullDistribution = [];
        foreach ($options as $opt) {
            $optValue = $opt['value'] ?? $opt['option_text'];
            $count = $distribution[$optValue] ?? 0;
            
            // Fuzzy match if exact didn't work
            if ($count === 0) {
                foreach (array_keys($distribution) as $responseValue) {
                    if (strpos($responseValue, substr($optValue, 0, 50)) === 0) {
                        $count = $distribution[$responseValue];
                        break;
                    }
                }
            }
            
            $fullDistribution[$optValue] = $count;
        }

        if (empty($values)) {
            return ['average' => 0, 'count' => 0, 'distribution' => $fullDistribution];
        }

        // Cast values to integers for numeric operations
        $numericValues = array_map('intval', $values);

        return [
            'average'      => round(array_sum($numericValues) / count($numericValues), 2),
            'count'        => count($numericValues),
            'distribution' => $fullDistribution,
        ];
    }

    private function getChoiceStats(array $responses, int $questionId): array
    {
        $values       = array_filter(array_column($responses, 'answer_value'));
        $distribution = array_count_values($values);
        $total        = count($values);

        // Get all options for this question
        $options = $this->questionOptionModel->getForQuestion($questionId);

        $withPercent = [];
        foreach ($options as $opt) {
            $optValue = $opt['value'] ?? $opt['option_text'];
            $optText = $opt['option_text'] ?? $opt['value'];
            
            // Try exact match first, then fuzzy match with first 50 chars
            $count = $distribution[$optValue] ?? 0;
            if ($count === 0) {
                foreach (array_keys($distribution) as $responseValue) {
                    if (strpos($responseValue, substr($optValue, 0, 50)) === 0) {
                        $count = $distribution[$responseValue];
                        break;
                    }
                }
            }
            
            $withPercent[$optText] = [
                'count'   => $count,
                'percent' => $total > 0 ? round(($count / $total) * 100) : 0,
            ];
        }

        return ['count' => $total, 'distribution' => $withPercent];
    }

    private function getYesNoStats(array $responses): array
    {
        $values = array_filter(array_column($responses, 'answer_value'));
        $yes    = count(array_filter($values, fn($v) => strtolower($v) === 'yes'));
        $no     = count(array_filter($values, fn($v) => strtolower($v) === 'no'));
        $total  = count($values);

        return [
            'yes'         => $yes,
            'no'          => $no,
            'total'       => $total,
            'yes_percent' => $total > 0 ? round(($yes / $total) * 100) : 0,
            'no_percent'  => $total > 0 ? round(($no  / $total) * 100) : 0,
        ];
    }
}
