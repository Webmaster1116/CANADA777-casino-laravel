<?php 
namespace VanguardLTE\Http\Requests\User
{
    class UpdateProfilePasswordRequest extends \VanguardLTE\Http\Requests\Request
    {
        public function rules()
        {
            return [
                'old_password' => 'required', 
                'password' => 'required', 
            ];
        }
    }

}
