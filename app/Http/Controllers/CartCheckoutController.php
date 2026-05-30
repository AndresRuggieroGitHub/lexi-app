<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class CartCheckoutController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        if (! Schema::hasTable('plans') || ! Schema::hasTable('subscriptions') || ! Schema::hasTable('payments')) {
            return response()->json([
                'message' => 'Billing no está inicializado en esta base de datos.',
            ], 503);
        }

        $validated = $request->validate([
            'items' => ['required', 'array', 'min:1'],
            'items.*.id' => ['required', 'string', 'max:120'],
            'items.*.name' => ['required', 'string', 'max:255'],
            'items.*.price' => ['required', 'numeric', 'min:0'],
            'items.*.qty' => ['required', 'integer', 'min:1', 'max:50'],
        ]);

        $now = now();
        $userId = (int) $request->user()->id;
        $checkoutRef = 'checkout-' . Str::ulid();

        $summary = DB::transaction(function () use ($validated, $now, $userId, $checkoutRef): array {
            $paymentsCount = 0;
            $totalCents = 0;

            foreach ($validated['items'] as $index => $item) {
                $priceCents = (int) round(((float) $item['price']) * 100);
                $qty = (int) $item['qty'];

                if ($priceCents < 0) {
                    $priceCents = 0;
                }

                $code = Str::slug((string) $item['id']);
                if ($code === '') {
                    $code = 'item-' . Str::slug((string) $item['name']);
                }
                if ($code === '') {
                    $code = 'item-' . $index;
                }

                $name = trim((string) $item['name']);
                $isAnnual = Str::contains(Str::lower($name . ' ' . (string) $item['id']), ['anual', 'annual', 'year']);
                $billingInterval = $isAnnual ? 'annual' : 'monthly';

                $plan = DB::table('plans')->where('code', $code)->first();

                if (! $plan) {
                    $planId = DB::table('plans')->insertGetId([
                        'code' => $code,
                        'name' => $name,
                        'price_cents' => $priceCents,
                        'currency' => 'EUR',
                        'billing_interval' => $billingInterval,
                        'features' => null,
                        'is_active' => true,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]);
                } else {
                    $planId = (int) $plan->id;

                    DB::table('plans')
                        ->where('id', $planId)
                        ->update([
                            'name' => $name,
                            'price_cents' => $priceCents,
                            'billing_interval' => $billingInterval,
                            'is_active' => true,
                            'updated_at' => $now,
                        ]);
                }

                $startsAt = $now->copy();
                $renewsAt = $billingInterval === 'annual'
                    ? $now->copy()->addYear()
                    : $now->copy()->addMonth();

                $externalReference = $checkoutRef . '-' . ($index + 1);

                $subscriptionId = DB::table('subscriptions')->insertGetId([
                    'user_id' => $userId,
                    'plan_id' => $planId,
                    'status' => 'active',
                    'provider' => 'simulated-checkout',
                    'external_reference' => $externalReference,
                    'quantity' => $qty,
                    'trial_ends_at' => null,
                    'starts_at' => $startsAt,
                    'renews_at' => $renewsAt,
                    'ends_at' => null,
                    'canceled_at' => null,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);

                $paymentAmount = $priceCents * $qty;

                DB::table('payments')->insert([
                    'subscription_id' => $subscriptionId,
                    'provider' => 'simulated-checkout',
                    'provider_payment_id' => $externalReference,
                    'amount_cents' => $paymentAmount,
                    'currency' => 'EUR',
                    'status' => 'paid',
                    'paid_at' => $now,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);

                $paymentsCount++;
                $totalCents += $paymentAmount;
            }

            return [
                'payments_count' => $paymentsCount,
                'total_cents' => $totalCents,
                'checkout_reference' => $checkoutRef,
            ];
        });

        return response()->json([
            'message' => 'Compra registrada correctamente.',
            'summary' => $summary,
        ]);
    }
}
