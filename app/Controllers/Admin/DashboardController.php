<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\SurveyModel;
use App\Services\AnalyticsService;

class DashboardController extends BaseController
{
    public function index()
    {
        $adminId  = (int) session()->get('admin_id');
        $model    = new SurveyModel();
        $analytics = new AnalyticsService();

        $surveys = $model->getByAdmin($adminId);

        foreach ($surveys as &$survey) {
            $survey['stats'] = $analytics->getSurveyStats((int) $survey['id']);
        }

        return view('admin/dashboard', [
            'surveys' => $surveys,
        ]);
    }
}
