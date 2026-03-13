<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddDemographicsToRespondents extends Migration
{
    public function up()
    {
        $this->forge->addColumn('respondents', [
            'fullname' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null'       => true,
                'after'      => 'survey_id',
            ],
            'email' => [
                'type'       => 'VARCHAR',
                'constraint' => '150',
                'null'       => false,
                'after'      => 'fullname',
            ],
            'address' => [
                'type' => 'TEXT',
                'null' => true,
                'after' => 'email',
            ],
            'age' => [
                'type' => 'INT',
                'null' => true,
                'after' => 'address',
            ],
        ]);

        // Add indexes
        $this->forge->addKey('email', false, false);
        $this->forge->addKey('age', false, false);
    }

    public function down()
    {
        $this->forge->dropColumn('respondents', ['fullname', 'email', 'address', 'age']);
    }
}
