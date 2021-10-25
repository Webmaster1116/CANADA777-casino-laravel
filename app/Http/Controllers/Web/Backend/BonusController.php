<?php

namespace VanguardLTE\Http\Controllers\Web\Backend;

use Illuminate\Http\Request;
use VanguardLTE\Http\Controllers\Controller;

class BonusController extends Controller
{
    //
    public function index(\Illuminate\Http\Request $request)
    {
        $bonus = \VanguardLTE\BonusCondition::get();
        return view('backend.bonus.list', compact('bonus'));
    }
    public function add(\Illuminate\Http\Request $request)
    {
        if ($request->isMethod('get')){
            $types = \VanguardLTE\BonusType::pluck('name', 'type');
            return view('backend.bonus.add', compact('types'));
        }
        if ($request->isMethod('post')){
            $bonus = new \VanguardLTE\BonusCondition;
            $bonus->type = $request->type;
            $bonus->name = $request->name;
            $bonus->valid_from = $request->valid_from;
            $bonus->valid_until = $request->valid_until;
            $bonus->is_mon = $request->is_mon && $request->is_mon == 'on';
            $bonus->is_tue = $request->is_tue && $request->is_tue == 'on';
            $bonus->is_wed = $request->is_wed && $request->is_wed == 'on';
            $bonus->is_thr = $request->is_thr && $request->is_thr == 'on';
            $bonus->is_fri = $request->is_fri && $request->is_fri == 'on';
            $bonus->is_sat = $request->is_sat && $request->is_sat == 'on';
            $bonus->is_sun = $request->is_sun && $request->is_sun == 'on';
            $bonus->deposit_min = $request->deposit_min;
            $bonus->deposit_max = $request->deposit_max;
            $bonus->match_win = $request->match_win;
            $bonus->code = $request->code;
            $bonus->wagering = $request->wagering;
            $bonus->active = $request->active;
            $bonus->save();
            return redirect()->route('backend.bonus.list');
        }
    }
    public function edit(\Illuminate\Http\Request $request, $id)
    {
        $bonus = \VanguardLTE\BonusCondition::where('id', $id)->first();
        if ($request->isMethod('get')){
            $types = \VanguardLTE\BonusType::pluck('name', 'type');
            return view('backend.bonus.edit', compact('types', 'bonus'));
        }
        if ($request->isMethod('post')){
            $bonus->type = $request->type;
            $bonus->name = $request->name;
            $bonus->valid_from = $request->valid_from;
            $bonus->valid_until = $request->valid_until;
            $bonus->is_mon = $request->is_mon && $request->is_mon == 'on';
            $bonus->is_tue = $request->is_tue && $request->is_tue == 'on';
            $bonus->is_wed = $request->is_wed && $request->is_wed == 'on';
            $bonus->is_thr = $request->is_thr && $request->is_thr == 'on';
            $bonus->is_fri = $request->is_fri && $request->is_fri == 'on';
            $bonus->is_sat = $request->is_sat && $request->is_sat == 'on';
            $bonus->is_sun = $request->is_sun && $request->is_sun == 'on';
            $bonus->deposit_min = $request->deposit_min;
            $bonus->deposit_max = $request->deposit_max;
            $bonus->match_win = $request->match_win;
            $bonus->code = $request->code;
            $bonus->wagering = $request->wagering;
            $bonus->active = $request->active;
            $bonus->save();
            return redirect()->route('backend.bonus.list');
        }
    }
    public function delete(\Illuminate\Http\Request $request, $id)
    {
        $bonus = \VanguardLTE\BonusCondition::where('id', $id)->delete();
        return redirect()->route('backend.bonus.list');
    }
}
