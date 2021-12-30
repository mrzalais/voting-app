<?php

namespace App\Http\Livewire;

use App\Models\Comment;
use App\Models\Idea;
use Illuminate\Notifications\DatabaseNotification;
use Livewire\Component;
use Symfony\Component\HttpFoundation\Response;

class CommentNotifications extends Component
{
    public const NOTIFICATION_THRESHOLD = 3;

    public $notifications;
    public $notificationCount;
    public bool $isLoading;

    protected $listeners = ['getNotifications'];

    public function mount()
    {
        $this->notifications = collect([]);
        $this->isLoading = true;
        $this->getNotificationCount();
    }

    public function getNotificationCount(): void
    {
        $this->notificationCount = auth()->user()->unreadNotifications()->count();

        if ($this->notificationCount > self::NOTIFICATION_THRESHOLD) {
            $this->notificationCount = self::NOTIFICATION_THRESHOLD . '+';
        }
    }

    public function getNotifications()
    {
        $this->notifications = auth()->user()
            ->unreadNotifications()
            ->latest()
            ->take(self::NOTIFICATION_THRESHOLD)
            ->get();

        $this->isLoading = false;
    }

    public function markAsRead($notificationId)
    {
        abort_if(auth()->guest(), Response::HTTP_FORBIDDEN);

        $notification = DatabaseNotification::findOrFail($notificationId);
        $notification->markAsRead();

        $idea = Idea::find($notification->data['idea_id']);
        $comment = Comment::find($notification->data['comment_id']);

        $comments = $idea->comments;

        session()->flash('scrollToComment', $comment->id);

        return redirect()->route('idea.show', [
           'idea' => $notification->data['idea_slug'],
        ]);
    }

    public function markAllAsRead(): void
    {
        abort_if(auth()->guest(), Response::HTTP_FORBIDDEN);

        auth()->user()->unreadNotifications->markAsRead();
        $this->getNotificationCount();
        $this->getNotifications();
    }

    public function render()
    {
        return view('livewire.comment-notifications');
    }
}
