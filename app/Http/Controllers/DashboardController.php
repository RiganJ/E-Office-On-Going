<?php
namespace App\Http\Controllers; use App\Models\{Document,Disposition,WorkflowApproval};
class DashboardController extends Controller
{
    public function __invoke()
    {
        $u = auth()->user();
        $daily = collect(range(6, 0))->map(function ($offset) {
            $date = today()->subDays($offset);

            return ['label' => $date->translatedFormat('D'), 'count' => Document::whereDate('created_at', $date)->count()];
        });

        return view('dashboard.index', [
            'pending' => WorkflowApproval::where('approver_user_id', $u->id)->where('status', 'PENDING')->count(),
            'dispositions' => Disposition::where('to_user_id', $u->id)->whereIn('status', ['UNREAD', 'READ', 'IN_PROGRESS'])->count(),
            'documents' => Document::whereDate('created_at', today())->count(),
            'totalDocuments' => Document::count(),
            'completed' => Document::whereIn('status', ['COMPLETED', 'ARCHIVED', 'APPROVED'])->count(),
            'daily' => $daily,
            'chartMax' => max(5, $daily->max('count')),
            'recent' => Document::with(['type', 'creator'])->latest()->limit(6)->get(),
            'deadlines' => Document::with('type')->whereNotNull('deadline')->whereNotIn('status',['COMPLETED','ARCHIVED'])->orderBy('deadline')->limit(8)->get(),
            'dueSoon' => Document::whereNotNull('deadline')->whereBetween('deadline',[now(),now()->addDays(3)])->whereNotIn('status',['COMPLETED','ARCHIVED'])->count(),
            'overdue' => Document::whereNotNull('deadline')->where('deadline','<',now())->whereNotIn('status',['COMPLETED','ARCHIVED'])->count(),
        ]);
    }
}
