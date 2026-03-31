<?php

namespace App\Http\Controllers;

use App\Events\MessageSent;
use App\Models\Message;
use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MessageController extends Controller
{
    /**
     * List messages for an order (scoped to participants).
     */
    public function index(Request $request, Order $order): JsonResponse
    {
        $userId = $request->user()->id;

        // Allow access if user is order owner, assigned driver, or admin
        if (
            $order->user_id != $userId &&
            $order->driver_id != $userId &&
            $request->user()->role !== 'admin'
        ) {
            return response()->json(['message' => 'Forbidden.'], 403);
        }

        $messages = $order->messages()
            ->with('sender:id,name,role')
            ->orderBy('created_at', 'asc')
            ->get()
            ->map(fn (Message $msg) => [
                'id' => $msg->id,
                'order_id' => $msg->order_id,
                'sender_id' => $msg->sender_id,
                'sender_name' => $msg->sender->name ?? '',
                'sender_role' => $msg->sender->role ?? '',
                'receiver_id' => $msg->receiver_id,
                'message' => $msg->message,
                'read_at' => $msg->read_at,
                'created_at' => $msg->created_at->toISOString(),
            ]);

        // Mark unread messages as read
        $order->messages()
            ->where('receiver_id', $userId)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return response()->json($messages);
    }

    /**
     * Send a message on an order.
     */
    public function store(Request $request, Order $order): JsonResponse
    {
        $userId = $request->user()->id;
        Log::info('MessageController@store called', [
            'userId' => $userId,
            'order_user_id' => $order->user_id,
            'order_driver_id' => $order->driver_id,
            'user_role' => $request->user()->role
        ]);

        // Allow if user is order owner, assigned driver, or admin
        if (
            $order->user_id != $userId &&
            $order->driver_id != $userId &&
            $request->user()->role !== 'admin'
        ) {
            Log::error('MessageStore Forbidden', ['order' => $order->id]);
            return response()->json(['message' => 'Forbidden.'], 403);
        }

        $request->validate([
            'message' => ['required', 'string', 'max:1000'],
            'receiver_id' => ['required', 'integer', 'exists:users,id'],
        ]);

        $message = $order->messages()->create([
            'sender_id' => $userId,
            'receiver_id' => $request->input('receiver_id'),
            'message' => $request->input('message'),
        ]);

        $message->load('sender:id,name,role');

        // Broadcast the message
        event(new MessageSent($message));

        return response()->json([
            'id' => $message->id,
            'order_id' => $message->order_id,
            'sender_id' => $message->sender_id,
            'sender_name' => $message->sender->name ?? '',
            'sender_role' => $message->sender->role ?? '',
            'receiver_id' => $message->receiver_id,
            'message' => $message->message,
            'created_at' => $message->created_at->toISOString(),
        ], 201);
    }
}
