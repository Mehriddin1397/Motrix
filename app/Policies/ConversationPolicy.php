<?php

namespace App\Policies;

use App\Models\User;
use Modules\Market\Models\Conversation;

class ConversationPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Conversation $conversation): bool
    {
        return $conversation->buyer_id === $user->id || $conversation->seller_id === $user->id;
    }

    public function reply(User $user, Conversation $conversation): bool
    {
        return $this->view($user, $conversation);
    }
}
