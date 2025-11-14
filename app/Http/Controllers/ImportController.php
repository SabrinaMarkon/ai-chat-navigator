<?php

namespace App\Http\Controllers;

use App\Services\Import\ImportService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ImportController extends Controller
{
    public function __construct(
        private ImportService $importService
    ) {}

    public function index(): Response
    {
        return Inertia::render('Import/Index');
    }

    public function store(Request $request)
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:json', 'max:10240'], // 10MB max
        ]);

        try {
            $file = $request->file('file');
            $path = $file->storeAs('imports', $file->getClientOriginalName());

            $result = $this->importService->import(
                \Storage::path($path),
                auth()->id()
            );

            // Clean up uploaded file
            \Storage::delete($path);

            return redirect()->route('import.index')->with('success',
                "Successfully imported {$result['imported']} conversation(s) from {$result['platform']}."
            );

        } catch (\Exception $e) {
            return redirect()->route('import.index')->with('error',
                "Import failed: {$e->getMessage()}"
            );
        }
    }
}
