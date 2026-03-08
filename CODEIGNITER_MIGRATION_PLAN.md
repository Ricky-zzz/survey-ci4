# CodeIgniter 4 Survey Application - Complete Implementation Plan

**Project Scope:** Migrate monolithic PHP survey app to scalable CodeIgniter 4 architecture  
**Timeline:** 5-7 days (estimated)  
**Tech Stack:** CodeIgniter 4 + Blade + Alpine.js + Tailwind CSS (CDN)  
**Database:** Reuse existing schema with minor adjustments  
**Frontend:** Alpine + Blade (no build step needed)

---

## Table of Contents
1. [Architecture Overview](#architecture-overview)
2. [Phase 1: Project Setup](#phase-1-project-setup)
3. [Phase 2: Core Models](#phase-2-core-models)
4. [Phase 3: Services Layer](#phase-3-services-layer)
5. [Phase 4: Middleware](#phase-4-middleware)
6. [Phase 5: Controllers & Routes](#phase-5-controllers--routes)
7. [Phase 6: Views & Frontend](#phase-6-views--frontend)
8. [Phase 7: Testing & Deployment](#phase-7-testing--deployment)
9. [Database Schema Adjustments](#database-schema-adjustments)
10. [Implementation Checklist](#implementation-checklist)

---

## Architecture Overview

### Technology Decisions

**Frontend: Alpine + Blade (NOT Vue)**
- ✅ **Lightweight**: 15KB vs 40KB+ Vue
- ✅ **No Build Step**: Instant development
- ✅ **Blade Integration**: Templates rendered server-side
- ✅ **Progressive Enhancement**: Works without JavaScript
- ✅ **Perfect for Forms**: File previews, dynamic sections, validation
- ✅ **Easier Deployment**: No npm build required on server

**Use Cases for Alpine in Survey App:**
- Show/hide conditional questions
- Real-time form validation feedback
- File upload preview
- Multi-step form progress
- Matrix grid interactions
- Dynamic required field validation

### Project Structure
```
survey/
├── app/
│   ├── Controllers/
│   │   ├── Admin/
│   │   │   ├── DashboardController.php
│   │   │   ├── SurveyController.php
│   │   │   ├── SectionController.php
│   │   │   ├── QuestionController.php
│   │   │   ├── ResultsController.php
│   │   │   └── FileController.php
│   │   └── Public/
│   │       ├── SurveyController.php
│   │       └── RespondentController.php
│   ├── Models/
│   │   ├── AdminModel.php
│   │   ├── SurveyModel.php
│   │   ├── SectionModel.php
│   │   ├── QuestionModel.php
│   │   ├── QuestionOptionModel.php
│   │   ├── RespondentModel.php
│   │   ├── ResponseModel.php
│   │   └── FileModel.php
│   ├── Views/
│   │   ├── layout.php
│   │   ├── admin/
│   │   │   ├── dashboard.php
│   │   │   ├── login.php
│   │   │   ├── surveys/
│   │   │   ├── sections/
│   │   │   ├── questions/
│   │   │   └── results/
│   │   ├── public/
│   │   │   ├── survey.php
│   │   │   └── thank-you.php
│   │   └── components/
│   │       ├── form-input.php
│   │       ├── form-textarea.php
│   │       ├── form-select.php
│   │       └── form-fileupload.php
│   ├── Services/
│   │   ├── FileUploaderService.php
│   │   ├── SurveyPublisherService.php
│   │   ├── ResponseService.php
│   │   └── AnalyticsService.php
│   ├── Filters/
│   │   ├── AuthFilter.php
│   │   └── NoCacheFilter.php
│   └── Entities/
│       └── [Optional entity classes for type safety]
├── public/
│   ├── css/
│   ├── js/
│   ├── uploads/
│   │   ├── survey-responses/
│   │   └── .htaccess
│   └── index.php
├── config/
│   ├── Routes.php
│   ├── Database.php
│   ├── Filters.php
│   ├── Constants.php
│   └── App.php
├── CODEIGNITER_MIGRATION_PLAN.md (this file)
└── composer.json
```

---

## Phase 1: Project Setup

### Step 1.1: Install CodeIgniter 4
```bash
# Navigate to parent directory
cd c:\laragon\www

# Create new CodeIgniter project
composer create-project codeigniter4/appstarter survey-ci4

# Copy database schema
# (We'll use existing schema, no schema changes needed)
```

### Step 1.2: Configure Environment
**File:** `env`
```
CI_ENVIRONMENT = development
database.default.hostname = localhost
database.default.database = survey
database.default.username = root
database.default.password = 
database.default.DBDriver = MySQLi
database.default.DBPrefix =
database.default.port = 3306

app.baseURL = 'http://localhost/survey-ci4/'
app.CSRFProtection = true
app.CSRFTokenName = 'csrf_token'
app.CSRFHeaderName = 'X-CSRF-TOKEN'
app.CSRFCookieName = 'XSRF-TOKEN'
```

### Step 1.3: Create Database
```bash
# Use existing survey.sql
# Import via phpMyAdmin or command line
```

### Step 1.4: Configure File Uploads
**Create directories:**
```bash
mkdir -p public/uploads/survey-responses
```

**.htaccess for uploads:**
```
<FilesMatch "\.(php|phtml|phtml|shtml)$">
    Deny from all
</FilesMatch>
```

---

## Phase 2: Core Models

### Model Responsibilities

**BaseModel Features to Use:**
- Auto timestamps (`created_at`, `updated_at`)
- Validation rules
- Relationships
- Query builder

### Step 2.1: AdminModel
**File:** `app/Models/AdminModel.php`

```php
<?php
namespace App\Models;

use CodeIgniter\Model;

class AdminModel extends Model
{
    protected $table = 'admins';
    protected $primaryKey = 'id';
    protected $allowedFields = ['username', 'password_hash', 'email'];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $validationRules = [
        'username' => 'required|is_unique[admins.username]|min_length[3]|max_length[100]',
        'password_hash' => 'required|min_length[6]',
        'email' => 'required|valid_email|is_unique[admins.email]',
    ];

    // Custom methods
    public function findByUsername($username)
    {
        return $this->where('username', $username)->first();
    }

    public function validatePassword($inputPassword, $hash)
    {
        return password_verify($inputPassword, $hash);
    }

    public function hashPassword($password)
    {
        return password_hash($password, PASSWORD_BCRYPT);
    }
}
```

### Step 2.2: SurveyModel
**File:** `app/Models/SurveyModel.php`

```php
<?php
namespace App\Models;

use CodeIgniter\Model;

class SurveyModel extends Model
{
    protected $table = 'surveys';
    protected $primaryKey = 'id';
    protected $allowedFields = ['name', 'description', 'is_public', 'passkey', 'created_by'];
    protected $useTimestamps = true;
    protected $validationRules = [
        'name' => 'required|max_length[255]',
        'description' => 'permit_empty|max_length[1000]',
        'is_public' => 'permit_empty|in_list[0,1]',
        'created_by' => 'required|integer',
    ];

    public function getSurveyWithSections($surveyId)
    {
        return $this->select('surveys.*')
            ->with('sections')
            ->find($surveyId);
    }

    public function generatePasskey()
    {
        return bin2hex(random_bytes(10));
    }
}
```

### Step 2.3: SectionModel
**File:** `app/Models/SectionModel.php`

```php
<?php
namespace App\Models;

use CodeIgniter\Model;

class SectionModel extends Model
{
    protected $table = 'sections';
    protected $primaryKey = 'id';
    protected $allowedFields = ['survey_id', 'title', 'description', 'is_respondent_info', 'order_sequence'];
    protected $useTimestamps = true;
    protected $validationRules = [
        'survey_id' => 'required|integer',
        'title' => 'required|max_length[255]',
        'description' => 'permit_empty|max_length[1000]',
        'order_sequence' => 'required|integer',
    ];

    public function getSectionWithQuestions($sectionId)
    {
        $questionModel = new QuestionModel();
        return $this->find($sectionId);
        // Will load questions through question relationship
    }
}
```

### Step 2.4: QuestionModel
**File:** `app/Models/QuestionModel.php`

```php
<?php
namespace App\Models;

use CodeIgniter\Model;

class QuestionModel extends Model
{
    protected $table = 'questions';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'section_id', 'question_text', 'type', 'required', 
        'allow_multiple_files', 'matrix_group_id', 'order_sequence'
    ];
    protected $useTimestamps = true;
    protected $validationRules = [
        'section_id' => 'required|integer',
        'question_text' => 'required',
        'type' => 'required|in_list[text,yesno,scale,multiple_choice,file_upload]',
    ];

    const TYPE_TEXT = 'text';
    const TYPE_YESNO = 'yesno';
    const TYPE_SCALE = 'scale';
    const TYPE_MULTIPLE_CHOICE = 'multiple_choice';
    const TYPE_FILE_UPLOAD = 'file_upload';

    public function getQuestionWithOptions($questionId)
    {
        $optionModel = new QuestionOptionModel();
        $question = $this->find($questionId);
        $question['options'] = $optionModel->where('question_id', $questionId)
            ->orderBy('order_sequence', 'ASC')
            ->findAll();
        return $question;
    }

    public function getMatrixGroup($matrixGroupId)
    {
        return $this->where('matrix_group_id', $matrixGroupId)
            ->orderBy('order_sequence', 'ASC')
            ->findAll();
    }
}
```

### Step 2.5: QuestionOptionModel
**File:** `app/Models/QuestionOptionModel.php`

```php
<?php
namespace App\Models;

use CodeIgniter\Model;

class QuestionOptionModel extends Model
{
    protected $table = 'question_options';
    protected $primaryKey = 'id';
    protected $allowedFields = ['question_id', 'option_text', 'value', 'order_sequence'];
    protected $useTimestamps = true;
    protected $validationRules = [
        'question_id' => 'required|integer',
        'option_text' => 'required|max_length[255]',
        'value' => 'required|max_length[100]',
    ];
}
```

### Step 2.6: RespondentModel
**File:** `app/Models/RespondentModel.php`

```php
<?php
namespace App\Models;

use CodeIgniter\Model;

class RespondentModel extends Model
{
    protected $table = 'respondents';
    protected $primaryKey = 'id';
    protected $allowedFields = ['survey_id', 'submitted_at'];
    protected $useTimestamps = true;
    protected $useSoftDeletes = false;

    public function getRespondentsForSurvey($surveyId)
    {
        return $this->where('survey_id', $surveyId)
            ->orderBy('submitted_at', 'DESC')
            ->findAll();
    }

    public function getCompletionStats($surveyId)
    {
        return [
            'total' => $this->where('survey_id', $surveyId)->countAllResults(),
            'completed' => $this->where('survey_id', $surveyId)
                ->where('submitted_at !=', null)
                ->countAllResults(),
            'in_progress' => $this->where('survey_id', $surveyId)
                ->where('submitted_at', null)
                ->countAllResults(),
        ];
    }
}
```

### Step 2.7: ResponseModel
**File:** `app/Models/ResponseModel.php`

```php
<?php
namespace App\Models;

use CodeIgniter\Model;

class ResponseModel extends Model
{
    protected $table = 'responses';
    protected $primaryKey = 'id';
    protected $allowedFields = ['respondent_id', 'question_id', 'answer_value'];
    protected $useTimestamps = true;
    protected $validationRules = [
        'respondent_id' => 'required|integer',
        'question_id' => 'required|integer',
        'answer_value' => 'permit_empty',
    ];

    public function getRespondentResponses($respondentId)
    {
        return $this->where('respondent_id', $respondentId)
            ->findAll();
    }

    public function getQuestionResponses($questionId)
    {
        return $this->where('question_id', $questionId)
            ->findAll();
    }

    public function saveResponse($respondentId, $questionId, $answer)
    {
        return $this->updateOrupdateInsert(
            ['respondent_id' => $respondentId, 'question_id' => $questionId],
            ['answer_value' => $answer]
        );
    }
}
```

### Step 2.8: FileModel
**File:** `app/Models/FileModel.php`

```php
<?php
namespace App\Models;

use CodeIgniter\Model;

class FileModel extends Model
{
    protected $table = 'files';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'respondent_id', 'question_id', 'file_path', 
        'original_filename', 'file_size', 'file_type'
    ];
    protected $useTimestamps = true;
    protected $createdField = 'uploaded_at';

    public function getFilesByRespondent($respondentId)
    {
        return $this->where('respondent_id', $respondentId)->findAll();
    }

    public function getFilesByQuestion($questionId)
    {
        return $this->where('question_id', $questionId)
            ->orderBy('uploaded_at', 'DESC')
            ->findAll();
    }
}
```

---

## Phase 3: Services Layer

### Why Services?
- Encapsulate business logic
- Reusable across controllers
- Easy to test
- Separation of concerns

### Step 3.1: FileUploaderService
**File:** `app/Services/FileUploaderService.php`

```php
<?php
namespace App\Services;

use Config\Services;

class FileUploaderService
{
    private $allowedExtensions = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'jpg', 'jpeg', 'png'];
    private $maxFileSize = 5242880; // 5MB
    private $uploadPath = 'uploads/survey-responses/';

    public function validateFile($file)
    {
        $errors = [];

        if (!$file->isValid()) {
            $errors[] = $file->getErrorString();
            return ['valid' => false, 'errors' => $errors];
        }

        if ($file->getSize() > $this->maxFileSize) {
            $errors[] = 'File exceeds maximum size of 5MB';
        }

        $ext = strtolower($file->getClientExtension());
        if (!in_array($ext, $this->allowedExtensions)) {
            $errors[] = 'File type not allowed. Allowed: ' . implode(', ', $this->allowedExtensions);
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors
        ];
    }

    public function uploadFile($file, $respondentId, $questionId)
    {
        $validation = $this->validateFile($file);
        if (!$validation['valid']) {
            return ['success' => false, 'errors' => $validation['errors']];
        }

        $newName = $file->getRandomName();
        $file->move(FCPATH . $this->uploadPath, $newName);

        $fileModel = new \App\Models\FileModel();
        $fileModel->insert([
            'respondent_id' => $respondentId,
            'question_id' => $questionId,
            'file_path' => $this->uploadPath . $newName,
            'original_filename' => $file->getClientName(),
            'file_size' => $file->getSize(),
            'file_type' => $file->getClientExtension(),
        ]);

        return [
            'success' => true,
            'file_id' => $fileModel->insertID,
            'file_path' => $this->uploadPath . $newName
        ];
    }

    public function deleteFile($fileId)
    {
        $fileModel = new \App\Models\FileModel();
        $file = $fileModel->find($fileId);

        if (!$file) {
            return ['success' => false, 'message' => 'File not found'];
        }

        if (file_exists(FCPATH . $file['file_path'])) {
            unlink(FCPATH . $file['file_path']);
        }

        $fileModel->delete($fileId);
        return ['success' => true];
    }
}
```

### Step 3.2: SurveyPublisherService
**File:** `app/Services/SurveyPublisherService.php`

```php
<?php
namespace App\Services;

use App\Models\SurveyModel;

class SurveyPublisherService
{
    private $surveyModel;

    public function __construct()
    {
        $this->surveyModel = new SurveyModel();
    }

    public function createShareLink($surveyId)
    {
        $survey = $this->surveyModel->find($surveyId);
        
        if (!$survey) {
            return ['success' => false, 'message' => 'Survey not found'];
        }

        // Generate unique passkey if doesn't exist
        if (empty($survey['passkey'])) {
            $passkey = $this->surveyModel->generatePasskey();
            $this->surveyModel->update($surveyId, ['passkey' => $passkey]);
        } else {
            $passkey = $survey['passkey'];
        }

        $shareUrl = base_url('surveys/' . $passkey);
        return ['success' => true, 'url' => $shareUrl, 'passkey' => $passkey];
    }

    public function getSurveyByPasskey($passkey)
    {
        return $this->surveyModel->where('passkey', $passkey)->first();
    }

    public function getEmbedCode($passkey)
    {
        $url = base_url('surveys/' . $passkey);
        $code = <<<HTML
        <iframe src="{$url}" width="100%" height="800" frameborder="0" allow="fullscreen"></iframe>
        HTML;
        return $code;
    }
}
```

### Step 3.3: ResponseService
**File:** `app/Services/ResponseService.php`

```php
<?php
namespace App\Services;

use App\Models\ResponseModel;
use App\Models\QuestionModel;
use App\Models\RespondentModel;

class ResponseService
{
    private $responseModel;
    private $questionModel;
    private $respondentModel;

    public function __construct()
    {
        $this->responseModel = new ResponseModel();
        $this->questionModel = new QuestionModel();
        $this->respondentModel = new RespondentModel();
    }

    public function validateResponses($surveyId, $responses)
    {
        $sectionModel = new \App\Models\SectionModel();
        $sections = $sectionModel->where('survey_id', $surveyId)->findAll();
        $errors = [];

        foreach ($sections as $section) {
            $questions = $this->questionModel->where('section_id', $section['id'])
                ->where('required', 1)
                ->findAll();

            foreach ($questions as $question) {
                if (empty($responses[$question['id']])) {
                    $errors[$question['id']] = 'This field is required';
                }
            }
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors
        ];
    }

    public function saveResponses($respondentId, $responses)
    {
        foreach ($responses as $questionId => $answer) {
            $this->responseModel->saveResponse($respondentId, $questionId, $answer);
        }

        return ['success' => true];
    }

    public function completeRespondent($respondentId)
    {
        $this->respondentModel->update($respondentId, ['submitted_at' => date('Y-m-d H:i:s')]);
    }
}
```

### Step 3.4: AnalyticsService
**File:** `app/Services/AnalyticsService.php`

```php
<?php
namespace App\Services;

use App\Models\ResponseModel;
use App\Models\RespondentModel;
use App\Models\QuestionModel;

class AnalyticsService
{
    private $responseModel;
    private $respondentModel;
    private $questionModel;

    public function __construct()
    {
        $this->responseModel = new ResponseModel();
        $this->respondentModel = new RespondentModel();
        $this->questionModel = new QuestionModel();
    }

    public function getSurveyStats($surveyId)
    {
        $stats = $this->respondentModel->getCompletionStats($surveyId);
        
        $completionRate = 0;
        if ($stats['total'] > 0) {
            $completionRate = round(($stats['completed'] / $stats['total']) * 100, 2);
        }

        return [
            'total_respondents' => $stats['total'],
            'completed' => $stats['completed'],
            'in_progress' => $stats['in_progress'],
            'completion_rate' => $completionRate . '%',
        ];
    }

    public function getQuestionStats($questionId)
    {
        $question = $this->questionModel->find($questionId);
        $responses = $this->responseModel->getQuestionResponses($questionId);

        switch ($question['type']) {
            case 'scale':
                return $this->getScaleStats($responses);
            case 'multiple_choice':
                return $this->getChoiceStats($responses);
            case 'yesno':
                return $this->getYesNoStats($responses);
            default:
                return ['total_responses' => count($responses)];
        }
    }

    private function getScaleStats($responses)
    {
        $values = array_column($responses, 'answer_value');
        $values = array_filter($values);
        
        if (empty($values)) {
            return ['average' => 0, 'total_responses' => 0];
        }

        return [
            'average' => round(array_sum($values) / count($values), 2),
            'total_responses' => count($values),
            'distribution' => array_count_values($values),
        ];
    }

    private function getChoiceStats($responses)
    {
        $values = array_column($responses, 'answer_value');
        $values = array_filter($values);

        return [
            'total_responses' => count($values),
            'distribution' => array_count_values($values),
        ];
    }

    private function getYesNoStats($responses)
    {
        $values = array_column($responses, 'answer_value');
        $values = array_filter($values);
        $distribution = array_count_values($values);

        return [
            'yes' => $distribution['yes'] ?? 0,
            'no' => $distribution['no'] ?? 0,
            'total' => count($values),
        ];
    }
}
```

---

## Phase 4: Middleware

### Step 4.1: AuthFilter (Middleware)
**File:** `app/Filters/AuthFilter.php`

```php
<?php
namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class AuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        if (!session('admin_id')) {
            return redirect()->to('admin/login')->with('error', 'Please login first');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
    }
}
```

### Step 4.2: NoCacheFilter
**File:** `app/Filters/NoCacheFilter.php`

```php
<?php
namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class NoCacheFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        $response->setHeader('Cache-Control', 'no-store, no-cache, max-age=0, must-revalidate');
        $response->setHeader('Pragma', 'no-cache');
        $response->setHeader('Expires', 'Thu, 01 Jan 1970 00:00:01 GMT');
    }
}
```

### Step 4.3: Register Filters
**File:** `app/Config/Filters.php`

```php
public $filters = [
    'auth' => ['before' => ['admin/*']],
    'nocache' => ['before' => ['surveys/*']],
];

public $filterList = [
    'auth' => \App\Filters\AuthFilter::class,
    'nocache' => \App\Filters\NoCacheFilter::class,
];
```

---

## Phase 5: Controllers & Routes

### Step 5.1: Public Survey Controller
**File:** `app/Controllers/Public/SurveyController.php`

```php
<?php
namespace App\Controllers\Public;

use CodeIgniter\Controller;
use App\Models\SurveyModel;
use App\Models\SectionModel;
use App\Models\QuestionModel;
use App\Models\RespondentModel;
use App\Services\SurveyPublisherService;

class SurveyController extends Controller
{
    private $surveyModel;
    private $sectionModel;
    private $questionModel;
    private $respondentModel;
    private $publisherService;

    public function __construct()
    {
        $this->surveyModel = new SurveyModel();
        $this->sectionModel = new SectionModel();
        $this->questionModel = new QuestionModel();
        $this->respondentModel = new RespondentModel();
        $this->publisherService = new SurveyPublisherService();
    }

    public function show($passkey)
    {
        $survey = $this->publisherService->getSurveyByPasskey($passkey);
        
        if (!$survey || !$survey['is_public']) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        // Get or create respondent session
        if (!session('respondent_id')) {
            $respondent = $this->respondentModel->insert(['survey_id' => $survey['id']]);
            session()->set('respondent_id', $respondent);
        }

        $sections = $this->sectionModel->where('survey_id', $survey['id'])
            ->orderBy('order_sequence', 'ASC')
            ->findAll();

        // Load questions for each section
        foreach ($sections as &$section) {
            $section['questions'] = $this->questionModel
                ->where('section_id', $section['id'])
                ->orderBy('order_sequence', 'ASC')
                ->findAll();

            // Load options for multiple choice questions
            foreach ($section['questions'] as &$question) {
                if (in_array($question['type'], ['multiple_choice', 'scale', 'yesno'])) {
                    $optionModel = new \App\Models\QuestionOptionModel();
                    $question['options'] = $optionModel
                        ->where('question_id', $question['id'])
                        ->orderBy('order_sequence', 'ASC')
                        ->findAll();
                }
            }
        }

        return view('public/survey', [
            'survey' => $survey,
            'sections' => $sections,
            'passkey' => $passkey,
        ]);
    }

    public function submit($passkey)
    {
        $survey = $this->publisherService->getSurveyByPasskey($passkey);
        
        if (!$survey || !$survey['is_public']) {
            return $this->response->setStatusCode(404);
        }

        $respondentId = session('respondent_id');
        if (!$respondentId) {
            return redirect()->back()->with('error', 'Invalid session');
        }

        $responses = $this->request->getPost();
        unset($responses['csrf_token']);

        // Validate responses
        $responseService = new \App\Services\ResponseService();
        $validation = $responseService->validateResponses($survey['id'], $responses);

        if (!$validation['valid']) {
            return redirect()->back()->withInput()->with('errors', $validation['errors']);
        }

        // Save responses
        $responseService->saveResponses($respondentId, $responses);
        $responseService->completeRespondent($respondentId);

        session()->destroy();

        return redirect()->to('surveys/' . $passkey . '/thank-you');
    }

    public function thankYou($passkey)
    {
        $survey = $this->publisherService->getSurveyByPasskey($passkey);
        
        if (!$survey) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        return view('public/thank-you', ['survey' => $survey]);
    }
}
```

### Step 5.2: Admin Dashboard Controller
**File:** `app/Controllers/Admin/DashboardController.php`

```php
<?php
namespace App\Controllers\Admin;

use CodeIgniter\Controller;
use App\Models\SurveyModel;
use App\Services\AnalyticsService;

class DashboardController extends Controller
{
    private $surveyModel;
    private $analyticsService;

    public function __construct()
    {
        $this->surveyModel = new SurveyModel();
        $this->analyticsService = new AnalyticsService();
    }

    public function index()
    {
        $adminId = session('admin_id');
        $surveys = $this->surveyModel
            ->where('created_by', $adminId)
            ->orderBy('created_at', 'DESC')
            ->findAll();

        $surveyStats = [];
        foreach ($surveys as $survey) {
            $surveyStats[$survey['id']] = $this->analyticsService->getSurveyStats($survey['id']);
        }

        return view('admin/dashboard', [
            'surveys' => $surveys,
            'stats' => $surveyStats,
        ]);
    }
}
```

### Step 5.3: Admin Survey Controller
**File:** `app/Controllers/Admin/SurveyController.php`

```php
<?php
namespace App\Controllers\Admin;

use CodeIgniter\Controller;
use App\Models\SurveyModel;
use App\Models\SectionModel;
use App\Services\SurveyPublisherService;

class SurveyController extends Controller
{
    private $surveyModel;
    private $sectionModel;
    private $publisherService;

    public function __construct()
    {
        $this->surveyModel = new SurveyModel();
        $this->sectionModel = new SectionModel();
        $this->publisherService = new SurveyPublisherService();
    }

    public function create()
    {
        return view('admin/surveys/create');
    }

    public function store()
    {
        $data = [
            'name' => $this->request->getPost('name'),
            'description' => $this->request->getPost('description'),
            'is_public' => $this->request->getPost('is_public') ? 1 : 0,
            'created_by' => session('admin_id'),
        ];

        if (!$this->surveyModel->save($data)) {
            return redirect()->back()->withInput()->with('errors', $this->surveyModel->errors());
        }

        $surveyId = $this->surveyModel->insertID;
        
        return redirect()->to('/admin/surveys/' . $surveyId . '/edit')
            ->with('success', 'Survey created successfully');
    }

    public function edit($id)
    {
        $survey = $this->surveyModel->find($id);
        $sections = $this->sectionModel->where('survey_id', $id)
            ->orderBy('order_sequence', 'ASC')
            ->findAll();

        return view('admin/surveys/edit', [
            'survey' => $survey,
            'sections' => $sections,
        ]);
    }

    public function update($id)
    {
        $data = [
            'name' => $this->request->getPost('name'),
            'description' => $this->request->getPost('description'),
            'is_public' => $this->request->getPost('is_public') ? 1 : 0,
        ];

        if (!$this->surveyModel->update($id, $data)) {
            return redirect()->back()->withInput()->with('errors', $this->surveyModel->errors());
        }

        return redirect()->back()->with('success', 'Survey updated successfully');
    }

    public function delete($id)
    {
        $this->surveyModel->delete($id);
        return redirect()->to('/admin/surveys')->with('success', 'Survey deleted');
    }

    public function shareLink($id)
    {
        $result = $this->publisherService->createShareLink($id);
        
        if (!$result['success']) {
            return $this->response->setJSON($result)->setStatusCode(404);
        }

        return $this->response->setJSON($result);
    }
}
```

### Step 5.4: Admin Respondents Controller
**File:** `app/Controllers/Admin/RespondentsController.php`

```php
<?php
namespace App\Controllers\Admin;

use CodeIgniter\Controller;
use App\Models\RespondentModel;
use App\Models\ResponseModel;

class RespondentsController extends Controller
{
    private $respondentModel;
    private $responseModel;

    public function __construct()
    {
        $this->respondentModel = new RespondentModel();
        $this->responseModel = new ResponseModel();
    }

    public function index($surveyId)
    {
        $respondents = $this->respondentModel
            ->where('survey_id', $surveyId)
            ->orderBy('submitted_at', 'DESC')
            ->paginate(20);

        return view('admin/respondents', [
            'respondents' => $respondents,
            'survey_id' => $surveyId,
            'pager' => $this->respondentModel->pager,
        ]);
    }

    public function detail($id)
    {
        $respondent = $this->respondentModel->find($id);
        $responses = $this->responseModel->getRespondentResponses($id);

        // Load question text for each response
        $questionModel = new \App\Models\QuestionModel();
        foreach ($responses as &$response) {
            $question = $questionModel->find($response['question_id']);
            $response['question_text'] = $question['question_text'];
            $response['type'] = $question['type'];
        }

        return view('admin/respondent-detail', [
            'respondent' => $respondent,
            'responses' => $responses,
        ]);
    }
}
```

### Step 5.5: Routes Configuration
**File:** `app/Config/Routes.php`

```php
<?php

namespace Config;

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// Admin Routes
$routes->group('admin', static function($routes) {
    // Auth routes (no middleware)
    $routes->get('login', 'Admin\AuthController::login');
    $routes->post('login', 'Admin\AuthController::handleLogin');
    $routes->get('logout', 'Admin\AuthController::logout');

    // Protected routes (with auth middleware)
    $routes->group('', ['filter' => 'auth'], static function($routes) {
        $routes->get('dashboard', 'Admin\DashboardController::index');

        // Survey management
        $routes->get('surveys', 'Admin\SurveyController::index');
        $routes->get('surveys/create', 'Admin\SurveyController::create');
        $routes->post('surveys', 'Admin\SurveyController::store');
        $routes->get('surveys/(:num)', 'Admin\SurveyController::view/$1');
        $routes->get('surveys/(:num)/edit', 'Admin\SurveyController::edit/$1');
        $routes->post('surveys/(:num)', 'Admin\SurveyController::update/$1');
        $routes->delete('surveys/(:num)', 'Admin\SurveyController::delete/$1');
        $routes->post('surveys/(:num)/share-link', 'Admin\SurveyController::shareLink/$1');

        // Section management
        $routes->post('sections', 'Admin\SectionController::store');
        $routes->post('sections/(:num)', 'Admin\SectionController::update/$1');
        $routes->delete('sections/(:num)', 'Admin\SectionController::delete/$1');

        // Question management
        $routes->post('questions', 'Admin\QuestionController::store');
        $routes->post('questions/(:num)', 'Admin\QuestionController::update/$1');
        $routes->delete('questions/(:num)', 'Admin\QuestionController::delete/$1');
        $routes->post('questions/(:num)/options', 'Admin\QuestionController::storeOption/$1');
        $routes->delete('options/(:num)', 'Admin\QuestionController::deleteOption/$1');

        // Results
        $routes->get('surveys/(:num)/results', 'Admin\ResultsController::index/$1');
        $routes->get('surveys/(:num)/respondents', 'Admin\RespondentsController::index/$1');
        $routes->get('respondents/(:num)', 'Admin\RespondentsController::detail/$1');
        $routes->get('respondents/(:num)/export-pdf', 'Admin\RespondentsController::exportPdf/$1');
    });
});

// Public Survey Routes
$routes->get('surveys/(:alphanum)', 'Public\SurveyController::show/$1');
$routes->post('surveys/(:alphanum)/submit', 'Public\SurveyController::submit/$1', ['filter' => 'nocache']);
$routes->get('surveys/(:alphanum)/thank-you', 'Public\SurveyController::thankYou/$1');

// Default route
$routes->get('/', 'Home::index');
```

---

## Phase 6: Views & Frontend

### Alpine + Blade Integration Strategy

**Key Points:**
- Use Blade templating
- Alpine for interactivity (no build step)
- Tailwind CDN for styling
- Component-based reusable views

### Step 6.1: Base Layout
**File:** `app/Views/layout.php`

```html
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $this->renderSection('title') ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-gray-50">
    <?= $this->include('layout/navbar') ?>
    
    <main class="container mx-auto py-8">
        <?= $this->renderSection('content') ?>
    </main>

    <?= $this->include('layout/footer') ?>
</body>
</html>
```

### Step 6.2: Public Survey View
**File:** `app/Views/public/survey.php`

```html
<?= $this->extend('layout') ?>

<?= $this->section('title') ?>
<?= esc($survey['name']) ?> - Survey
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="max-w-2xl mx-auto">
    <div x-data="surveyForm()" class="space-y-8">
        <!-- Progress Bar -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex justify-between mb-2">
                <span class="text-sm font-medium text-gray-700">Progress</span>
                <span class="text-sm font-medium text-gray-700" x-text="`${currentStep + 1} of ${totalSteps}`"></span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-2">
                <div class="bg-blue-600 h-2 rounded-full transition-all duration-300" 
                     :style="`width: ${((currentStep + 1) / totalSteps) * 100}%`"></div>
            </div>
        </div>

        <!-- Survey Form -->
        <form @submit.prevent="submitSection" action="/surveys/<?= esc($passkey) ?>/submit" method="POST" class="space-y-6">
            <?= csrf_field() ?>

            <template x-for="(section, index) in sections" :key="index">
                <div x-show="currentStep === index" class="bg-white rounded-lg shadow p-6 space-y-6">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900" x-text="section.title"></h2>
                        <p class="text-gray-600 mt-2" x-text="section.description"></p>
                    </div>

                    <template x-for="question in section.questions" :key="question.id">
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-gray-900">
                                <span x-text="question.question_text"></span>
                                <span x-show="question.required" class="text-red-500">*</span>
                            </label>

                            <!-- Text Input -->
                            <input x-show="question.type === 'text'" 
                                   :name="`${question.id}`"
                                   type="text" 
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   :required="question.required">

                            <!-- Textarea -->
                            <textarea x-show="question.type === 'textarea'" 
                                      :name="`${question.id}`"
                                      rows="4"
                                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                      :required="question.required"></textarea>

                            <!-- Multiple Choice -->
                            <div x-show="question.type === 'multiple_choice'" class="space-y-2">
                                <template x-for="option in question.options" :key="option.id">
                                    <label class="flex items-center">
                                        <input type="radio" 
                                               :name="`${question.id}`"
                                               :value="option.value"
                                               class="h-4 w-4 text-blue-600"
                                               :required="question.required">
                                        <span class="ml-2 text-gray-700" x-text="option.option_text"></span>
                                    </label>
                                </template>
                            </div>

                            <!-- Scale/Rating -->
                            <div x-show="question.type === 'scale'" class="flex justify-between">
                                <template x-for="option in question.options" :key="option.id">
                                    <label class="flex flex-col items-center">
                                        <input type="radio" 
                                               :name="`${question.id}`"
                                               :value="option.value"
                                               class="h-4 w-4 text-blue-600"
                                               :required="question.required">
                                        <span class="text-xs text-gray-600 mt-1" x-text="option.option_text"></span>
                                    </label>
                                </template>
                            </div>

                            <!-- Yes/No -->
                            <div x-show="question.type === 'yesno'" class="flex gap-4">
                                <label class="flex items-center">
                                    <input type="radio" 
                                           :name="`${question.id}`"
                                           value="yes"
                                           class="h-4 w-4 text-blue-600"
                                           :required="question.required">
                                    <span class="ml-2 text-gray-700">Yes</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="radio" 
                                           :name="`${question.id}`"
                                           value="no"
                                           class="h-4 w-4 text-blue-600"
                                           :required="question.required">
                                    <span class="ml-2 text-gray-700">No</span>
                                </label>
                            </div>

                            <!-- File Upload -->
                            <input x-show="question.type === 'file_upload'" 
                                   type="file"
                                   :name="`${question.id}`"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg"
                                   :required="question.required">
                        </div>
                    </template>
                </div>
            </template>

            <!-- Navigation Buttons -->
            <div class="flex justify-between">
                <button type="button" 
                        @click="previousStep()" 
                        x-show="currentStep > 0"
                        class="px-6 py-2 bg-gray-300 text-gray-900 rounded-lg hover:bg-gray-400">
                    Previous
                </button>

                <button type="button" 
                        @click="nextStep()" 
                        x-show="currentStep < totalSteps - 1"
                        class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    Next
                </button>

                <button type="submit" 
                        x-show="currentStep === totalSteps - 1"
                        class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                    Submit
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function surveyForm() {
    return {
        currentStep: 0,
        sections: <?= json_encode($sections) ?>,
        totalSteps: <?= count($sections) ?>,
        
        nextStep() {
            if (this.currentStep < this.totalSteps - 1) {
                this.currentStep++;
                window.scrollTo(0, 0);
            }
        },
        
        previousStep() {
            if (this.currentStep > 0) {
                this.currentStep--;
                window.scrollTo(0, 0);
            }
        },
        
        submitSection(e) {
            this.$refs.form.submit();
        }
    }
}
</script>
<?= $this->endSection() ?>
```

### Step 6.3: Admin Dashboard View
**File:** `app/Views/admin/dashboard.php`

```html
<?= $this->extend('layout') ?>

<?= $this->section('title') ?>Admin Dashboard<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <h1 class="text-3xl font-bold text-gray-900">Dashboard</h1>
        <a href="/admin/surveys/create" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
            Create Survey
        </a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <template x-for="survey in surveys" :key="survey.id">
            <div class="bg-white rounded-lg shadow p-6 space-y-4">
                <h3 class="text-lg font-semibold text-gray-900" x-text="survey.name"></h3>
                <p class="text-gray-600 text-sm" x-text="survey.description"></p>
                
                <div class="space-y-2 text-sm">
                    <p><strong>Responses:</strong> <span x-text="`${stats[survey.id].completed} / ${stats[survey.id].total_respondents}`"></span></p>
                    <p><strong>Completion:</strong> <span x-text="stats[survey.id].completion_rate"></span></p>
                </div>

                <div class="flex gap-2">
                    <a :href="`/admin/surveys/${survey.id}/edit`" class="flex-1 px-4 py-2 bg-blue-100 text-blue-600 rounded-lg hover:bg-blue-200 text-center">
                        Edit
                    </a>
                    <a :href="`/admin/surveys/${survey.id}/results`" class="flex-1 px-4 py-2 bg-green-100 text-green-600 rounded-lg hover:bg-green-200 text-center">
                        Results
                    </a>
                </div>
            </div>
        </template>
    </div>
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('dashboard', () => ({
        surveys: <?= json_encode($surveys) ?>,
        stats: <?= json_encode($stats) ?>,
    }));
});
</script>
<?= $this->endSection() ?>
```

---

## Phase 7: Testing & Deployment

### Step 7.1: Unit Tests
**Directory:** `tests/Unit/`

Create tests for:
- Model validation
- Service logic
- Controller actions

**Example:** `tests/Unit/SurveyModelTest.php`
```php
<?php

namespace Tests\Unit;

use CodeIgniter\Test\CIUnitTestCase;
use App\Models\SurveyModel;

class SurveyModelTest extends CIUnitTestCase
{
    private $model;

    protected function setUp(): void
    {
        parent::setUp();
        $this->model = new SurveyModel();
    }

    public function testGeneratesPasskey()
    {
        $passkey = $this->model->generatePasskey();
        $this->assertIsString($passkey);
        $this->assertGreaterThan(0, strlen($passkey));
    }

    public function testCreatesSurvey()
    {
        $data = [
            'name' => 'Test Survey',
            'description' => 'Test Description',
            'is_public' => 1,
            'created_by' => 1,
        ];

        $result = $this->model->insert($data);
        $this->assertTrue($result);
    }
}
```

### Step 7.2: Security Considerations

**CSRF Protection:**
- ✅ Use `<?= csrf_field() ?>` in all forms
- ✅ Configured in `.env`

**Input Validation:**
- ✅ Server-side validation in Models
- ✅ Type casting in Controllers

**File Upload Security:**
- ✅ Validate file type/size
- ✅ Store outside webroot (if possible)
- ✅ Rename files to prevent execution

**SQL Injection:**
- ✅ Use Query Builder (automatic parameterization)
- ✅ Never use string concatenation in queries

**Password Security:**
- ✅ Use bcrypt hashing (password_hash/password_verify)
- ✅ Never store plaintext passwords

### Step 7.3: Performance Optimization

**Database:**
- ✅ Add indexes (already in schema)
- ✅ Use eager loading where possible
- ✅ Paginate large result sets

**Caching:**
- ✅ Cache survey structure (rarely changes)
- ✅ Cache analytics results

**Frontend:**
- ✅ Minify Tailwind CDN output
- ✅ Lazy load Alpine components

### Step 7.4: Deployment Checklist

**Pre-deployment:**
```bash
# Run tests
php spark test

# Check code style
php spark code:lint

# Check for errors
php spark lint
```

**On Server:**
1. Set `CI_ENVIRONMENT = production` in `.env`
2. Disable error display in `.env`: `app.displayErrors = false`
3. Ensure `uploads/` directory is writable
4. Set proper permissions on config files (not world-readable)
5. Use HTTPS only
6. Enable database backups

**Verification:**
- Test login flow
- Test survey submission
- Test file uploads
- Check error logs: `writable/logs/`

---

## Database Schema Adjustments

**No major changes needed** - schema is already well-designed!

**Optional enhancements:**
```sql
-- Add index for faster lookups
ALTER TABLE questions ADD INDEX idx_section_type (section_id, type);
ALTER TABLE responses ADD INDEX idx_question (question_id);

-- Add soft deletes if needed (requires migration)
ALTER TABLE surveys ADD COLUMN deleted_at TIMESTAMP NULL;
ALTER TABLE questions ADD COLUMN deleted_at TIMESTAMP NULL;
```

---

## Implementation Checklist

### Phase 1: Setup ✓
- [ ] Install CodeIgniter 4
- [ ] Configure database connection
- [ ] Set environment variables
- [ ] Create upload directories

### Phase 2: Models ✓
- [ ] AdminModel
- [ ] SurveyModel
- [ ] SectionModel
- [ ] QuestionModel
- [ ] QuestionOptionModel
- [ ] RespondentModel
- [ ] ResponseModel
- [ ] FileModel

### Phase 3: Services ✓
- [ ] FileUploaderService
- [ ] SurveyPublisherService
- [ ] ResponseService
- [ ] AnalyticsService

### Phase 4: Middleware ✓
- [ ] AuthFilter
- [ ] NoCacheFilter
- [ ] Register in Filters.php

### Phase 5: Controllers ✓
**Public:**
- [ ] Public\SurveyController
- [ ] Public\RespondentController

**Admin:**
- [ ] Admin\AuthController
- [ ] Admin\DashboardController
- [ ] Admin\SurveyController
- [ ] Admin\SectionController
- [ ] Admin\QuestionController
- [ ] Admin\RespondentsController
- [ ] Admin\ResultsController
- [ ] Admin\FileController

### Phase 6: Views ✓
**Shared:**
- [ ] layout.php
- [ ] navbar.php
- [ ] footer.php

**Public:**
- [ ] public/survey.php
- [ ] public/thank-you.php

**Admin:**
- [ ] admin/login.php
- [ ] admin/dashboard.php
- [ ] admin/surveys/create.php
- [ ] admin/surveys/edit.php
- [ ] admin/sections/form.php
- [ ] admin/questions/form.php
- [ ] admin/respondents.php
- [ ] admin/respondent-detail.php
- [ ] admin/results.php

**Components:**
- [ ] form-input.php
- [ ] form-select.php
- [ ] form-textarea.php
- [ ] form-fileupload.php

### Phase 7: Testing ✓
- [ ] Unit tests for models
- [ ] Feature tests for controllers
- [ ] Functional tests for workflows

### Phase 8: Deployment ✓
- [ ] Security audit
- [ ] Performance optimization
- [ ] Backup configuration
- [ ] Production deployment

---

## Frontend Architecture with Alpine + Blade

### Why This Approach is Superior for You:

1. **No Build Step**
   - Deploy directly from editor
   - No webpack/vite configuration
   - Instant development feedback

2. **Easier to Maintain**
   - Blade templates are PHP-native
   - No separate API to maintain
   - Smaller JavaScript footprint

3. **Form Handling**
   - Server-side rendering is faster
   - Progressive enhancement works
   - Better accessibility

4. **Alpine.js Usage Patterns**

```html
<!-- Show/hide sections based on question type -->
<div x-show="question.type === 'multiple_choice'">
    <!-- options here -->
</div>

<!-- Real-time validation feedback -->
<input @change="validateField($event)" />

<!-- Dynamic field requirements -->
<input :required="question.required" />

<!-- File preview before upload -->
<input type="file" @change="previewFile($event)" />
<img x-show="preview" :src="preview" />
```

---

## Quick Reference: API Endpoints

### Public Routes
```
GET  /surveys/:passkey                  - Display survey
POST /surveys/:passkey/submit           - Submit responses
GET  /surveys/:passkey/thank-you        - Confirmation page
```

### Admin Routes (Protected)
```
POST /admin/login                       - Admin login
GET  /admin/dashboard                   - View dashboard
GET  /admin/surveys                     - List surveys
POST /admin/surveys                     - Create survey
GET  /admin/surveys/:id/edit            - Edit survey
POST /admin/surveys/:id                 - Update survey
DELETE /admin/surveys/:id               - Delete survey
POST /admin/surveys/:id/share-link      - Generate share link
GET  /admin/surveys/:id/results         - View results
GET  /admin/surveys/:id/respondents     - List respondents
GET  /admin/respondents/:id             - View response details
```

---

## Final Notes

- **Scalability**: CodeIgniter modules pattern allows easy expansion
- **Maintainability**: Clear separation of concerns (Models, Services, Controllers)
- **Security**: Built-in CSRF, input validation, SQL injection prevention
- **Performance**: Database indexes, caching-ready structure
- **Developer Experience**: Alpine + Blade = no build step, instant feedback

**Estimated Timeline:**
- straight forward one day development
