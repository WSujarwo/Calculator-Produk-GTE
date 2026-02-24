<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MarketingController extends Controller
{
    public function index(Request $request): View
    {
        $marketingList = $this->marketingList($request);
        $editId = (int) $request->query('edit');
        $editing = collect($marketingList)->firstWhere('id', $editId);

        return view('settings.marketing', [
            'marketingList' => $marketingList,
            'editing' => $editing,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateRequest($request);
        $marketingList = $this->marketingList($request);

        $nextId = empty($marketingList) ? 1 : (max(array_column($marketingList, 'id')) + 1);

        $marketingList[] = [
            'id' => $nextId,
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? '-',
        ];

        $this->saveMarketingList($request, $marketingList);

        return redirect()->route('setting.marketing.index')->with('success', 'Marketing berhasil ditambahkan.');
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $validated = $this->validateRequest($request);
        $marketingList = $this->marketingList($request);

        $index = collect($marketingList)->search(fn (array $item) => $item['id'] === $id);
        if ($index === false) {
            return redirect()->route('setting.marketing.index')->with('error', 'Data marketing tidak ditemukan.');
        }

        $marketingList[$index]['name'] = $validated['name'];
        $marketingList[$index]['email'] = $validated['email'];
        $marketingList[$index]['phone'] = $validated['phone'] ?? '-';

        $this->saveMarketingList($request, $marketingList);

        return redirect()->route('setting.marketing.index')->with('success', 'Marketing berhasil diupdate.');
    }

    public function destroy(Request $request, int $id): RedirectResponse
    {
        $marketingList = $this->marketingList($request);
        $filtered = array_values(array_filter($marketingList, fn (array $item) => $item['id'] !== $id));

        $this->saveMarketingList($request, $filtered);

        return redirect()->route('setting.marketing.index')->with('success', 'Marketing berhasil dihapus.');
    }

    private function marketingList(Request $request): array
    {
        return $request->session()->get('settings.marketing', []);
    }

    private function saveMarketingList(Request $request, array $marketingList): void
    {
        $request->session()->put('settings.marketing', $marketingList);
    }

    private function validateRequest(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:100'],
            'phone' => ['nullable', 'string', 'max:30'],
        ]);
    }
}

