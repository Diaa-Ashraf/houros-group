<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Http\Requests\Storefront\StoreJobApplicationRequest;
use App\Models\Branch;
use App\Services\JobApplicationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CareerController extends Controller
{
    public function __construct(
        protected JobApplicationService $jobApplicationService
    ) {}

    /**
     * Display careers & available vacancies page with application form.
     */
    public function index(): View
    {
        $branches = Branch::select(['id', 'name'])
            ->where('is_active', true)
            ->orderBy('sort_order', 'asc')
            ->get();

        $openPositions = [[
            'title' => 'طلبات توظيف عامة',
            'department' => 'التوريدات والتشطيبات والديكور',
            'type' => 'تسجيل اهتمام',
            'description' => 'أرسل خبراتك في مجال البناء أو التشطيب أو المبيعات. سنتواصل عند توفر فرصة مناسبة.',
        ]];

        return view('storefront.careers.index', compact('branches', 'openPositions'));
    }

    /**
     * Store submitted job application with uploaded CV.
     */
    public function store(StoreJobApplicationRequest $request): RedirectResponse
    {
        $this->jobApplicationService->create(
            $request->validated(),
            $request->file('cv_file')
        );

        return back()->with('success', __('تم استلام طلب التوظيف بنجاح! سيقوم فريق الموارد البشرية بالتواصل معك بعد مراجعة بياناتك.'));
    }
}
