<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

use DB;

class UserRole extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'role_user';

    /**
     * Primary key of table.
     *
     * @var string
     */
    protected $primaryKey = "user_id";

    /**
     * save user role by user id.
     *
     * @param string $userId.
     * @param string $roleId.
     *
     * @return id of role_user.
     */
    public function saveUserRole($userId, $roleId)
    {
        return DB::table($this->table)->insertGetId(
            [
                'user_id' => $userId,
                'role_id' => $roleId,
            ]
        );
    }

    /**
     * update user role by user id.
     *
     * @param string $userId.
     * @param string $roleId.
     *
     */
    public function updateUserRole($userId, $roleId)
    {
        DB::table($this->table)
            ->where('user_id', $userId)
            ->update(['role_id'=>$roleId]);
    }
}