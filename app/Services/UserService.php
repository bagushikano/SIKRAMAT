<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\DB;

class UserService
{
    public function find($user_id)
    {
        $user = User::where('id', $user_id)->first();
        return $user;
    }

    public function changePassword($password, $user_id)
    {
        try {
            DB::beginTransaction();
                User::where('id', $user_id)->update([
                    'password' => bcrypt($password)
                ]);
            DB::commit();

            return([
                'status' => 'success',
                'message' => 'Password berhasil dirubah'
            ]);
        } catch (\Throwable $th) {
            DB::rollback();

            return([
                'status' => 'failed',
                'message' => $th->getMessage()
            ]);
        }
    }
}
