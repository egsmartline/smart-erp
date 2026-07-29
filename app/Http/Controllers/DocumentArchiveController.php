<?php

namespace App\Http\Controllers;

use App\Models\DocumentArchive;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DocumentArchiveController extends TenantAwareController
{
    public function index()
    {
        $query = $this->tenantQuery(DocumentArchive::class);

        if ($search = request('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('file_name', 'like', "%{$search}%")
                  ->orWhere('notes', 'like', "%{$search}%");
            });
        }

        if ($category = request('category')) {
            $query->where('category', $category);
        }

        if ($dateFrom = request('date_from')) {
            $query->whereDate('created_at', '>=', $dateFrom);
        }

        if ($dateTo = request('date_to')) {
            $query->whereDate('created_at', '<=', $dateTo);
        }

        $documents = $query->with('uploader')->latest()->paginate(20);
        $categories = $this->tenantQuery(DocumentArchive::class)
            ->whereNotNull('category')
            ->select('category')
            ->distinct()
            ->pluck('category');

        return view('document-archives.index', compact('documents', 'categories'));
    }

    public function create()
    {
        return view('document-archives.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'file' => 'required|file|max:51200',
            'category' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
        ]);

        $file = $request->file('file');
        $fileName = time() . '_' . $file->getClientOriginalName();
        $filePath = $file->storeAs('documents/' . $this->getTenantId(), $fileName, 'public');

        DocumentArchive::create([
            'tenant_id' => $this->getTenantId(),
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'file_path' => $filePath,
            'file_name' => $file->getClientOriginalName(),
            'file_type' => $file->getClientMimeType(),
            'file_size' => $file->getSize(),
            'category' => $validated['category'] ?? null,
            'notes' => $validated['notes'] ?? null,
            'uploaded_by' => auth()->id(),
        ]);

        return redirect()->route('document-archives.index')
            ->with('success', 'تم رفع المستند بنجاح');
    }

    public function show(DocumentArchive $documentArchive)
    {
        if ($documentArchive->tenant_id !== $this->getTenantId()) {
            abort(403);
        }

        $documentArchive->load('uploader');
        return view('document-archives.show', compact('documentArchive'));
    }

    public function edit(DocumentArchive $documentArchive)
    {
        if ($documentArchive->tenant_id !== $this->getTenantId()) {
            abort(403);
        }

        return view('document-archives.edit', compact('documentArchive'));
    }

    public function update(Request $request, DocumentArchive $documentArchive)
    {
        if ($documentArchive->tenant_id !== $this->getTenantId()) {
            abort(403);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
            'file' => 'nullable|file|max:51200',
        ]);

        $data = [
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'category' => $validated['category'] ?? null,
            'notes' => $validated['notes'] ?? null,
        ];

        if ($request->hasFile('file')) {
            Storage::disk('public')->delete($documentArchive->file_path);

            $file = $request->file('file');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('documents/' . $this->getTenantId(), $fileName, 'public');

            $data['file_path'] = $filePath;
            $data['file_name'] = $file->getClientOriginalName();
            $data['file_type'] = $file->getClientMimeType();
            $data['file_size'] = $file->getSize();
        }

        $documentArchive->update($data);

        return redirect()->route('document-archives.show', $documentArchive)
            ->with('success', 'تم تحديث المستند بنجاح');
    }

    public function destroy(DocumentArchive $documentArchive)
    {
        if ($documentArchive->tenant_id !== $this->getTenantId()) {
            abort(403);
        }

        Storage::disk('public')->delete($documentArchive->file_path);
        $documentArchive->delete();

        return redirect()->route('document-archives.index')
            ->with('success', 'تم حذف المستند بنجاح');
    }

    public function download(DocumentArchive $documentArchive)
    {
        if ($documentArchive->tenant_id !== $this->getTenantId()) {
            abort(403);
        }

        if (!Storage::disk('public')->exists($documentArchive->file_path)) {
            return back()->with('error', 'الملف غير موجود');
        }

        return Storage::disk('public')->download($documentArchive->file_path, $documentArchive->file_name);
    }
}
