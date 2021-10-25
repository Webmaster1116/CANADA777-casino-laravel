<?php

namespace VanguardLTE\Http\Controllers\Web\Backend;

use Illuminate\Http\Request;
use VanguardLTE\Http\Controllers\Controller;

class AutomizyController extends Controller
{
    public function index(\Illuminate\Http\Request $request)
    {
        $automizy_api = new \VanguardLTE\Lib\automizy_Api;
        $lists = $automizy_api->getAllLists();
        $smart_lists = []; 
        if(count($lists['smartLists']) > 0) {
            $smart_lists = $lists['smartLists'];
        }
        return view('backend.automizy.list', compact('smart_lists'));
    }
    public function add_list(\Illuminate\Http\Request $request)
    {
        if ($request->isMethod('get')){
            return view('backend.automizy.add_list');
        }
        if ($request->isMethod('post')){
            
            $data = ["name" => $request->name];
            $automizy_api = new \VanguardLTE\Lib\automizy_Api;
            $lists = $automizy_api->createList($data);
            return redirect()->route('backend.automizy.list');
        }
    }
    public function edit_list(\Illuminate\Http\Request $request, $id)
    {
        $automizy_api = new \VanguardLTE\Lib\automizy_Api;
        $smart_list = $automizy_api->getListById($id);
        $all_contacts = $automizy_api->getAllContactsByList($smart_list['id']);
        $automizy_contacts = $all_contacts['contacts'];
        $original_list_1  = \VanguardLTE\User::where('role_id', 1)->where('email', '!=', '')->pluck('email');
        $original_list_2  = \VanguardLTE\UserFun::where('email', '!=', '')->pluck('email');;
        $new_contacts = [];
        $existing_contact = [];
        if(count($automizy_contacts) > 0){
            foreach($automizy_contacts as $val){
                $existing_contact[] = $val['email'];
            }
            $new_contact_1 = \VanguardLTE\User::whereNotIn('email', $existing_contact)->where('email','!=', '')->where('role_id', 1)->pluck('email');
            $new_contact_2 = \VanguardLTE\UserFun::whereNotIn('email', $existing_contact)->pluck('email');
            $new_contacts = array_merge((array)json_decode($new_contact_1), (array)json_decode($new_contact_2));
        }else{
            $new_contact_1 = \VanguardLTE\User::where('role_id', 1)->where('email', '!=', '')->pluck('email');
            $new_contact_2 = \VanguardLTE\UserFun::where('email', '!=', '')->pluck('email');
            $new_contacts = array_merge((array)json_decode($new_contact_1), (array)json_decode($new_contact_2));
        }
        if ($request->isMethod('get')){
            return view('backend.automizy.edit_list', compact('smart_list', 'new_contacts', 'automizy_contacts'));
        }
        if ($request->isMethod('post')){
            $data = ["name" => $request->name];
            $automizy_api = new \VanguardLTE\Lib\automizy_Api;
            $lists = $automizy_api->editList($data, $id);
            return redirect()->route('backend.automizy.list');
        }
    }
    public function delete_list(\Illuminate\Http\Request $request, $id)
    {
        $automizy_api = new \VanguardLTE\Lib\automizy_Api;
        $smart_list = $automizy_api->deleteList($id);
        return redirect()->route('backend.automizy.list');
    }
    public function add_contacts(\Illuminate\Http\Request $request, $id, $email)
    {
        $data = ['email' => $email];
        $automizy_api = new \VanguardLTE\Lib\automizy_Api;
        $automizy_api->addContactsByList($data, $id);
        return redirect()->route('backend.automizy.edit_list', $id);
    }
    // public function delete_contacts(\Illuminate\Http\Request $request)
    // {
    //     if ($request->isMethod('post')){
    //         $data = ['email' => $contact];
    //         $automizy_api->addContactsByList($data, $id);
    //         return redirect()->route('backend.automizy.edit_list', $id);
    //     }
    // }
}
