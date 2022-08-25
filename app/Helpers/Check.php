<?php

namespace App\Helpers;

use App\Models\Configuration;
use App\Models\Item;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class Check
{
    public static function getCodeItem()
    {
        $item = Item::all()->max('code_item');
        if ($item == null) {
            $getCodeItem = 001;
        } else {
            $getCodeItem = substr($item, 0, 3);
            $int = (int) $getCodeItem;
            $int++;
            $getCodeItem = sprintf("%03s", $int);
        }
        return $getCodeItem;
    }
    public static function configuration()
    {
        $configuration = Configuration::first();
        return $configuration;
    }
    public static function getProfile($id = null)
    {
        $getProfile = User::join('profile', 'profile.users_id', '=', 'users.id');
        $getId = Auth::id();
        if ($id != null) {
            $getId = $id;
        }
        $getProfile = $getProfile->where('users.id', $getId);
        return $getProfile->first();
    }

    public static function getBulanLaporan($nameMonth, $value)
    {
        $data = [];
        switch ($nameMonth) {
            case '01':
                $data['01'] = $value;
                break;
            case '02':
                $data['02'] = $value;
                break;
            case '03':
                $data['03'] = $value;
                break;
            case '04':
                $data['04'] = $value;
                break;
            case '05':
                $data['05'] = $value;
                break;
            case '06':
                $data['06'] = $value;
                break;
            case '07':
                $data['07'] = $value;
                break;
            case '08':
                $data['08'] = $value;
                break;
            case '09':
                $data['09'] = $value;
                break;
            case '10':
                $data['10'] = $value;
                break;
            case '11':
                $data['11'] = $value;
                break;
            case '12':
                $data['12'] = $value;
                break;
        }
        return $data;
    }
}
