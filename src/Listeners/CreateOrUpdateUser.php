<?php

namespace Module\System\Listeners;

use Illuminate\Events\Dispatcher;
use Module\System\Models\SystemUser;
use Module\Procurement\Events\ProcurementBiodataCreated;

class CreateOrUpdateUser
{
    /**
     * handleUserFromProcurement function
     *
     * @param ProcurementBiodataCreated $event
     * @return void
     */
    public function handleUserFromProcurement(ProcurementBiodataCreated $event): void
    {
        /** GET CURRENT MODEL */
        $source = $event->model;

        SystemUser::handleUserFromProcurement($source);
    }

    /**
     * subscribe function
     *
     * @param Dispatcher $events
     * @return void
     */
    public function subscribe(Dispatcher $events): void
    {
        $events->listen(
            ProcurementBiodataCreated::class,
            [CreateOrUpdateUser::class, 'handleUserFromProcurement']
        );
    }
}
