<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * Obtener notificaciones del usuario actual
     */
    public function index()
    {
        $notifications = auth('admin')->user()
            ->notifications()
            ->take(10)
            ->get()
            ->map(function ($notification) {
                return [
                    'id' => $notification->id,
                    'type' => $notification->data['type'] ?? 'general',
                    'title' => $notification->data['title'] ?? 'Notificación',
                    'message' => $notification->data['message'] ?? '',
                    'icon' => $notification->data['icon'] ?? 'fa-bell',
                    'color' => $notification->data['color'] ?? 'primary',
                    'url' => $notification->data['url'] ?? '#',
                    'read' => !is_null($notification->read_at),
                    'created_at' => $notification->created_at->diffForHumans(),
                ];
            });

        return response()->json([
            'notifications' => $notifications,
            'unread_count' => auth('admin')->user()->unreadNotifications->count()
        ]);
    }

    /**
     * Obtener solo notificaciones no leídas
     */
    public function unread()
    {
        $notifications = auth('admin')->user()
            ->unreadNotifications()
            ->take(5)
            ->get()
            ->map(function ($notification) {
                return [
                    'id' => $notification->id,
                    'type' => $notification->data['type'] ?? 'general',
                    'title' => $notification->data['title'] ?? 'Notificación',
                    'message' => $notification->data['message'] ?? '',
                    'icon' => $notification->data['icon'] ?? 'fa-bell',
                    'color' => $notification->data['color'] ?? 'primary',
                    'url' => $notification->data['url'] ?? '#',
                    'created_at' => $notification->created_at->diffForHumans(),
                ];
            });

        return response()->json([
            'notifications' => $notifications,
            'unread_count' => auth('admin')->user()->unreadNotifications->count()
        ]);
    }

    /**
     * Marcar notificación como leída
     */
    public function markAsRead($id)
    {
        $notification = auth('admin')->user()
            ->notifications()
            ->where('id', $id)
            ->first();

        if ($notification) {
            $notification->markAsRead();
            
            return response()->json([
                'success' => true,
                'message' => 'Notificación marcada como leída',
                'unread_count' => auth('admin')->user()->unreadNotifications->count()
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Notificación no encontrada'
        ], 404);
    }

    /**
     * Marcar todas como leídas
     */
    public function markAllAsRead()
    {
        auth('admin')->user()->unreadNotifications->markAsRead();

        return response()->json([
            'success' => true,
            'message' => 'Todas las notificaciones marcadas como leídas',
            'unread_count' => 0
        ]);
    }

    /**
     * Eliminar notificación
     */
    public function delete($id)
    {
        $notification = auth('admin')->user()
            ->notifications()
            ->where('id', $id)
            ->first();

        if ($notification) {
            $notification->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Notificación eliminada',
                'unread_count' => auth('admin')->user()->unreadNotifications->count()
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Notificación no encontrada'
        ], 404);
    }
}
