<?php

namespace SuiteZap\LawFirm\Observers;

use SuiteZap\LawFirm\Models\Prazo;
use Webkul\Activity\Repositories\ActivityRepository;
use Carbon\Carbon;

class PrazoObserver
{
    /**
     * @var ActivityRepository
     */
    protected $activityRepository;

    /**
     * Create a new observer instance.
     */
    public function __construct(ActivityRepository $activityRepository)
    {
        $this->activityRepository = $activityRepository;
    }

    /**
     * Handle the Prazo "created" event.
     *
     * Cria uma Activity no Calendário quando um Prazo é criado.
     */
    public function created(Prazo $prazo): void
    {
        $processo = $prazo->processo;

        if (!$processo) {
            return;
        }

        // Build title: "PRAZO: {titulo} ({processo->titulo})"
        $title = "PRAZO: {$prazo->titulo} ({$processo->titulo})";

        // Determine user_id: processo->user_id or logged user
        $userId = $processo->user_id ?? auth()->guard('user')->id() ?? 1;

        // Schedule: data_vencimento from 08:00 to 18:00
        $scheduleDate = Carbon::parse($prazo->data_vencimento)->startOfDay();
        $scheduleFrom = $scheduleDate->copy()->setTime(8, 0, 0);
        $scheduleTo = $scheduleDate->copy()->setTime(18, 0, 0);

        // is_done based on status
        $isDone = strtolower(trim($prazo->status)) === 'concluído' ? 1 : 0;

        // Create the Activity
        $activity = $this->activityRepository->create([
            'type' => 'call',
            'title' => $title,
            'comment' => $prazo->descricao ?? '',
            'schedule_from' => $scheduleFrom,
            'schedule_to' => $scheduleTo,
            'is_done' => $isDone,
            'user_id' => $userId,
        ]);

        // Link to Lead if exists (defensive check)
        if ($processo->lead_id) {
            $activity->leads()->syncWithoutDetaching([$processo->lead_id]);
        }

        // Store activity_id in prazo for future updates/deletes
        $prazo->update(['activity_id' => $activity->id]);
    }

    /**
     * Handle the Prazo "updated" event.
     *
     * Atualiza a Activity correspondente quando um Prazo é editado.
     */
    public function updated(Prazo $prazo): void
    {
        $processo = $prazo->processo;

        if (!$processo) {
            return;
        }

        // If no activity_id, create one instead
        if (!$prazo->activity_id) {
            $this->created($prazo);
            return;
        }

        // Build title
        $title = "PRAZO: {$prazo->titulo} ({$processo->titulo})";

        // Determine user_id
        $userId = $processo->user_id ?? auth()->guard('user')->id() ?? 1;

        // Schedule dates
        $scheduleDate = Carbon::parse($prazo->data_vencimento)->startOfDay();
        $scheduleFrom = $scheduleDate->copy()->setTime(8, 0, 0);
        $scheduleTo = $scheduleDate->copy()->setTime(18, 0, 0);

        // is_done based on status
        $isDone = strtolower(trim($prazo->status)) === 'concluído' ? 1 : 0;

        // Update the Activity
        $this->activityRepository->update([
            'type' => 'call',
            'title' => $title,
            'comment' => $prazo->descricao ?? '',
            'schedule_from' => $scheduleFrom,
            'schedule_to' => $scheduleTo,
            'is_done' => $isDone,
            'user_id' => $userId,
        ], $prazo->activity_id);

        // Re-sync Lead link if needed (defensive)
        if ($processo->lead_id) {
            $activity = $this->activityRepository->find($prazo->activity_id);
            if ($activity) {
                $activity->leads()->syncWithoutDetaching([$processo->lead_id]);
            }
        }
    }

    /**
     * Handle the Prazo "deleted" event.
     *
     * Remove a Activity correspondente quando um Prazo é excluído.
     */
    public function deleted(Prazo $prazo): void
    {
        if (!$prazo->activity_id) {
            return;
        }

        // Delete the corresponding Activity
        $this->activityRepository->delete($prazo->activity_id);
    }
}
