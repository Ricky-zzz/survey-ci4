<?php

namespace App\Models;

use CodeIgniter\Model;

class QuestionOptionModel extends Model
{
    protected $table         = 'question_options';
    protected $primaryKey    = 'id';
    protected $allowedFields = ['question_id', 'option_text', 'value', 'order_sequence'];
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = '';

    protected $validationRules = [
        'question_id' => 'required|integer',
        'option_text' => 'required|max_length[255]',
        'value'       => 'required|max_length[100]',
    ];

    public function getForQuestion(int $questionId): array
    {
        return $this->where('question_id', $questionId)
                    ->orderBy('order_sequence', 'ASC')
                    ->findAll();
    }

    public function replaceOptions(int $questionId, array $options): void
    {
        $this->where('question_id', $questionId)->delete();

        foreach ($options as $i => $opt) {
            $this->insert([
                'question_id'    => $questionId,
                'option_text'    => $opt['option_text'],
                'value'          => $opt['value'],
                'order_sequence' => $i,
            ]);
        }
    }
}
