<?php

namespace Nando118\StudiKasus\PHP\LoginManagement\Model;

class UserPasswordUpdateRequest
{
    public ?string $id = null;
    public ?string $oldPassword = null;
    public ?string $newPassword = null;
}