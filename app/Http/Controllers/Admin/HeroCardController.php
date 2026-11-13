<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HeroCard;
use App\Http\Requests\Admin\StoreHeroCardRequest;
use App\Http\Requests\Admin\UpdateHeroCardRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class HeroCardController extends Controller
{
    /**
     * Display a listing of the hero cards.
     */
    public function index(): View
    {
        $cards = HeroCard::orderBy('order', 'asc')->orderBy('id', 'asc')->get();
        return view('admin.hero-cards.index', compact('cards'));
    }

    /**
     * Show the form for creating a new hero card.
     */
    public function create(): View
    {
        $nextOrder = (HeroCard::max('order') ?? 0) + 1;
        return view('admin.hero-cards.create', compact('nextOrder'));
    }

    /**
     * Store a newly created hero card in storage.
     */
    public function store(StoreHeroCardRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['is_active'] = $request->boolean('is_active', true);
        $data['order'] = $request->filled('order') ? (int)$request->input('order') : ((HeroCard::max('order') ?? 0) + 1);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('hero-cards', 'public');
            $data['image'] = $path;
        }

        HeroCard::create($data);

        return redirect()->route('admin.hero-cards.index')->with('success', 'تم إنشاء كارت الهيرو الترويجي بنجاح.');
    }

    /**
     * Show the form for editing the specified hero card.
     */
    public function edit(HeroCard $hero_card): View
    {
        return view('admin.hero-cards.edit', compact('hero_card'));
    }

    /**
     * Update the specified hero card in storage.
     */
    public function update(UpdateHeroCardRequest $request, HeroCard $hero_card): RedirectResponse
    {
        $data = $request->validated();
        $data['is_active'] = $request->boolean('is_active', true);
        $data['order'] = $request->filled('order') ? (int)$request->input('order') : $hero_card->order;

        if ($request->hasFile('image')) {
            if ($hero_card->image && !str_starts_with($hero_card->image, 'assets/')) {
                Storage::disk('public')->delete($hero_card->image);
            }
            $path = $request->file('image')->store('hero-cards', 'public');
            $data['image'] = $path;
        }

        $hero_card->update($data);

        return redirect()->route('admin.hero-cards.index')->with('success', 'تم تحديث بيانات كارت الهيرو بنجاح.');
    }

    /**
     * Toggle active state.
     */
    public function toggle(HeroCard $hero_card): RedirectResponse
    {
        $hero_card->update([
            'is_active' => !$hero_card->is_active,
        ]);

        $statusText = $hero_card->is_active ? 'تفعيل' : 'تعطيل';
        return redirect()->back()->with('success', "تم {$statusText} عرض الكارت بنجاح.");
    }

    /**
     * Remove the specified hero card from storage.
     */
    public function destroy(HeroCard $hero_card): RedirectResponse
    {
        if ($hero_card->image && !str_starts_with($hero_card->image, 'assets/')) {
            Storage::disk('public')->delete($hero_card->image);
        }

        $hero_card->delete();

        return redirect()->route('admin.hero-cards.index')->with('success', 'تم حذف كارت الهيرو بنجاح.');
    }
}

