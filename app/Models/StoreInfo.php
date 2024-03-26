<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Arr;

class StoreInfo extends BaseModel
{
    use HasFactory;

    /**
     * @return array
     */
    public function toESArray(): array
    {
        // 只取出需要的字段
        $arr = Arr::only($this->toArray(), [
            'id',
            'name',
            'address',
            'location',
        ]);

        $location = explode(',', $arr['location']);
        $arr['location'] = ['lon' => $location[1], 'lat'=> $location[0]];

        return $arr;
    }
}
