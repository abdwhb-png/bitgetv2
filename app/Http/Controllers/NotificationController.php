<?php

namespace App\Http\Controllers;

use App\NotifData;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Notifications\DefaultNotif;

class NotificationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $unread = request()->user()->unreadNotifications()
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($notification) {
                return array_merge($notification->toArray(), [
                    'created' => \Carbon\Carbon::parse($notification->created_at)->diffForHumans(),
                ]);
            });

        $readed = DB::table('notifications')->whereNotNull('read_at')
            ->orderBy('created_at', 'desc')
            ->where('notifiable_id', request()->user()->id)
            ->get()
            ->map(function ($notification) {
                $notification->data = json_decode($notification->data);
                $notification->created = \Carbon\Carbon::parse($notification->created_at)->diffForHumans();
                return $notification;
            });

        return compact('unread', 'readed');
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'title' => 'required|string',
            'body' => 'nullable|string',
        ]);

        $user = User::find($request->user_id);

        $via = $request->via ?? ['mail', 'database', 'broadcast'];

        $notifData = new NotifData($request->title, $request->body ?? '');

        $user->notify(new DefaultNotif($notifData, $via));

        return [
            'success' => true,
            'message' => 'Notification sent successfully',
            'transc' => \App\Models\Transaction::first(),
        ];
    }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return request()->user()->notifications()->where('id', $id)->first();
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }


    /**
     * Update the specified resource in storage.
     */
    public function update($notification, Request $request)
    {
        $request->user()->notifications()->where('id', $notification)->update(['read_at' => now()]);
        return back(303)->with('status', 'notification marked as read');
    }


    public function markAllAsRead(Request $request)
    {
        $request->user()->unreadNotifications->markAsRead();
        return back(303)->with('status', 'all notifications marked as read');
    }

    public function deleteAll(Request $request)
    {
        request()->user()->notifications()->delete();
        return back(303)->with('status', 'all notifications deleted');
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy($notification)
    {
        $notification->delete();

        return back(303)->with('status', 'notification deleted');
    }
}