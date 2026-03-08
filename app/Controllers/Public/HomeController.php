<?php

namespace App\Controllers\Public;

use App\Controllers\BaseController;
use App\Models\SurveyModel;
use App\Services\SurveyPublisherService;

class HomeController extends BaseController
{
    public function index()
    {
        $surveyModel = new SurveyModel();
        
        // Only show surveys that are public AND active
        $surveys = $surveyModel->where('is_public', 1)
                               ->where('is_active', 1)
                               ->orderBy('created_at', 'DESC')
                               ->findAll();
        
        return view('public/index', ['surveys' => $surveys]);
    }

    public function accessByPasscode()
    {
        $passcode = $this->request->getPost('passcode');
        
        if (!$passcode) {
            return redirect()->back()->with('passcode_error', 'Please enter a passcode');
        }

        $surveyModel = new SurveyModel();
        $survey = $surveyModel->where('passkey', $passcode)
                              ->where('is_active', 1)
                              ->first();

        if (!$survey) {
            return redirect()->back()
                ->with('passcode_error', 'Invalid passcode or survey is not active.');
        }

        // Store this survey ID in session as passkey-validated so show() can allow it
        $validated = session()->get('passkey_access') ?? [];
        $validated[] = (int) $survey['id'];
        session()->set('passkey_access', array_unique($validated));

        return redirect()->to('s/' . $survey['id']);
    }
}
