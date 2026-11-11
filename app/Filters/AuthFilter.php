<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class AuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();

            if (! $session->get('isLoggedIn')) {
            // Ingat halaman tujuan (hanya GET) supaya setelah login kembali ke sana
            if (strtolower($request->getMethod()) === 'get') {
                $session->set('intendedUrl', (string) current_url());
            }

            return redirect()->to(base_url('masuk'))
                ->with('error', 'Silakan masuk terlebih dahulu.');
        }

        if (! empty($arguments) && ! in_array($session->get('userRole'), $arguments, true)) {
            return redirect()->to(base_url('/'))
                ->with('error', 'Kamu tidak punya akses ke halaman itu.');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
    }
}