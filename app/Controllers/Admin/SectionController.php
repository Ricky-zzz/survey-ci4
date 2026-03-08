<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\QuestionModel;
use App\Models\SectionModel;
use App\Models\SurveyModel;

class SectionController extends BaseController
{
    private SectionModel $sectionModel;
    private SurveyModel  $surveyModel;

    public function __construct()
    {
        $this->sectionModel = new SectionModel();
        $this->surveyModel  = new SurveyModel();
    }

    public function show(int $surveyId, int $sectionId)
    {
        $survey  = $this->surveyModel->find($surveyId);
        $section = $this->sectionModel->find($sectionId);

        if (! $survey || ! $section || (int) $section['survey_id'] !== $surveyId) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $questionModel        = new QuestionModel();
        $section['questions'] = $questionModel->getQuestionsWithOptions($sectionId);

        // Check if survey has responses
        $respondentCount = (new \App\Models\RespondentModel())
            ->where('survey_id', $surveyId)
            ->where('submitted_at IS NOT NULL')
            ->countAllResults();

        return view('admin/sections/show', [
            'survey'        => $survey,
            'section'       => $section,
            'hasResponses'  => $respondentCount > 0,
            'respondentCount' => $respondentCount,
        ]);
    }

    public function store(int $surveyId)
    {
        $rules = ['title' => 'required|max_length[255]'];
        if (! $this->validate($rules)) {
            return redirect()->back()->with('errors', $this->validator->getErrors())->withInput();
        }

        $sectionId = (int) $this->sectionModel->insert([
            'survey_id'          => $surveyId,
            'title'              => $this->request->getPost('title'),
            'description'        => $this->request->getPost('description') ?? '',
            'is_respondent_info' => 0,
            'order_sequence'     => $this->sectionModel->getNextOrder($surveyId),
        ]);

        return redirect()->to(base_url('admin/surveys/' . $surveyId . '/sections/' . $sectionId))
                         ->with('success', 'Section created. Add questions below.');
    }

    public function update(int $surveyId, int $sectionId)
    {
        $rules = ['title' => 'required|max_length[255]'];
        if (! $this->validate($rules)) {
            return redirect()->back()->with('errors', $this->validator->getErrors())->withInput();
        }

        $this->sectionModel->update($sectionId, [
            'title'       => $this->request->getPost('title'),
            'description' => $this->request->getPost('description') ?? '',
        ]);

        return redirect()->to(base_url('admin/surveys/' . $surveyId . '/sections/' . $sectionId))
                         ->with('success', 'Section updated.');
    }

    public function delete(int $surveyId, int $sectionId)
    {
        $section = $this->sectionModel->find($sectionId);
        if ($section && $section['is_respondent_info']) {
            return redirect()->back()->with('error', 'Cannot delete the default respondent info section.');
        }

        $this->sectionModel->delete($sectionId);
        return redirect()->to(base_url('admin/surveys/' . $surveyId . '/edit'))
                         ->with('success', 'Section deleted.');
    }

    public function reorder(int $surveyId)
    {
        $order = $this->request->getJSON(true)['order'] ?? [];
        foreach ($order as $seq => $sectionId) {
            $this->sectionModel->update((int) $sectionId, ['order_sequence' => (int) $seq]);
        }
        return $this->response->setJSON(['success' => true]);
    }
}
