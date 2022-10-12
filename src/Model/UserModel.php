<?php

namespace App\Model;

use App\Entity\User;
use Nienfba\Framework\Model;


class UserModel extends Model {

    public function getEntityName(): string {
        return User::class;
    }
}