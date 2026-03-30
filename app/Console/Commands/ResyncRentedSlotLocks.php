<?php

namespace App\Console\Commands;

use App\Http\Controllers\Traits\RentAndPassTrait;
use App\Models\RentedSlot;
use Carbon\Carbon;
use Illuminate\Console\Command;

class ResyncRentedSlotLocks extends Command
{
    use RentAndPassTrait;

    protected $signature = 'rented-slots:resync-locks {--from= : Re-sync reservations ending at or after this local datetime (Y-m-d H:i:s)} {--id=* : Specific rented slot IDs to sync}';

    protected $description = 'Re-sync future rented slot schedules with the lock provider using the current timezone rules.';

    public function handle(): int
    {
        $ids = array_filter((array) $this->option('id'));
        $from = $this->option('from')
            ? Carbon::createFromFormat('Y-m-d H:i:s', $this->option('from'), config('app.timezone'))
            : Carbon::now(config('app.timezone'));

        $query = RentedSlot::query()->orderBy('start_date_time');

        if (!empty($ids)) {
            $query->whereIn('id', $ids);
        } else {
            $query->where('end_date_time', '>=', $from->format('Y-m-d H:i:s'));
        }

        $slots = $query->get();

        if ($slots->isEmpty()) {
            $this->info('No rented slots matched the requested filter.');
            return self::SUCCESS;
        }

        $this->info(sprintf('Syncing %d rented slots...', $slots->count()));

        foreach ($slots as $slot) {
            $this->syncRentedSlotKeycode($slot);
            $this->line(sprintf(
                '#%d %s -> %s',
                $slot->id,
                $slot->start_date_time,
                $slot->end_date_time
            ));
        }

        $this->info('Lock sync completed.');

        return self::SUCCESS;
    }
}
