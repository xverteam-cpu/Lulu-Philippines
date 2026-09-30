<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FranchiseApplication;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FranchiseApplicationController extends Controller
{
    public function index(Request $request): View
    {
        abort_unless($request->user()?->is_admin, 403);

        return view('admin.franchises', [
            'applications' => FranchiseApplication::query()
                ->latest()
                ->paginate(20),
        ]);
    }

    public function show(Request $request, FranchiseApplication $franchiseApplication): View
    {
        abort_unless($request->user()?->is_admin, 403);

        return view('admin.franchise-show', [
            'application' => $franchiseApplication,
        ]);
    }
}
