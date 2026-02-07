<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAddressRequest;
use App\Http\Requests\UpdateAddressRequest;
use App\Http\Resources\AddressResource;
use App\Models\Address;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class AddressController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $addresses = Address::query()
            ->where('user_id', $request->user()->id)
            ->latest()
            ->paginate(15);

        return AddressResource::collection($addresses);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreAddressRequest $request): AddressResource
    {
        $address = Address::create([
            ...$request->validated(),
            'user_id' => $request->user()->id,
        ]);

        return new AddressResource($address);
    }

    /**
     * Display the specified resource.
     */
    public function show(Address $address): AddressResource
    {
        $this->authorize('view', $address);
        
        return new AddressResource($address);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAddressRequest $request, Address $address): AddressResource
    {
        $this->authorize('update', $address);

        $address->update($request->validated());

        return new AddressResource($address);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Address $address): Response
    {
        $this->authorize('delete', $address);

        $address->delete();

        return response()->noContent();
    }
}
