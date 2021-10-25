<?php

namespace VanguardLTE\Http\Controllers\Web\Backend;

use Illuminate\Http\Request;
use VanguardLTE\Http\Controllers\Controller;

class NotificationsController extends Controller
{
    public $frequency_type = [
        'One Time',
        'Once a Day',
        'Once a Month'
    ];
    public function index(\Illuminate\Http\Request $request)
    {
        $frequency_type = $this->frequency_type;
        $notifications = \VanguardLTE\Notifications::orderBy('created_at', 'DESC')->get();
        
        return view('backend.notifications.list', compact('notifications', 'frequency_type'));
    }
    public function add(\Illuminate\Http\Request $request)
    {
        $frequency_type = $this->frequency_type;
        if ($request->isMethod('get')){
            return view('backend.notifications.add', compact('frequency_type'));
        }
        if ($request->isMethod('post')){
            $image_path = '';
            if ($request->hasFile('image')) {

                $request->validate([
                    'image' => 'mimes:jpeg,bmp,png' // Only allow .jpg, .bmp and .png file types.
                ]);
    
                // Save the file locally in the storage/public/ folder under a new folder named /product
                $image_path = $request->file('image')->hashName();

                $request->image->move(public_path('notify'), $image_path);
            }
            if(!isset($request->campaign)){
                $campaign = 0;
            }else{
                $campaign = 1;
            }
            $freespinround = new \VanguardLTE\Notifications;
            $freespinround->message = $request->message;
            $freespinround->campaign = $campaign;
            $freespinround->notify_date = $request->valid_from;
            $freespinround->notify_time = $request->valid_time;
            $freespinround->frequency = $request->frequency_type;
            $freespinround->active = $request->active;
            $freespinround->save();
            return redirect()->route('backend.notifications.list');
        }
    }
    public function edit(\Illuminate\Http\Request $request, $id)
    {
        $notification = \VanguardLTE\Notifications::where('id', $id)->first();
        $frequency_type = $this->frequency_type;
        if ($request->isMethod('get')){
            return view('backend.notifications.edit', compact('notification', 'frequency_type'));
        }
        if ($request->isMethod('post')){
            if(!isset($request->campaign)){
                $campaign = 0;
            }else{
                $campaign = 1;
            }
            $notification->message = $request->message;
            $notification->campaign = $campaign;
            $notification->notify_date = $request->valid_from;
            $notification->notify_time = $request->valid_time;
            $notification->frequency = $request->frequency_type;
            $notification->active = $request->active;
            $notification->save();
            return redirect()->route('backend.notifications.list');
        }
    }
    public function delete(\Illuminate\Http\Request $request, $id)
    {
        $notifications = \VanguardLTE\Notifications::where('id', $id)->delete();
        return redirect()->route('backend.notification.list');
    }
}
