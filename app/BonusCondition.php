<?php

namespace VanguardLTE;

use Illuminate\Database\Eloquent\Model;

class BonusCondition extends Model
{
    //
    public function getType(){
        $ret = '';
        $type = $this->belongsTo('VanguardLTE\BonusType', 'type', 'type')->first();
        if ($type)
            $ret = $type->name;
        return $ret;
    } 
    public function getDays(){
        $ret = '';
        if ($this->is_mon)
            $ret = 'Mon';
        if ($this->is_tue)
            $ret = $ret == '' ? 'Tue' : $ret.', Tue';
        if ($this->is_wed)
            $ret = $ret == '' ? 'Wed' : $ret.', Wed';
        if ($this->is_thr)
            $ret = $ret == '' ? 'Thr' : $ret.', Thr';
        if ($this->is_fri)
            $ret = $ret == '' ? 'Fri' : $ret.', Fri';
        if ($this->is_sat)
            $ret = $ret == '' ? 'Sat' : $ret.', Sat';
        if ($this->is_sun)
            $ret = $ret == '' ? 'Sun' : $ret.', Sun';

        return $ret;
    }
    public static function getCondition($date, $amount){
        $ret = null;
        $weekMap = [
            0 => 'is_sun',
            1 => 'is_mon',
            2 => 'is_tue',
            3 => 'is_wed',
            4 => 'is_thr',
            5 => 'is_fri',
            6 => 'is_sat',
        ];
        $weekIndex = \Carbon\Carbon::parse($date)->dayOfWeek;    
        $weekField = $weekMap[$weekIndex];
        $ret = \VanguardLTE\BonusCondition::where([
            ['valid_from', '<=', $date],
            ['valid_until', '>=', $date],
            [$weekField, '=', true],
            ['deposit_min', '<=', $amount],
            ['deposit_max', '>=', $amount],
            ['active', '=', true],
        ])->first();
        return $ret;
    }
}
