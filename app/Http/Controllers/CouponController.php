<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCouponRequest;
use App\Http\Requests\UpdateCouponRequest;
use App\Http\Resources\CouponResource;
use App\Models\Coupon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class CouponController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = Coupon::query();

        if ($request->has('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('code', 'like', '%' . $request->search . '%')
                  ->orWhere('name', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->boolean('active_only')) {
            $query->where('is_active', true)
                  ->where(function ($q) {
                      $q->whereNull('valid_until')
                        ->orWhere('valid_until', '>=', now());
                  });
        }

        $coupons = $query->latest()->paginate(15);

        return CouponResource::collection($coupons);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCouponRequest $request): CouponResource
    {
        $coupon = Coupon::create($request->validated());

        return new CouponResource($coupon);
    }

    /**
     * Display the specified resource.
     */
    public function show(Coupon $coupon): CouponResource
    {
        return new CouponResource($coupon);
    }

    /**
     * Validate a coupon code.
     */
    public function validate(Request $request): CouponResource
    {
        $request->validate([
            'code' => 'required|string',
            'order_amount' => 'nullable|numeric|min:0',
        ]);

        $coupon = Coupon::where('code', $request->code)
            ->where('is_active', true)
            ->where(function ($query) {
                $query->whereNull('valid_from')
                      ->orWhere('valid_from', '<=', now());
            })
            ->where(function ($query) {
                $query->whereNull('valid_until')
                      ->orWhere('valid_until', '>=', now());
            })
            ->where(function ($query) {
                $query->whereNull('usage_limit')
                      ->orWhereRaw('used_count < usage_limit');
            })
            ->firstOrFail();

        if ($request->order_amount && $coupon->min_order_amount) {
            if ($request->order_amount < $coupon->min_order_amount) {
                abort(422, 'Order amount is below the minimum required for this coupon.');
            }
        }

        return new CouponResource($coupon);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCouponRequest $request, Coupon $coupon): CouponResource
    {
        $coupon->update($request->validated());

        return new CouponResource($coupon);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Coupon $coupon): Response
    {
        $coupon->delete();

        return response()->noContent();
    }
}
