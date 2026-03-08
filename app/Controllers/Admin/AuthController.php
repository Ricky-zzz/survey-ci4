<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\AdminModel;

class AuthController extends BaseController
{
    public function login()
    {
        if (session()->get('admin_id')) {
            return redirect()->to(base_url('admin/dashboard'));
        }

        return view('admin/login');
    }

    public function doLogin()
    {
        $rules = [
            'username' => 'required|min_length[3]',
            'password' => 'required',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()
                ->with('errors', $this->validator->getErrors())
                ->withInput();
        }

        $model  = new AdminModel();
        $admin  = $model->findByUsername($this->request->getPost('username'));

        if (! $admin || ! $model->validatePassword($this->request->getPost('password'), $admin['password_hash'])) {
            return redirect()->back()
                ->with('error', 'Invalid username or password.')
                ->withInput();
        }

        session()->set([
            'admin_id'   => $admin['id'],
            'admin_user' => $admin['username'],
        ]);

        return redirect()->to(base_url('admin/dashboard'));
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to(base_url('admin/login'));
    }
}
