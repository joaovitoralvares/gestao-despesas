<?php

namespace App\Policies;

use App\Models\Despesa;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class DespesaPolicy
{
    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Despesa $despesa): Response
    {
        return $user->id === $despesa->user_id
            ? Response::allow()
            : Response::denyAsNotFound();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Despesa $despesa): bool
    {
        //
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Despesa $despesa): Response
    {
        return $user->id === $despesa->user_id
            ? Response::allow()
            : Response::denyAsNotFound();
    }

}
