<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\MonthPrice;
use App\Models\DisabledDate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class AdminBookingController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->query('status');
        $date = $request->query('date');
        $q = $request->query('q');

        $query = Booking::orderBy('id', 'desc');

        if ($status) $query->where('status', $status);
        if ($date) $query->where('tour_date', $date);

        if ($q) {
            $query->where(function ($sub) use ($q) {
                $sub->where('order_id', 'like', "%$q%")
                    ->orWhere('email', 'like', "%$q%")
                    ->orWhere('mobile', 'like', "%$q%")
                    ->orWhere('name', 'like', "%$q%");
            });
        }

        $bookings = $query->paginate(20)->appends($request->query());

        return view('admin.bookings', compact('bookings', 'status', 'date', 'q'));
    }

    public function show($id)
    {
        $booking = Booking::findOrFail($id);
        return view('admin.booking_show', compact('booking'));
    }

    public function resendTicket($id)
    {
        $booking = Booking::findOrFail($id);

        if ($booking->status !== 'CONFIRMED') {
            return back()->with('error', 'Only CONFIRMED bookings can send tickets.');
        }

        if (!$booking->ticket_no) {
            $booking->ticket_no = 'TKT-' . date('Ymd') . '-' . substr(md5($booking->order_id), 0, 6);
        }

        $emailBody =
"Your booking ticket ✅

Ticket No: {$booking->ticket_no}
Order ID: {$booking->order_id}
Tour Date: {$booking->tour_date}

Name: {$booking->name}
Mobile: {$booking->mobile}
Pickup: " . ($booking->pickup_hotel ?? 'N/A') . "

Adults: {$booking->adults_qty}
Children: {$booking->children_qty}
Kids: {$booking->kids_qty}

Paid Amount: " . ($booking->payhere_amount ?? $booking->total) . " " . ($booking->payhere_currency ?? 'LKR') . "
Payment ID: " . ($booking->payhere_payment_id ?? '—') . "
Method: " . ($booking->payhere_method ?? '—') . "
";

        Mail::raw($emailBody, function ($message) use ($booking) {
            $message->to($booking->email)
                ->subject("Your Ticket - {$booking->ticket_no} (Resent)");
        });

        $booking->ticket_sent_at = now();
        $booking->save();

        return back()->with('success', 'Ticket email resent successfully.');
    }

    // ✅ Monthly pricing UI
    public function prices(Request $request)
    {
        $year = (int)($request->query('year', date('Y')));
        $prices = MonthPrice::where('year', $year)->get()->keyBy('month');

        return view('admin.prices', compact('year', 'prices'));
    }

    // ✅ FIXED: correctly saves is_enabled
    public function savePrice(Request $request)
    {
        $data = $request->validate([
            'year' => 'required|integer',
            'month' => 'required|integer|min:1|max:12',
            'adult_price' => 'required|numeric|min:0',
            'child_price' => 'required|numeric|min:0',
            'is_enabled' => 'required|in:0,1',
        ]);

        MonthPrice::updateOrCreate(
            ['year' => (int)$data['year'], 'month' => (int)$data['month']],
            [
                'adult_price' => (float)$data['adult_price'],
                'child_price' => (float)$data['child_price'],
                'is_enabled'  => (int)$data['is_enabled'],
            ]
        );

        return back()->with('success', 'Monthly price saved.');
    }

    public function reports(Request $request)
    {
        $from = $request->query('from', date('Y-m-01'));
        $to   = $request->query('to', date('Y-m-d'));

        $summary = Booking::selectRaw("
            COUNT(*) as total_bookings,
            SUM(CASE WHEN status='CONFIRMED' THEN 1 ELSE 0 END) as confirmed_count,
            SUM(CASE WHEN status='PENDING' THEN 1 ELSE 0 END) as pending_count,
            SUM(CASE WHEN status='FAILED' THEN 1 ELSE 0 END) as failed_count,
            SUM(CASE WHEN status='CANCELLED' THEN 1 ELSE 0 END) as cancelled_count,
            SUM(CASE WHEN status='REFUNDED' THEN 1 ELSE 0 END) as refunded_count,
            SUM(CASE WHEN status='CONFIRMED' THEN total ELSE 0 END) as confirmed_revenue
        ")
        ->whereBetween('tour_date', [$from, $to])
        ->first();

        $byDate = Booking::selectRaw("tour_date, COUNT(*) as cnt, SUM(total) as revenue")
            ->where('status', 'CONFIRMED')
            ->whereBetween('tour_date', [$from, $to])
            ->groupBy('tour_date')
            ->orderBy('tour_date', 'desc')
            ->get();

        $byMethod = Booking::selectRaw("COALESCE(payhere_method,'Unknown') as method, COUNT(*) as cnt, SUM(total) as revenue")
            ->where('status', 'CONFIRMED')
            ->whereBetween('tour_date', [$from, $to])
            ->groupBy('method')
            ->orderBy('revenue', 'desc')
            ->get();

        return view('admin.reports', compact('from', 'to', 'summary', 'byDate', 'byMethod'));
    }

    // ✅ Disabled dates UI
    public function disabledDates()
    {
        $items = DisabledDate::orderBy('date', 'desc')->get();
        return view('admin.disabled_dates', compact('items'));
    }

    public function addDisabledDate(Request $request)
    {
        $data = $request->validate([
            'date' => 'required|date',
            'note' => 'nullable|string|max:255',
        ]);

        DisabledDate::updateOrCreate(
            ['date' => $data['date']],
            ['note' => $data['note'] ?? null]
        );

        return back()->with('success', 'Date disabled');
    }

    public function deleteDisabledDate($id)
    {
        DisabledDate::where('id', $id)->delete();
        return back()->with('success', 'Deleted');
    }
}
