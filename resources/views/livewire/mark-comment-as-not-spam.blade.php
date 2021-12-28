<x-modal-confirm
    livewireEventToOpenModal="markAsNotSpamCommentWasSet"
    eventToCloseModal="commentWasMarkedAsNotSpam"
    modalTitle="Reset Comment Spam Counter"
    modalDescription="Are you sure you want to mark this comment as NOT spam? This will reset the spam counter to 0."
    modalAction="Reset Spam Counter"
    wireClick="markAsNotSpam"
/>
