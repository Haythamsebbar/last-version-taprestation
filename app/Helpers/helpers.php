<?php

use Carbon\Carbon;
use App\Models\Prestataire;
use App\Models\Booking;

if (!function_exists('generate_time_slots')) {
    function generate_time_slots(Prestataire $prestataire, Carbon $startDate, Carbon $endDate)
    {
        $slots = [];
        $availabilities = $prestataire->availabilities()->where('is_active', true)->get();
        $bookings = $prestataire->bookings()->whereIn('status', ['confirmed', 'pending'])->where('start_datetime', '<=', $endDate->endOfDay())->where('end_datetime', '>=', $startDate->startOfDay())->get();

        for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
            // Utiliser dayOfWeek (0=dimanche, 1=lundi, etc.) au lieu de dayOfWeekIso
            $dayOfWeek = $date->dayOfWeek;
            $availability = $availabilities->firstWhere('day_of_week', $dayOfWeek);

            if ($availability) {
                $sTime = Carbon::parse($availability->start_time);
                $eTime = Carbon::parse($availability->end_time);
                $startTime = $date->copy()->hour($sTime->hour)->minute($sTime->minute)->second($sTime->second);
                $endTime = $date->copy()->hour($eTime->hour)->minute($eTime->minute)->second($eTime->second);
                $slotDuration = $availability->slot_duration;

                for ($slotTime = $startTime->copy(); $slotTime->lt($endTime); $slotTime->addMinutes($slotDuration)) {
                    $slotEnd = $slotTime->copy()->addMinutes($slotDuration);

                    $bookedBooking = $bookings->first(function ($booking) use ($slotTime, $slotEnd) {
                        // Check if the slot overlaps with a booking
                        // Handle case where booking has same start and end time (duration 0)
                        if ($booking->start_datetime == $booking->end_datetime) {
                            return $booking->start_datetime >= $slotTime && $booking->start_datetime < $slotEnd;
                        }
                        return ($booking->start_datetime < $slotEnd) && ($booking->end_datetime > $slotTime);
                    });

                    $isBreak = false;
                    if ($availability->break_start_time && $availability->break_end_time) {
                        $breakStartTime = Carbon::parse($availability->break_start_time);
                        $breakEndTime = Carbon::parse($availability->break_end_time);
                        $breakStart = $date->copy()->hour($breakStartTime->hour)->minute($breakStartTime->minute)->second($breakStartTime->second);
                        $breakEnd = $date->copy()->hour($breakEndTime->hour)->minute($breakEndTime->minute)->second($breakEndTime->second);

                        // Check if the slot overlaps with a break
                        if (($slotTime < $breakEnd) && ($slotEnd > $breakStart)) {
                            $isBreak = true;
                        }
                    }

                    // Include all slots with their status
                    if (!$isBreak) {
                        $slots[] = [
                            'datetime' => $slotTime->copy(),
                            'is_booked' => (bool) $bookedBooking,
                            'booking_status' => $bookedBooking ? $bookedBooking->status : null,
                            'booking_id' => $bookedBooking ? $bookedBooking->id : null
                        ];
                    }
                }
            }
        }

        return $slots;
    }
}

if (!function_exists('get_admin_page_title')) {
    /**
     * Get the title for the current admin page based on the route.
     *
     * @return string
     */
    function get_admin_page_title(): string
    {
        $titleMap = [
            'administrateur.dashboard' => 'Tableau de bord',
            'administrateur.users.*' => 'Gestion des utilisateurs',
            'administrateur.prestataires.*' => 'Gestion des prestataires',
            'administrateur.clients.*' => 'Gestion des clients',
            'administrateur.services.*' => 'Modération des services',
            'administrateur.reviews.*' => 'Modération des avis',
        ];

        foreach ($titleMap as $pattern => $title) {
            if (request()->routeIs($pattern)) {
                return $title;
            }
        }

        return 'Administration';
    }
}
