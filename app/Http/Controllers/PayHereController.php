<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class PayHereController extends Controller
{
    private function makeTicketNo(string $orderId): string
    {
        return 'TKT-' . date('Ymd') . '-' . substr(md5($orderId), 0, 6);
    }

    private function verifyPayHereMd5Sig(Request $request): bool
    {
        $merchantId = $request->input('merchant_id');
        $orderId = $request->input('order_id');
        $payhereAmount = $request->input('payhere_amount');
        $payhereCurrency = $request->input('payhere_currency');
        $statusCode = $request->input('status_code');
        $md5sig = $request->input('md5sig');

        $merchantSecret = env('PAYHERE_MERCHANT_SECRET');

        if (!$merchantId || !$orderId || !$payhereAmount || !$payhereCurrency || $statusCode === null || !$md5sig || !$merchantSecret) {
            return false;
        }

        $local = strtoupper(md5(
            $merchantId .
                $orderId .
                $payhereAmount .
                $payhereCurrency .
                $statusCode .
                strtoupper(md5($merchantSecret))
        ));

        return $local === $md5sig;
    }

    public function notify(Request $request)
    {

        \Log::info('PAYHERE NOTIFY HIT', $request->all());
        $orderId = $request->input('order_id');
        if (!$orderId) {
            return response('Missing order_id', 400);
        }

        $booking = Booking::where('order_id', $orderId)->first();
        if (!$booking) {
            return response('Booking not found', 404);
        }

        // Save PayHere details (even if failed)
        $booking->payhere_payment_id = $request->input('payment_id');
        $booking->payhere_status_code = (int)$request->input('status_code');
        $booking->payhere_status_message = $request->input('status_message');
        $booking->payhere_method = $request->input('method');

        // Extra useful fields
        $booking->payhere_amount = $request->input('payhere_amount');       // PayHere final amount
        $booking->payhere_currency = $request->input('payhere_currency');   // LKR
        $booking->payhere_md5sig = $request->input('md5sig');

        // Optional card info (may be null)
        $booking->payhere_card_no = $request->input('card_no');
        $booking->payhere_card_holder_name = $request->input('card_holder_name');

        // ✅ Verify signature before confirming
        $sigOk = $this->verifyPayHereMd5Sig($request);

        if (!$sigOk) {
            // Do NOT confirm if signature invalid
            Log::error('PayHere signature mismatch', [
                'order_id' => $orderId,
                'received' => $request->all(),
            ]);

            // keep status as PENDING or mark FAILED (your choice)
            $booking->status = 'FAILED';
            $booking->save();

            return response('Invalid signature', 400);
        }

        // Status code
        $statusCode = (int)$request->input('status_code');

        if ($statusCode === 2) {
            // ✅ SUCCESS
            $booking->status = 'CONFIRMED';

            if (!$booking->ticket_no) {
                $booking->ticket_no = $this->makeTicketNo($booking->order_id);
            }

            $booking->save();

            // Send ticket email once
            // Send ticket email once
            if (!$booking->ticket_sent_at) {

                $emailBody =
                    "Your booking is CONFIRMED ✅

Ticket No: {$booking->ticket_no}
Order ID: {$booking->order_id}
Tour Date: {$booking->tour_date}

Name: {$booking->name}
Mobile: {$booking->mobile}
Pickup: " . ($booking->pickup_hotel ?? 'N/A') . "

Adults: {$booking->adults_qty}
Children: {$booking->children_qty}
Kids: {$booking->kids_qty}

Paid Amount (PayHere): {$booking->payhere_amount} {$booking->payhere_currency}
Payment ID: {$booking->payhere_payment_id}
Method: " . ($booking->payhere_method ?? '—') . "

Thank you.";

                try {

                    Mail::raw($emailBody, function ($message) use ($booking) {
                        $message->to($booking->email)
                            ->from(config('mail.from.address'), config('mail.from.name')) // ✅ important
                            ->subject("Your Ticket - {$booking->ticket_no}");
                    });

                    // ✅ only set sent time if mail send did not throw error
                    $booking->ticket_sent_at = now();
                    $booking->save();

                    \Log::info('TICKET EMAIL SENT', [
                        'order_id' => $booking->order_id,
                        'to' => $booking->email
                    ]);
                } catch (\Throwable $e) {

                    \Log::error('TICKET EMAIL FAILED', [
                        'order_id' => $booking->order_id,
                        'to' => $booking->email,
                        'error' => $e->getMessage()
                    ]);

                    // keep ticket_sent_at NULL if failed
                }
            }

            return response('OK', 200);
        }

        // ❌ Not success
        // You can separate CANCELLED vs FAILED if you want.
        $booking->status = 'FAILED';
        $booking->save();

        return response('OK', 200);
    }
}
