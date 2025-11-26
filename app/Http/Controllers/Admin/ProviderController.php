<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Provider;

class ProviderController extends Controller
{
    public function index()
    {
        $providers = Provider::orderBy('code')->get();

        return view('admin.providers.index', compact('providers'));
    }

    public function show(Provider $provider)
    {
        // Implicit binding will use primary key 'code'
        return view('admin.providers.show', compact('provider'));
    }
}
