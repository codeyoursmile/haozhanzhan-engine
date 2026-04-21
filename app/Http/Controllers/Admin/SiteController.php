<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Site;
use Illuminate\Http\Request;

class SiteController extends Controller
{
    public function index()
    {
        $site = Site::first();
        return response()->json($site);
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'site_name' => 'required|string|max:255',
            'site_logo' => 'nullable|string|max:500',
            'site_keywords' => 'nullable|string|max:500',
            'site_description' => 'nullable|string',
        ]);

        $site = Site::first();
        
        if ($site) {
            $site->update($validated);
        } else {
            $site = Site::create($validated);
        }
        
        return response()->json($site);
    }
}