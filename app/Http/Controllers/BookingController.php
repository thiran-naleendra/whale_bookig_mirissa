<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\MonthPrice;
use App\Models\DisabledDate;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BookingController extends Controller
{
    public function form()
    {
        return view('booking.form');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'tour_date' => 'required|date',
            'name' => 'required|string|max:100',
            'email' => 'required|email|max:150',
            'mobile' => 'required|string|max:20',
            'pickup_hotel' => 'nullable|string|max:255',
            'payment_option' => 'required|string|max:20',
            'adults_qty' => 'required|integer|min:0',
            'children_qty' => 'required|integer|min:0',
            'kids_qty' => 'required|integer|min:0',
            'special_requests' => 'nullable|string',
        ]);

        $tourDate = $data['tour_date'];

        // ✅ Block disabled specific dates
        if (DisabledDate::where('date', $tourDate)->exists()) {
            return back()->with('error', "Selected date is not available for booking.");
        }

        $year  = (int)date('Y', strtotime($tourDate));
        $month = (int)date('n', strtotime($tourDate));

        // ✅ Find month price and check month enabled
        $price = MonthPrice::where('year', $year)->where('month', $month)->first();

        if (!$price) {
            return back()->with('error', "Prices not set for $year-$month. Add month price in admin or phpMyAdmin.");
        }

        // month_prices must have is_enabled column (1/0)
        if (isset($price->is_enabled) && (int)$price->is_enabled === 0) {
            return back()->with('error', "Bookings are closed for $year-$month.");
        }

        $adultTotal = (int)$data['adults_qty'] * (float)$price->adult_price;
        $childTotal = (int)$data['children_qty'] * (float)$price->child_price;

        $subtotal = $adultTotal + $childTotal; // kids free
        $discount = 0;
        $total = $subtotal - $discount;

        $orderId = 'TOUR-' . date('Ymd') . '-' . strtoupper(Str::random(6));

        $booking = Booking::create([
            'order_id' => $orderId,
            'tour_date' => $tourDate,
            'name' => $data['name'],
            'email' => $data['email'],
            'mobile' => $data['mobile'],
            'pickup_hotel' => $data['pickup_hotel'] ?? null,
            'payment_option' => $data['payment_option'],
            'adults_qty' => $data['adults_qty'],
            'children_qty' => $data['children_qty'],
            'kids_qty' => $data['kids_qty'],
            'special_requests' => $data['special_requests'] ?? null,
            'subtotal' => $subtotal,
            'discount' => $discount,
            'total' => $total,
            'status' => 'PENDING',
        ]);

        return view('booking.payhere_redirect', compact('booking'));
    }

    public function success()
    {
        return view('booking.success');
    }

    public function cancel()
    {
        return view('booking.cancel');
    }

    public function getMonthPrice(Request $request)
    {
        $date = $request->query('date');

        if (!$date) {
            return response()->json(['available' => false, 'message' => 'Date required'], 400);
        }

        // ✅ Block disabled specific dates
        if (DisabledDate::where('date', $date)->exists()) {
            return response()->json([
                'available' => false,
                'message' => 'Selected date is not available for booking'
            ]);
        }

        $year  = (int)date('Y', strtotime($date));
        $month = (int)date('n', strtotime($date));

        $price = MonthPrice::where('year', $year)
            ->where('month', $month)
            ->first();

        if (!$price) {
            return response()->json([
                'available' => false,
                'message' => "Prices not set for $year-$month"
            ]);
        }

        if (isset($price->is_enabled) && (int)$price->is_enabled === 0) {
            return response()->json([
                'available' => false,
                'message' => "Bookings are closed for $year-$month"
            ]);
        }

        return response()->json([
            'available' => true,
            'adult_price' => (float)$price->adult_price,
            'child_price' => (float)$price->child_price,
        ]);
    }
}
