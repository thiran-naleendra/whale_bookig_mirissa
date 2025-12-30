<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    protected $table = 'bookings';

   protected $fillable = [
  'order_id','tour_date','name','email','mobile','pickup_hotel','payment_option',
  'adults_qty','children_qty','kids_qty','special_requests',
  'subtotal','discount','total','status',
  'payhere_payment_id','payhere_amount','payhere_currency',
  'payhere_status_code','payhere_status_message','payhere_method',
  'payhere_md5sig','payhere_card_no','payhere_card_holder_name',
  'ticket_no','ticket_sent_at'
];
}
