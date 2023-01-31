<?php

namespace Masterei\Sentry;

use Illuminate\Routing\Controller;

class SentryController extends Controller
{
    public function index()
    {
        return view('sentry::limitless.index');
    }

    public function users()
    {
        return view('sentry::users');
    }
}
