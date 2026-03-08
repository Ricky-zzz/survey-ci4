<?php

namespace App\Services;

use App\Models\SurveyModel;

class SurveyPublisherService
{
    private SurveyModel $surveyModel;

    public function __construct()
    {
        $this->surveyModel = new SurveyModel();
    }

    public function createShareLink(int $surveyId): array
    {
        $survey = $this->surveyModel->find($surveyId);
        if (! $survey) {
            return ['success' => false, 'error' => 'Survey not found.'];
        }

        $this->surveyModel->update($surveyId, ['is_public' => 1, 'is_active' => 1]);

        return [
            'success' => true,
            'url'     => base_url('s/' . $surveyId),
        ];
    }

    public function getSurveyByPasskey(string $passkey): ?array
    {
        return $this->surveyModel->where('passkey', $passkey)
                                 ->where('is_public', 1)
                                 ->first();
    }

    public function revokeShareLink(int $surveyId): void
    {
        $this->surveyModel->update($surveyId, ['is_active' => 0]);
    }
}
