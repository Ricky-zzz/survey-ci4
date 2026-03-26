<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class PoliticalSurveySeeder extends Seeder
{
    private const SURVEY_ID = 4;

    public function run()
    {
        // Realistic Philippine demographic data
        $firstNames = ['Maria', 'Juan', 'Peter', 'Angela', 'Miguel', 'Rosa', 'Carlo', 'Ana', 'Ricardo', 'Carmen', 'Jose', 'Corazon', 'Antonio', 'Beatrice', 'Manuel'];
        $lastNames = ['Santos', 'Cruz', 'Reyes', 'Dela Cruz', 'Garcia', 'Lopez', 'Rodriguez', 'Gonzales', 'Fernandez', 'Morales', 'Aquino', 'Marcos', 'Estrada', 'Arroyo', 'Duterte'];
        $addresses = ['Manila', 'Quezon City', 'Cebu', 'Davao', 'Makaati', 'Las Piñas', 'Makati', 'Caloocan', 'Pasay', 'Taguig', 'Balibago', 'Biñan', 'Cavite', 'Laguna', 'Bulacan'];
        
        // Answer distributions reflecting realistic Philippine sentiment
        $answers = [
            // Q22: Current Status - skewed toward employed/students
            22 => ['Employed / Working Professional', 'Employed / Working Professional', 'Employed / Working Professional', 'Student', 'Student', 'Working Student', 'Unemployed / Looking for work', 'Senior Citizen'],
            
            // Q23: Election Participation - most vote, some rarely
            23 => ['Always', 'Always', 'Always', 'Barely', 'Barely', 'Never'],
            
            // Q24: Reason for Voting - mixed motives
            24 => ['Civic duty', 'Desire for Change', 'Desire for Change', 'Strategic Voting', 'Family/Social Influence', 'Civic duty'],
            
            // Q25: Factor choosing candidate - track record and least-worst common
            25 => ['Track record', 'Track record', 'Least-worst', 'Popularity', 'Political Party', 'Track record'],
            
            // Q26: Voting decision influence - news and social media dominant
            26 => ['News Outlets', 'Social Media', 'Personal Networks', 'Debates', 'News Outlets', 'Social Media'],
            
            // Q27: Trust in government - PESSIMISTIC (80% low-no trust)
            27 => ['Low', 'Low', 'Low', 'No Trust at all', 'No Trust at all', 'Moderate', 'Low', 'Low'],
            
            // Q28: Economic satisfaction - VERY CRITICAL (70% dissatisfied)
            28 => ['Dissatisfied', 'Very Dissatisfied', 'Dissatisfied', 'Neither Satisfied nor Dissatisfied', 'Dissatisfied', 'Very Dissatisfied', 'Dissatisfied'],
            
            // Q29: Corruption is major issue - OVERWHELMING YES (85% yes)
            29 => ['yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'no'],
            
            // Q30: Corruption impact - SEVERE (75% say extreme/very much)
            30 => ['Extremely (It ruins almost everything)', 'Very much (It\'s a huge obstacle)', 'Extremely (It ruins almost everything)', 'Very much (It\'s a huge obstacle)', 'Moderately (It\'s there, but things still work)', 'Extremely (It ruins almost everything)'],
            
            // Q31: Optimism about future - DIVIDED but LEANING PESSIMISTIC
            31 => ['Somewhat Pessimistic (I am worried about where we are headed)', 'Very Pessimistic (I don\'t see things getting better anytime soon)', 'Somewhat Pessimistic (I am worried about where we are headed)', 'Very Pessimistic (I don\'t see things getting better anytime soon)', 'Somewhat Optimistic (I see some potential for improvement)'],
            
            // Q32: Oil response rating - CRITICAL (60% poor/very poor)
            32 => ['Poor (The response has been slow or ineffective)', 'Very Poor (No noticeable effort to help consumers)', 'Fair (Some effort, but not enough for the average citizen)', 'Poor (The response has been slow or ineffective)', 'Very Poor (No noticeable effort to help consumers)'],
            
            // Q33: Oil crisis priority - MIXED but SUBSIDIES POPULAR
            33 => ['Provide more fuel subsidies for public transport and farmers.', 'Promote long-term alternatives (e.g., electric vehicles, better mass transit).', 'Suspend fuel excise taxes to lower pump prices immediately.', 'Provide more fuel subsidies for public transport and farmers.'],
            
            // Q34: Accountability confidence - VERY LOW (80% not confident)
            34 => ['Not Confident (Investigation will likely lead to nothing)', 'Not Confident at all (It will be forgotten like previous scandals)', 'Not Confident (Investigation will likely lead to nothing)', 'Somewhat Confident (Some might face consequences)'],
            
            // Q35: Disaster preparedness satisfaction - CRITICAL (70% dissatisfied)
            35 => ['Dissatisfied', 'Very Dissatisfied', 'Dissatisfied', 'Neither Satisfied nor Dissatisfied', 'Very Dissatisfied', 'Dissatisfied'],
            
            // Q36: Tax transparency - SKEPTICAL (80% say not transparent)
            36 => ['Not transparent; many projects are questionable', 'Not transparent at all; it\'s a systematic scam', 'Somewhat transparent', 'Not transparent; many projects are questionable', 'Not transparent at all; it\'s a systematic scam'],
            
            // Q37: MOST urgent problem - CORRUPTION TOP, then WAGES
            37 => ['Investigating and punishing those involved in graft and corruption.', 'Investigating and punishing those involved in graft and corruption.', 'Increasing workers\' daily wages', 'Investigating and punishing those involved in graft and corruption.', 'Improving Access to Basic Services (better healthcare, water, and electricity for all)'],
        ];

        $respondents = [];
        $responses = [];
        
        // Generate 50 respondents
        for ($i = 1; $i <= 50; $i++) {
            $firstName = $firstNames[array_rand($firstNames)];
            $lastName = $lastNames[array_rand($lastNames)];
            $email = strtolower($firstName . '.' . $lastName . ($i % 10) . '@example.com');
            $address = $addresses[array_rand($addresses)];
            $age = rand(18, 75);

            $respondents[] = [
                'survey_id' => self::SURVEY_ID,
                'fullname' => $firstName . ' ' . $lastName,
                'email' => $email,
                'address' => $address,
                'age' => $age,
                'submitted_at' => date('Y-m-d H:i:s', strtotime('-' . rand(0, 30) . ' days')),
                'created_at' => date('Y-m-d H:i:s', strtotime('-' . rand(0, 30) . ' days')),
            ];
        }

        // Insert respondents
        $this->db->table('respondents')->insertBatch($respondents);

        // Get the inserted respondent IDs
        $insertedRespondents = $this->db->table('respondents')
            ->where('survey_id', self::SURVEY_ID)
            ->get()
            ->getResultArray();

        // Generate responses for each respondent
        foreach ($insertedRespondents as $respondent) {
            foreach ($answers as $questionId => $optionPool) {
                $responses[] = [
                    'respondent_id' => $respondent['id'],
                    'question_id' => $questionId,
                    'answer_value' => $optionPool[array_rand($optionPool)],
                    'created_at' => $respondent['created_at'],
                ];
            }
        }

        // Insert responses
        if (!empty($responses)) {
            // Insert in chunks to avoid memory issues
            $chunks = array_chunk($responses, 100);
            foreach ($chunks as $chunk) {
                $this->db->table('responses')->insertBatch($chunk);
            }
        }

        echo 'Successfully seeded 50 respondents with realistic political survey answers for the Philippines survey.';
    }
}
