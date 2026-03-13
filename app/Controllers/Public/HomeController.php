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
        // This endpoint is no longer needed (surveys are auth-protected)
        // But kept for backward compatibility
        return redirect()->to(base_url('admin/index'));
    }
}
