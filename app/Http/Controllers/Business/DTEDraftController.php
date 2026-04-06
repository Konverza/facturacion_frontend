<?php

namespace App\Http\Controllers\Business;

use App\Http\Controllers\Controller;
use App\Models\BusinessUser;
use App\Models\DTE;
use Illuminate\Support\Facades\Session;

class DTEDraftController extends Controller
{
    public function index()
    {
        $businessId = Session::get('business') ?? null;
        $authUser = auth()->user();

        $businessUser = BusinessUser::where('business_id', $businessId)
            ->where('user_id', $authUser->id)
            ->first();

        $draftsQuery = DTE::query()
            ->where('business_id', $businessId)
            ->where('status', 'pending');

        if ($authUser->only_fcf) {
            $draftsQuery->where('type', '01');
        }

        if (!$businessUser?->see_others_dtes) {
            $draftsQuery->where(function ($query) use ($authUser) {
                $query->where('user_id', $authUser->id)
                    ->orWhereNull('user_id');
            });
        }

        $drafts = $draftsQuery
            ->with('user:id,name')
            ->orderByDesc('updated_at')
            ->paginate(15)
            ->withQueryString();

        return view('business.documents.drafts', [
            'drafts' => $drafts,
            'canSeeOthersDtes' => (bool) ($businessUser?->see_others_dtes),
        ]);
    }
}
