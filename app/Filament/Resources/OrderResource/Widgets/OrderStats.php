<?php

namespace App\Filament\Resources\OrderResource\Widgets;

use App\Models\Order;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Number;

class OrderStats extends BaseWidget
{
    protected function getStats(): array
    {
        return [/*
            Stat::make('New Order' , Order::query()->where('status' , 'new')->count()),

            Stat::make('Order Processing' , Order::query()->where('status' , 'processing')->count()),

            Stat::make('Order Shipped' , Order::query()->where('status' , 'shipped')->count()),

            Stat::make('Order Delivered' , Order::query()->where('status' , 'delivered')->count()),

            Stat::make('Order Cancelled' , Order::query()->where('status' , 'cancelled')->count()),

            Stat::make('Average Price' , Number::currency(Order::query()->avg('grand_total') , 'USD'))*/

            Stat::make('New Order', Order::query()->where('status', 'new')->count())
                ->color('info') // Yeni siparişler için mavi renk

                ->description('New orders created')
                ->descriptionIcon('heroicon-o-sparkles'),

            Stat::make('Order Processing', Order::query()->where('status', 'processing')->count())
                ->color('warning') // İşlenmekte olan siparişler için sarı renk
                ->description('Processing orders in progress')
                ->descriptionIcon('heroicon-o-clock'),

            Stat::make('Order Shipped', Order::query()->where('status', 'shipped')->count())
                ->color('success') // Gönderilen siparişler için yeşil renk
                ->description('Orders shipped to customers')
                ->descriptionIcon('heroicon-o-truck'),

            Stat::make('Order Delivered', Order::query()->where('status', 'delivered')->count())
                ->color('success') // Teslim edilen siparişler için yeşil renk
                ->description('Orders successfully delivered')
                ->descriptionIcon('heroicon-o-check-circle'),

            Stat::make('Order Cancelled', Order::query()->where('status', 'cancelled')->count())
                ->color('danger') // İptal edilen siparişler için kırmızı renk
                ->description('Cancelled orders')
                ->descriptionIcon('heroicon-o-x-circle'),

            Stat::make('Average Price', Number::currency(Order::query()->avg('grand_total'), 'USD'))
                ->color('secondary') // Ortalama fiyat için gri renk
                ->description('Average order price')
                ->descriptionIcon('heroicon-o-currency-dollar'),
        ];
    }
}
