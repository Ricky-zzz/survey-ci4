<?php

namespace App\Controllers\Public;

use App\Controllers\BaseController;

class HomeController extends BaseController
{
    public function index()
    {
        // Redirect to admin login
        return redirect()->to(base_url('admin/index'));
    }

    public function accessByPasscode()
    {
        $passcode = trim($this->request->getPost('passcode') ?? '');
        
        if (empty($passcode)) {
            return redirect()->back()->with('passcode_error', 'Please enter a passcode.');
        }

        // Find survey by passcode
        $survey = (new \App\Models\SurveyModel())->where('passkey', $passcode)->first();
        
        if (!$survey) {
            return redirect()->back()->with('passcode_error', 'Invalid passcode. Please try again.');
        }
        
        if (!$survey['is_active']) {
            return redirect()->back()->with('passcode_error', 'This survey is not currently active.');
        }

        // Store survey ID in passkey_access session array for validation on survey page
        $validatedSurveys = session()->get('passkey_access') ?? [];
        $validatedSurveys[] = $survey['id'];
        session()->set('passkey_access', $validatedSurveys);
        
        // Redirect to survey
        return redirect()->to(base_url('s/' . $survey['id']));
    }
}
