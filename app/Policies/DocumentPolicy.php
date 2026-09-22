<?php
namespace App\Policies;
use App\Models\{Document,User};
class DocumentPolicy {
 public function view(User $user,Document $document):bool{return $user->hasPermission('view documents')&&($document->creator_id===$user->id||$user->hasPermission('view all documents'));}
 public function create(User $user):bool{return $user->hasPermission('create documents');}
 public function update(User $user,Document $document):bool{return $document->creator_id===$user->id&&in_array($document->status,['DRAFT','REVISION_REQUIRED'],true);}
 public function delete(User $user,Document $document):bool{return $this->update($user,$document);}
 public function submit(User $user,Document $document):bool{return $this->update($user,$document)&&$document->workflowInstance?->status==='REVISION_REQUIRED';}
 public function approve(User $user,Document $document):bool{$instance=$document->workflowInstance;return $user->hasPermission('approve documents')&&$instance?->status==='IN_REVIEW'&&$instance->approvals()->where('approver_user_id',$user->id)->where('status','PENDING')->exists();}
 public function download(User $user,Document $document):bool{return $this->view($user,$document);}
 public function archive(User $user,Document $document):bool{return $this->view($user,$document)&&in_array($document->status,['COMPLETED','ARCHIVED'],true);}
}
