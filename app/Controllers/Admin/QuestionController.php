<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\QuestionModel;
use App\Models\QuestionOptionModel;
use App\Models\SectionModel;

class QuestionController extends BaseController
{
    private QuestionModel       $questionModel;
    private QuestionOptionModel $optionModel;
    private SectionModel        $sectionModel;

    public function __construct()
    {
        $this->questionModel = new QuestionModel();
        $this->optionModel   = new QuestionOptionModel();
        $this->sectionModel  = new SectionModel();
    }

    public function edit(int $surveyId, int $sectionId, int $questionId)
    {
        $question = $this->questionModel->find($questionId);
        if (! $question || (int) $question['section_id'] !== $sectionId) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $section = $this->sectionModel->find($sectionId);
        $survey  = (new \App\Models\SurveyModel())->find($surveyId);

        $question['options'] = in_array($question['type'], [
            QuestionModel::TYPE_MULTIPLE_CHOICE,
            QuestionModel::TYPE_SCALE,
        ]) ? $this->optionModel->getForQuestion($questionId) : [];

        return view('admin/questions/edit', [
            'survey'   => $survey,
            'section'  => $section,
            'question' => $question,
        ]);
    }

    public function store(int $surveyId, int $sectionId)
    {
        $rules = [
            'question_text' => 'required',
            'type'          => 'required|in_list[text,yesno,scale,multiple_choice,file_upload]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->with('errors', $this->validator->getErrors())->withInput();
        }

        $questionId = (int) $this->questionModel->insert([
            'section_id'           => $sectionId,
            'question_text'        => $this->request->getPost('question_text'),
            'type'                 => $this->request->getPost('type'),
            'required'             => (int) (bool) $this->request->getPost('required'),
            'allow_multiple_files' => (int) (bool) $this->request->getPost('allow_multiple_files'),
            'matrix_group_id'      => $this->request->getPost('matrix_group_id') ?: null,
            'order_sequence'       => $this->questionModel->getNextOrder($sectionId),
        ]);

        // Save options for multiple_choice and scale types
        $this->saveOptions($questionId);

        return redirect()->to(base_url('admin/surveys/' . $surveyId . '/sections/' . $sectionId))
                         ->with('success', 'Question added.');
    }

    public function update(int $surveyId, int $sectionId, int $questionId)
    {
        $rules = ['question_text' => 'required'];

        if (! $this->validate($rules)) {
            return redirect()->back()->with('errors', $this->validator->getErrors())->withInput();
        }

        $this->questionModel->update($questionId, [
            'question_text'        => $this->request->getPost('question_text'),
            'required'             => (int) (bool) $this->request->getPost('required'),
            'allow_multiple_files' => (int) (bool) $this->request->getPost('allow_multiple_files'),
        ]);

        // Re-save options if this is a scale/multiple_choice question
        $question = $this->questionModel->find($questionId);
        if (in_array($question['type'], [QuestionModel::TYPE_MULTIPLE_CHOICE, QuestionModel::TYPE_SCALE], true)) {
            $this->saveOptions($questionId, $question['type']);
        }

        return redirect()->to(base_url('admin/surveys/' . $surveyId . '/sections/' . $sectionId))
                         ->with('success', 'Question updated.');
    }

    public function delete(int $surveyId, int $sectionId, int $questionId)
    {
        $this->questionModel->delete($questionId);
        return redirect()->to(base_url('admin/surveys/' . $surveyId . '/sections/' . $sectionId))
                         ->with('success', 'Question deleted.');
    }

    private function saveOptions(int $questionId, ?string $typeOverride = null): void
    {
        $type = $typeOverride ?? $this->request->getPost('type');
        if (! in_array($type, [QuestionModel::TYPE_MULTIPLE_CHOICE, QuestionModel::TYPE_SCALE], true)) {
            return;
        }

        $texts  = $this->request->getPost('option_text') ?? [];
        $values = $this->request->getPost('option_value') ?? [];

        $options = [];
        foreach ($texts as $i => $text) {
            if (trim($text) !== '') {
                $options[] = [
                    'option_text' => trim($text),
                    'value'       => trim($values[$i] ?? $text),
                ];
            }
        }

        if (! empty($options)) {
            $this->optionModel->replaceOptions($questionId, $options);
        }
    }
}
