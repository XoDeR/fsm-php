<?php

namespace App;

use DateTimeImmutable;

enum ArticleState
{
    case Draft;
    case InReview;
    case Scheduled;
    case Published;
    case Archived;
}

enum ArticleEvent
{
    case Submit;
    case Approve;
    case Schedule;
    case Publish;
    case Update;
    case Archive;
}

final class ArticleContext
{
    public function __construct(
        public int $authorId,
        public ?DateTimeImmutable $publishAt,
        public int $currentUserId,
        public bool $hasRequiredMeta,
    ) {
    }
}

// Guard -- boolean rule that must be true for the transition to happen

// Draft --Submit --> InReview (guard: hasRequiredMeta)
// InReview --Approve --> Scheduled (guard: publishAt in future)
// Scheduled --Publish --> Published (guard: now >= publishAt)
// Draft/InReview --Update --> Draft (guard: currentUserId === authorId)
// Published --Archive --> Archived (terminal state)
