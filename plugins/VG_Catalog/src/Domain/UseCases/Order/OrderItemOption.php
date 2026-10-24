<?php

namespace VG_Catalog\Domain\UseCases\Order;

use Illuminate\Database\Eloquent\Model;
use VG_Catalog\Domain\UseCases\Option\Option;

class OrderItemOption extends Model
{
    protected $primaryKey = 'id';
    public $timestamps = true;
    protected $table = 'vg_order_item_options';

    protected $fillable = [
        'order_item_id',
        'option_id'
    ];

    public function option()
    {
        return $this->belongsTo(Option::class, ownerKey: 'post_id');
    }
}