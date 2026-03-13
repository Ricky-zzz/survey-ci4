<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\AdminModel;

class AuthController extends BaseController
{
    public function index()
    {
        if (session()->get('admin_id')) {
            return redirect()->to(base_url('admin/dashboard'));
        }

        return view('admin/index');
    }

    public function register()
    {
        if (session()->get('admin_id')) {
            return redirect()->to(base_url('admin/dashboard'));
        }

        return view('admin/register');
    }

    public function doRegister()
    {
        $rules = [
            'username' => 'required|min_length[3]|max_length[100]|is_unique[admins.username]',
            'email'    => 'required|valid_email|is_unique[admins.email]',
            'password' => 'required|min_length[6]',
            'confirm_password' => 'required|matches[password]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()
                ->with('errors', $this->validator->getErrors())
                ->withInput();
        }

        $model = new AdminModel();
        $model->insert([
            'username' => $this->request->getPost('username'),
            'email'    => $this->request->getPost('email'),
            'password_hash' => $model->hashPassword($this->request->getPost('password')),
        ]);

        return redirect()->to(base_url('admin/index'))
            ->with('success', 'Account created! Please log in.');
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
        return redirect()->to(base_url('admin/index'));
    }
}
