<?php

namespace App\Policies;

use App\Models\Post;
use App\Models\User;

class PostPolicy
{
    /**
     * Можно ли смотреть список постов.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Можно ли смотреть конкретный пост.
     */
    public function view(User $user, Post $post): bool
    {
        return true;
    }

    /**
     * Создавать пост может любой авторизованный пользователь.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Редактировать пост может только его автор.
     */
    public function update(User $user, Post $post): bool
    {
        return $user->id === $post->user_id;
    }

    /**
     * Удалять пост может только его автор.
     */
    public function delete(User $user, Post $post): bool
    {
        return $user->id === $post->user_id;
    }

    /**
     * Восстановление удалённых постов в этой практике не используется.
     */
    public function restore(User $user, Post $post): bool
    {
        return false;
    }

    /**
     * Полное удаление в этой практике не используется.
     */
    public function forceDelete(User $user, Post $post): bool
    {
        return false;
    }
}
