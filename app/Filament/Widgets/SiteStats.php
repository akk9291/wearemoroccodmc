<?php

namespace App\Filament\Widgets;

use App\Models\ContactMessage;
use App\Models\Itinerary;
use App\Models\NewsletterSubscriber;
use App\Models\QuoteRequest;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class SiteStats extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $user = auth()->user();
        $leads = $user->hasAnyRole(['Admin', 'Sales']);
        $content = $user->hasAnyRole(['Admin', 'Editor']);

        return array_values(array_filter([
            $leads ? Stat::make('New quote requests this week', QuoteRequest::where('created_at', '>=', now()->startOfWeek())->count())
                ->description(QuoteRequest::where('status', 'new')->count().' still marked new')->icon('heroicon-o-inbox-arrow-down') : null,
            $leads ? Stat::make('Total leads', QuoteRequest::count() + ContactMessage::count())
                ->description(QuoteRequest::count().' quotes · '.ContactMessage::count().' messages')->icon('heroicon-o-users') : null,
            $content ? Stat::make('Published itineraries', Itinerary::published()->count())
                ->description(Itinerary::where('status', 'draft')->count().' drafts')->icon('heroicon-o-map') : null,
            $leads ? Stat::make('Newsletter subscribers', NewsletterSubscriber::count())
                ->description(NewsletterSubscriber::where('created_at', '>=', now()->subDays(30))->count().' in the last 30 days')->icon('heroicon-o-at-symbol') : null,
        ]));
    }
}
