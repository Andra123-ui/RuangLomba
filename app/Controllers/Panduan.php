<?php

namespace App\Controllers;

use App\Models\CategoryModel;

class Panduan extends BaseController
{
    public function organizer(): string
    {
        return view('panduan_organizer', [
            'title'      => 'Panduan Organizer',
            'categories' => (new CategoryModel())->orderBy('name', 'ASC')->findAll(),
            'types'      => ['Lomba', 'Beasiswa', 'Webinar', 'Workshop', 'Seminar', 'Program & Event'],
        ]);
    }
}