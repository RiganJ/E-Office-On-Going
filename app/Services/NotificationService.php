<?php
namespace App\Services;
use App\Models\{Document,User,WorkflowInstance}; use App\Notifications\DocumentActionNotification;
class NotificationService {
 public static function user(?User $user,Document $document,string $event,?string $message=null):void { if($user)$user->notify(new DocumentActionNotification($document,$event,$message)); }
 public static function approvers(WorkflowInstance $instance,Document $document,string $event):void { $instance->approvals()->where('status','PENDING')->with('approver')->get()->each(fn($approval)=>self::user($approval->approver,$document,$event,'Dokumen menunggu keputusan Anda.')); }
}
