<?php

declare(strict_types=1);

namespace App\Action\User;

use App\Models\User;

final class GetUserByIdResponse
{
    private User $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function getUser(): User
    {
        return $this->user;
    }
}
