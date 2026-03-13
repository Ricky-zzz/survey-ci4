<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\SectionModel;
use App\Models\SurveyModel;
use App\Services\SurveyPublisherService;

class SurveyController extends BaseController
{
    private SurveyModel            $surveyModel;
    private SectionModel           $sectionModel;
    private SurveyPublisherService $publisherService;

    public function __construct()
    {
        $this->surveyModel      = new SurveyModel();
        $this->sectionModel     = new SectionModel();
        $this->publisherService = new SurveyPublisherService();
    }

    public function index()
    {
        $adminId  = (int) session()->get('admin_id');
        $surveys  = $this->surveyModel->getByAdmin($adminId);
        $analytics = new \App\Services\AnalyticsService();

        foreach ($surveys as &$survey) {
            $survey['stats'] = $analytics->getSurveyStats((int) $survey['id']);
        }
        unset($survey);

        return view('admin/surveys/index', ['surveys' => $surveys]);
    }

    public function create()
    {
        return view('admin/surveys/create');
    }

    public function store()
    {
        $rules = ['name' => 'required|max_length[255]'];
        if (! $this->validate($rules)) {
            return redirect()->back()->with('errors', $this->validator->getErrors())->withInput();
        }

        $adminId  = (int) session()->get('admin_id');
        $surveyId = (int) $this->surveyModel->insert([
            'name'        => $this->request->getPost('name'),
            'description' => $this->request->getPost('description'),
            'is_public'   => (int)(bool) $this->request->getPost('is_public'),
            'is_active'   => 0,
            'passkey'     => $this->surveyModel->generatePasskey(),
            'created_by'  => $adminId,
        ]);

        return redirect()->to(base_url('admin/surveys/' . $surveyId . '/edit'))
                         ->with('success', 'Survey created. Add your sections and questions.');
    }

    public function edit(int $id)
    {
        $survey = $this->surveyModel->getSurveyWithSections($id);
        if (! $survey) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $respondentCount = (new \App\Models\RespondentModel())
            ->where('survey_id', $id)
            ->where('submitted_at IS NOT NULL')
            ->countAllResults();

        return view('admin/surveys/edit', [
            'survey'        => $survey,
            'hasResponses'  => $respondentCount > 0,
            'respondentCount' => $respondentCount,
        ]);
    }

    public function update(int $id)
    {
        $rules = ['name' => 'required|max_length[255]'];
        if (! $this->validate($rules)) {
            return redirect()->back()->with('errors', $this->validator->getErrors())->withInput();
        }

        $data = [
            'name'        => $this->request->getPost('name'),
            'description' => $this->request->getPost('description'),
        ];
        $isPublic = $this->request->getPost('is_public');
        if ($isPublic !== null) {
            $data['is_public'] = $isPublic === '1' ? 1 : 0;
        }
        $isActive = $this->request->getPost('is_active');
        if ($isActive !== null) {
            $data['is_active'] = $isActive === '1' ? 1 : 0;
        }
        // Regenerate passkey if requested
        if ($this->request->getPost('regenerate_passkey')) {
            $data['passkey'] = $this->surveyModel->generatePasskey();
        }
        $this->surveyModel->update($id, $data);

        return redirect()->back()->with('success', 'Survey updated.');
    }

    public function delete(int $id)
    {
        $this->surveyModel->delete($id);
        return redirect()->to(base_url('admin/surveys'))->with('success', 'Survey deleted.');
    }

    public function shareLink(int $id)
    {
        $result = $this->publisherService->createShareLink($id);
        return $this->response->setJSON($result);
    }

    public function revokeLink(int $id)
    {
        $this->publisherService->revokeShareLink($id);
        return $this->response->setJSON(['success' => true]);
    }
}
