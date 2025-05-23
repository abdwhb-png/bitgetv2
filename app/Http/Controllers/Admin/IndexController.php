<?php

namespace App\Http\Controllers\Admin;

use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Models\PaymentProof;
use App\Models\PaymentMethod;
use App\Models\SystemSetting;
use App\Models\CustomerService;
use App\Concerns\ValidationRulesTrait;
use App\Http\Controllers\BaseController;

class IndexController extends BaseController
{
    use ValidationRulesTrait;

    public function index()
    {
        $pMethods = PaymentMethod::all();
        $setting = SystemSetting::firstOrFail();
        $cServices = CustomerService::all();
        $pProofs = PaymentProof::latest()->get()->map(function ($proof) {
            return [
                'id' => $proof->id,
                'file_url' => $proof->file_url,
                'detail' => [
                    'user' => $proof->account->user ? $proof->account->user->info->fullName() : 'unknown',
                    'method' => $proof->method,
                    'amount' => $proof->amount,
                    'payment_ref_id' => $proof->ref_id,
                    'created' => $proof->created_at->diffForHumans(),
                ]
            ];
        });

        return Inertia::render('Dashboard', [
            'setting' => $setting,
            'cServices' => $cServices,
            'pMethods' => $pMethods,
            'pProofs' => $pProofs,
        ]);
    }

    public function pmethodStore(Request $request)
    {
        $validated = $request->validate($this->pMethodRules('store'));

        PaymentMethod::create($validated);

        return back(303)->with('status', 'Payment method created successfully');
    }

    public function pmethodUpdate(PaymentMethod $item, Request $request)
    {
        $validated = $request->validate($this->pMethodRules('update', $item->id));

        $item->update($validated);

        return back(303)->with('status', 'Payment method updated');
    }

    public function pmethodDestroy(PaymentMethod $item)
    {
        $item->delete();

        return back(303)->with('status', 'Payment method deleted');
    }

    public function cServiceUpdate(CustomerService $item, Request $request)
    {
        $validated = $request->validate($this->cServiceRules('update', $item->id));

        $item->update($validated);

        return back(303)->with('status', 'customer service updated');
    }

    public function settingUpdate(SystemSetting $item, Request $request)
    {
        $validated = $request->validate($this->settingRules('update'));

        $item->update($validated);

        return back(303)->with('status', 'setting updated');
    }

    public function pProofsUpdate()
    {
        return;
    }
}
