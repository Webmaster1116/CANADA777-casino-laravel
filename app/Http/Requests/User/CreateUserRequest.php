<?php 
namespace VanguardLTE\Http\Requests\User
{
    class CreateUserRequest extends \VanguardLTE\Http\Requests\Request
    {
        public function rules()
        {
            $rules = [
                'username' => 'required|regex:/^[A-Za-z0-9]+$/|unique:users,username', 
                'email' => 'required|email|unique:users,email', 
                'password' => 'required|min:6|confirmed',
                'first_name' => 'required',
                'last_name' => 'required',
                'birthday' => 'required',
                'phone' => 'required',
                'postalCode' => 'required'
            ];
            return $rules;
        }
    }

}
