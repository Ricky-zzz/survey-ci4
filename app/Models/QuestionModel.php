<?php

namespace App\Models;

use CodeIgniter\Model;

class QuestionModel extends Model
{
    protected $table         = 'questions';
    protected $primaryKey    = 'id';
    protected $allowedFields = [
        'section_id', 'question_text', 'type', 'required',
        'allow_multiple_files', 'matrix_group_id', 'order_sequence',
    ];
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules = [
        'section_id'    => 'required|integer',
        'question_text' => 'required',
        'type'          => 'required|in_list[text,yesno,scale,multiple_choice,file_upload]',
    ];

    public const TYPE_TEXT            = 'text';
    public const TYPE_YESNO           = 'yesno';
    public const TYPE_SCALE           = 'scale';
    public const TYPE_MULTIPLE_CHOICE = 'multiple_choice';
    public const TYPE_FILE_UPLOAD     = 'file_upload';

    public function getBySection(int $sectionId): array
    {
        return $this->where('section_id', $sectionId)
                    ->orderBy('order_sequence', 'ASC')
                    ->findAll();
    }

    public function getQuestionsWithOptions(int $sectionId): array
    {
        $questions    = $this->getBySection($sectionId);
        $optionModel  = new QuestionOptionModel();

        foreach ($questions as &$question) {
            if (!is_array($question)) {
                continue; // Skip invalid question records
            }
            $question['options'] = in_array($question['type'] ?? null, [self::TYPE_MULTIPLE_CHOICE, self::TYPE_SCALE])
                ? $optionModel->getForQuestion((int) $question['id'])
                : [];
        }

        return $questions;
    }

    public function getNextOrder(int $sectionId): int
    {
        $result = $this->selectMax('order_sequence')
                       ->where('section_id', $sectionId)
                       ->first();

        return ($result['order_sequence'] ?? -1) + 1;
    }

    public function getMatrixGroup(string $matrixGroupId): array
    {
        return $this->where('matrix_group_id', $matrixGroupId)
                    ->orderBy('order_sequence', 'ASC')
                    ->findAll();
    }
}
