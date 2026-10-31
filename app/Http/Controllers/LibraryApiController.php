<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Payment;
use App\Models\Property;
use App\Models\Seat;
use App\Models\SeatRow;
use App\Models\Space;
use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

class LibraryApiController extends Controller
{
    /**
     * Get complete dataset formatted for the CoworkLibrary single-page app.
     */
    public function getData(): JsonResponse
    {
        return response()->json($this->buildFullPayload());
    }

    /**
     * Build the full payload matching the exact JS state schema.
     */
    public function buildFullPayload(): array
    {
        $user = auth()->user();
        if ($user && $user->isVendor()) {
            $ownerProperty = $user->properties()->first()
                ?? Property::where('name', 'like', '%Udaan%')->first()
                ?? Property::first();
        } else {
            $ownerProperty = Property::where('name', 'like', '%Udaan%')->first()
                ?? Property::first();
        }

        $properties = Property::with([
            'spaces.seatRows.seats',
            'students',
            'payments',
            'bookings',
        ])->get();

        $hues = [250, 205, 160, 20, 330, 280, 190];
        $libs = [];

        foreach ($properties as $idx => $prop) {
            $halls = [];
            foreach ($prop->spaces as $space) {
                $rows = [];
                foreach ($space->seatRows as $row) {
                    $rows[] = [
                        'id' => (string) $row->id,
                        'n' => (int) $row->total_columns,
                        'aisle' => (int) ($space->aisle_position ?? 5),
                    ];
                }
                $halls[] = [
                    'id' => (string) $space->id,
                    'name' => $space->name,
                    'ac' => (bool) $space->is_ac,
                    'rows' => $rows,
                ];
            }

            $defaultShifts = [
                'full' => ['label' => 'Full day', 'time' => '6 AM – 10 PM', 'price' => 1200],
                'morning' => ['label' => 'Morning', 'time' => '6 AM – 2 PM', 'price' => 700],
                'evening' => ['label' => 'Evening', 'time' => '2 PM – 10 PM', 'price' => 700],
            ];

            $libs[] = [
                'id' => (string) $prop->id,
                'userId' => (string) ($prop->user_id ?? ''),
                'name' => $prop->name,
                'area' => $prop->area ?? '',
                'city' => $prop->city ?? 'Jaipur',
                'address' => $prop->address ?? '',
                'phone' => $prop->phone ?? '',
                'upi' => $prop->upi_id ?? '',
                'open' => $prop->open_time ?? '6:00 AM',
                'close' => $prop->close_time ?? '10:00 PM',
                'facilities' => $prop->facilities ?? ['AC', 'Wi-Fi', 'Locker', 'RO water', 'CCTV'],
                'desc' => $prop->description ?? '',
                'shifts' => ! empty($prop->shifts) ? $prop->shifts : $defaultShifts,
                'halls' => $halls,
                'hue' => $hues[$idx % count($hues)],
            ];
        }

        $allStudents = Student::with('seat')->get();
        $studentsList = [];
        foreach ($allStudents as $st) {
            $seatCode = '';
            if ($st->seat) {
                $seatCode = $st->seat->seat_row_id.':'.$st->seat->column_index;
            }

            $studentsList[] = [
                'id' => (string) $st->id,
                'libId' => (string) $st->property_id,
                'name' => $st->name,
                'phone' => (string) $st->phone,
                'parent' => (string) ($st->parent_phone ?? ''),
                'exam' => (string) ($st->exam ?? 'General'),
                'shift' => $st->shift,
                'hall' => (string) $st->space_id,
                'seat' => $seatCode,
                'fee' => (int) $st->monthly_fee,
                'joined' => $st->joined_on ? $st->joined_on->format('Y-m-d') : Carbon::today()->format('Y-m-d'),
                'paidUntil' => $st->paid_until ? $st->paid_until->format('Y-m-d') : Carbon::today()->format('Y-m-d'),
                'status' => $st->status ?? 'active',
                'created' => $st->created_at ? $st->created_at->format('Y-m-d') : Carbon::today()->format('Y-m-d'),
            ];
        }

        $allPayments = Payment::orderBy('paid_on', 'desc')->get();
        $paymentsList = [];
        foreach ($allPayments as $pm) {
            $paymentsList[] = [
                'id' => (string) $pm->id,
                'sid' => (string) $pm->student_id,
                'amount' => (int) $pm->amount,
                'months' => (int) ($pm->months ?? 1),
                'mode' => $pm->mode ?? 'UPI',
                'date' => $pm->paid_on ? $pm->paid_on->format('Y-m-d') : Carbon::today()->format('Y-m-d'),
            ];
        }

        $allBookings = Booking::with('seat')->orderBy('created_at', 'desc')->get();
        $requestsList = [];
        foreach ($allBookings as $bk) {
            $seatCode = '';
            if ($bk->seat) {
                $seatCode = $bk->seat->seat_row_id.':'.$bk->seat->column_index;
            }

            $requestsList[] = [
                'id' => (string) $bk->id,
                'libId' => (string) $bk->property_id,
                'name' => (string) ($bk->customer_name ?? 'Applicant'),
                'phone' => (string) ($bk->customer_phone ?? ''),
                'parent' => (string) ($bk->parent_phone ?? ''),
                'exam' => (string) ($bk->exam ?? 'Competitive'),
                'shift' => (string) ($bk->shift ?? 'morning'),
                'hall' => (string) ($bk->space_id ?? ''),
                'seat' => $seatCode,
                'start' => $bk->start_date ? $bk->start_date->format('Y-m-d') : Carbon::today()->format('Y-m-d'),
                'note' => (string) ($bk->notes ?? ''),
                'status' => $bk->status ?? 'pending',
                'date' => $bk->created_at ? $bk->created_at->format('Y-m-d') : Carbon::today()->format('Y-m-d'),
            ];
        }

        $myRequestsList = [];
        if ($user) {
            $myBookings = Booking::with('seat')
                ->where(function ($q) use ($user) {
                    $q->where('user_id', $user->id);
                    if (! empty($user->phone)) {
                        $q->orWhere('customer_phone', $user->phone);
                    }
                })
                ->orderBy('created_at', 'desc')
                ->get();

            foreach ($myBookings as $bk) {
                $seatCode = '';
                if ($bk->seat) {
                    $seatCode = $bk->seat->seat_row_id.':'.$bk->seat->column_index;
                }

                $myRequestsList[] = [
                    'id' => (string) $bk->id,
                    'libId' => (string) $bk->property_id,
                    'name' => (string) ($bk->customer_name ?? 'Applicant'),
                    'phone' => (string) ($bk->customer_phone ?? ''),
                    'parent' => (string) ($bk->parent_phone ?? ''),
                    'exam' => (string) ($bk->exam ?? 'Competitive'),
                    'shift' => (string) ($bk->shift ?? 'morning'),
                    'hall' => (string) ($bk->space_id ?? ''),
                    'seat' => $seatCode,
                    'start' => $bk->start_date ? $bk->start_date->format('Y-m-d') : Carbon::today()->format('Y-m-d'),
                    'note' => (string) ($bk->notes ?? ''),
                    'status' => $bk->status ?? 'pending',
                    'date' => $bk->created_at ? $bk->created_at->format('Y-m-d') : Carbon::today()->format('Y-m-d'),
                ];
            }
        }

        return [
            'v' => 1,
            'ownerLibId' => (string) ($ownerProperty ? $ownerProperty->id : ''),
            'libs' => $libs,
            'students' => $studentsList,
            'payments' => $paymentsList,
            'requests' => $requestsList,
            'myRequests' => $myRequestsList,
        ];
    }

    /**
     * Handle actions sent by the frontend application and persist to MySQL.
     */
    public function handleSync(Request $request): JsonResponse
    {
        $action = $request->input('action');
        $payload = $request->input('payload', []);

        try {
            switch ($action) {
                case 'create_booking':
                    $libId = (int) ($payload['libId'] ?? 1);
                    $hallId = ! empty($payload['hall']) ? (int) $payload['hall'] : null;
                    $seatStr = $payload['seat'] ?? '';
                    $seatId = null;
                    $seatNumber = 'Desk';

                    if ($seatStr) {
                        if (str_contains($seatStr, ':')) {
                            $parts = explode(':', $seatStr);
                            if (count($parts) >= 3) {
                                $rowId = (int) $parts[1];
                                $col = (int) $parts[2];
                            } else {
                                $rowId = (int) $parts[0];
                                $col = (int) $parts[1];
                            }
                            $foundSeat = Seat::where('seat_row_id', $rowId)
                                ->where('column_index', $col)
                                ->first();
                            if ($foundSeat) {
                                $seatId = $foundSeat->id;
                                $seatNumber = $foundSeat->seat_number;
                                if (! $hallId) {
                                    $hallId = $foundSeat->space_id;
                                }
                            }
                        } elseif (is_numeric($seatStr)) {
                            $foundSeat = Seat::find((int) $seatStr);
                            if ($foundSeat) {
                                $seatId = $foundSeat->id;
                                $seatNumber = $foundSeat->seat_number;
                                if (! $hallId) {
                                    $hallId = $foundSeat->space_id;
                                }
                            }
                        }
                    }

                    $booking = Booking::create([
                        'property_id' => $libId,
                        'space_id' => $hallId,
                        'seat_id' => $seatId,
                        'seat_number' => $seatNumber,
                        'user_id' => auth()->id() ?? (int) ($payload['user_id'] ?? 1),
                        'shift' => $payload['shift'] ?? 'morning',
                        'customer_name' => $payload['name'] ?? '',
                        'customer_phone' => $payload['phone'] ?? '',
                        'parent_phone' => $payload['parent'] ?? null,
                        'exam' => $payload['exam'] ?? null,
                        'start_date' => ! empty($payload['start']) ? Carbon::parse($payload['start']) : Carbon::today(),
                        'monthly_price' => (float) ($payload['fee'] ?? 700),
                        'status' => 'pending',
                        'notes' => $payload['note'] ?? null,
                    ]);

                    return response()->json([
                        'success' => true,
                        'message' => 'Booking request submitted successfully',
                        'id' => (string) $booking->id,
                        'data' => $this->buildFullPayload(),
                    ]);

                case 'approve_booking':
                    $reqId = (int) ($payload['id'] ?? 0);
                    $booking = Booking::find($reqId);
                    if (! $booking) {
                        return response()->json(['success' => false, 'message' => 'Request not found'], 404);
                    }

                    $seatStr = $payload['seat'] ?? '';
                    $hallId = (int) ($payload['hall'] ?? $booking->space_id);
                    $seatId = null;
                    $foundSeat = null;

                    if ($seatStr) {
                        if (str_contains($seatStr, ':')) {
                            $parts = explode(':', $seatStr);
                            if (count($parts) >= 3) {
                                $rowId = (int) $parts[1];
                                $col = (int) $parts[2];
                            } else {
                                $rowId = (int) $parts[0];
                                $col = (int) $parts[1];
                            }
                            $foundSeat = Seat::where('seat_row_id', $rowId)
                                ->where('column_index', $col)
                                ->first();
                        } elseif (is_numeric($seatStr)) {
                            $foundSeat = Seat::find((int) $seatStr);
                        }

                        if ($foundSeat) {
                            $seatId = $foundSeat->id;
                            $hallId = $foundSeat->space_id;
                        }
                    }

                    if (! $seatId && $booking->seat_id) {
                        $seatId = $booking->seat_id;
                        $foundSeat = Seat::find($seatId);
                        if ($foundSeat) {
                            $hallId = $foundSeat->space_id;
                        }
                    }

                    $fee = (float) ($payload['fee'] ?? ($booking->monthly_price ?? 700));
                    $startDate = ! empty($payload['start']) ? Carbon::parse($payload['start']) : ($booking->start_date ?? Carbon::today());
                    $isPaid = ! empty($payload['paid']);
                    $paidUntil = $isPaid ? $startDate->copy()->addMonth() : $startDate->copy();

                    if ($foundSeat) {
                        $foundSeat->update(['status' => 'occupied']);
                    }

                    // Enrolling the student immediately books and locks the seat
                    $student = Student::create([
                        'property_id' => $booking->property_id,
                        'space_id' => $hallId,
                        'seat_id' => $seatId,
                        'shift' => $booking->shift ?? 'morning',
                        'name' => $booking->customer_name ?? 'Student',
                        'phone' => $booking->customer_phone ?? '',
                        'parent_phone' => $booking->parent_phone,
                        'exam' => $booking->exam,
                        'monthly_fee' => $fee,
                        'joined_on' => $startDate,
                        'paid_until' => $paidUntil,
                        'status' => 'active',
                    ]);

                    if ($isPaid) {
                        Payment::create([
                            'student_id' => $student->id,
                            'property_id' => $booking->property_id,
                            'amount' => $fee,
                            'months' => 1,
                            'mode' => 'Cash',
                            'paid_on' => Carbon::today(),
                        ]);
                    }

                    $booking->update([
                        'status' => 'approved',
                        'student_id' => $student->id,
                        'space_id' => $hallId,
                        'seat_id' => $seatId,
                        'seat_number' => $foundSeat ? $foundSeat->seat_number : ($booking->seat_number ?? 'Desk'),
                        'approved_at' => now(),
                    ]);

                    return response()->json([
                        'success' => true,
                        'message' => 'Request approved and seat booked immediately',
                        'student_id' => (string) $student->id,
                        'data' => $this->buildFullPayload(),
                    ]);

                case 'reject_booking':
                    $reqId = (int) ($payload['id'] ?? 0);
                    $booking = Booking::find($reqId);
                    if ($booking) {
                        $booking->update(['status' => 'rejected']);
                    }

                    return response()->json([
                        'success' => true,
                        'message' => 'Request rejected',
                        'data' => $this->buildFullPayload(),
                    ]);

                case 'save_student':
                    $stId = (int) ($payload['id'] ?? 0);
                    $hallId = (int) ($payload['hall'] ?? 0);
                    $seatStr = $payload['seat'] ?? '';
                    $seatId = null;
                    $foundSeat = null;

                    if ($seatStr) {
                        if (str_contains($seatStr, ':')) {
                            $parts = explode(':', $seatStr);
                            if (count($parts) >= 3) {
                                $rowId = (int) $parts[1];
                                $col = (int) $parts[2];
                            } else {
                                $rowId = (int) $parts[0];
                                $col = (int) $parts[1];
                            }
                            $foundSeat = Seat::where('seat_row_id', $rowId)
                                ->where('column_index', $col)
                                ->first();
                        } elseif (is_numeric($seatStr)) {
                            $foundSeat = Seat::find((int) $seatStr);
                        }

                        if ($foundSeat) {
                            $seatId = $foundSeat->id;
                            $foundSeat->update(['status' => 'occupied']);
                        }
                    }

                    $fee = (float) ($payload['fee'] ?? 700);
                    $joinedOn = ! empty($payload['joined']) ? Carbon::parse($payload['joined']) : Carbon::today();
                    $paidUntil = ! empty($payload['paidUntil']) ? Carbon::parse($payload['paidUntil']) : Carbon::today();

                    if ($stId > 0 && ($existing = Student::find($stId))) {
                        $existing->update([
                            'name' => $payload['name'] ?? $existing->name,
                            'phone' => $payload['phone'] ?? $existing->phone,
                            'parent_phone' => $payload['parent'] ?? $existing->parent_phone,
                            'exam' => $payload['exam'] ?? $existing->exam,
                            'shift' => $payload['shift'] ?? $existing->shift,
                            'space_id' => $hallId > 0 ? $hallId : $existing->space_id,
                            'seat_id' => $seatId ?? $existing->seat_id,
                            'monthly_fee' => $fee,
                            'joined_on' => $joinedOn,
                            'paid_until' => $paidUntil,
                            'status' => 'active',
                        ]);
                    } else {
                        $propertyId = (int) ($payload['property_id'] ?? 1);
                        $newStudent = Student::create([
                            'property_id' => $propertyId,
                            'space_id' => $hallId,
                            'seat_id' => $seatId,
                            'shift' => $payload['shift'] ?? 'morning',
                            'name' => $payload['name'] ?? '',
                            'phone' => $payload['phone'] ?? '',
                            'parent_phone' => $payload['parent'] ?? null,
                            'exam' => $payload['exam'] ?? null,
                            'monthly_fee' => $fee,
                            'joined_on' => $joinedOn,
                            'paid_until' => $paidUntil,
                            'status' => 'active',
                        ]);

                        if ($paidUntil->greaterThan($joinedOn)) {
                            $months = max(1, (int) round($joinedOn->diffInDays($paidUntil) / 30));
                            Payment::create([
                                'student_id' => $newStudent->id,
                                'property_id' => $propertyId,
                                'amount' => $fee * $months,
                                'months' => $months,
                                'mode' => 'Cash',
                                'paid_on' => Carbon::today(),
                            ]);
                        }
                    }

                    return response()->json([
                        'success' => true,
                        'message' => 'Student record saved',
                        'data' => $this->buildFullPayload(),
                    ]);

                case 'vacate_student':
                    $stId = (int) ($payload['id'] ?? 0);
                    $student = Student::find($stId);
                    if ($student) {
                        $student->update([
                            'status' => 'left',
                            'left_on' => Carbon::today(),
                            'seat_id' => null,
                        ]);
                    }

                    return response()->json([
                        'success' => true,
                        'message' => 'Seat vacated successfully',
                        'data' => $this->buildFullPayload(),
                    ]);

                case 'save_payment':
                    $stId = (int) ($payload['sid'] ?? 0);
                    $student = Student::find($stId);
                    if (! $student) {
                        return response()->json(['success' => false, 'message' => 'Student not found'], 404);
                    }

                    $months = (int) ($payload['months'] ?? 1);
                    $amount = (float) ($payload['amount'] ?? ($student->monthly_fee * $months));
                    $mode = $payload['mode'] ?? 'UPI';

                    Payment::create([
                        'student_id' => $student->id,
                        'property_id' => $student->property_id,
                        'amount' => $amount,
                        'months' => $months,
                        'mode' => $mode,
                        'paid_on' => Carbon::today(),
                    ]);

                    $currentPaidUntil = $student->paid_until ? Carbon::parse($student->paid_until) : Carbon::today();
                    $newPaidUntil = $currentPaidUntil->copy()->addMonths($months);
                    $student->update(['paid_until' => $newPaidUntil]);

                    return response()->json([
                        'success' => true,
                        'message' => 'Payment recorded successfully',
                        'data' => $this->buildFullPayload(),
                    ]);

                case 'save_library':
                    $libId = (int) ($payload['id'] ?? 0);
                    $userId = auth()->id() ?? 1;
                    $user = auth()->user();

                    $property = ($libId > 0) ? Property::find($libId) : null;

                    if ($property) {
                        $property->update([
                            'name' => $payload['name'] ?? $property->name,
                            'area' => $payload['area'] ?? $property->area,
                            'city' => $payload['city'] ?? $property->city,
                            'address' => $payload['address'] ?? $property->address,
                            'phone' => $payload['phone'] ?? $property->phone,
                            'upi_id' => $payload['upi'] ?? $property->upi_id,
                            'open_time' => $payload['open'] ?? $property->open_time,
                            'close_time' => $payload['close'] ?? $property->close_time,
                            'facilities' => $payload['facilities'] ?? $property->facilities,
                            'shifts' => $payload['shifts'] ?? $property->shifts,
                            'description' => $payload['desc'] ?? $property->description,
                        ]);
                    } else {
                        $defaultShifts = [
                            'full' => ['label' => 'Full day', 'time' => '6 AM – 10 PM', 'price' => 1200],
                            'morning' => ['label' => 'Morning', 'time' => '6 AM – 2 PM', 'price' => 700],
                            'evening' => ['label' => 'Evening', 'time' => '2 PM – 10 PM', 'price' => 700],
                        ];
                        $property = Property::create([
                            'user_id' => $userId,
                            'name' => ! empty($payload['name']) ? $payload['name'] : 'My Study Library',
                            'category' => 'library',
                            'area' => $payload['area'] ?? '',
                            'city' => $payload['city'] ?? 'Jaipur',
                            'address' => $payload['address'] ?? '',
                            'phone' => $payload['phone'] ?? ($user->phone ?? ''),
                            'upi_id' => $payload['upi'] ?? '',
                            'open_time' => $payload['open'] ?? '6:00 AM',
                            'close_time' => $payload['close'] ?? '10:00 PM',
                            'facilities' => $payload['facilities'] ?? ['AC', 'Wi-Fi', 'Locker', 'RO water', 'CCTV'],
                            'shifts' => $payload['shifts'] ?? $defaultShifts,
                            'description' => $payload['desc'] ?? '',
                        ]);

                        if ($user && ! in_array($user->role, ['administrator', 'super_admin', 'admin'], true)) {
                            $user->update(['role' => 'vendor']);
                        }

                        $hall = Space::create([
                            'property_id' => $property->id,
                            'name' => 'Main Hall',
                            'is_ac' => true,
                            'type' => 'floor',
                            'sort_order' => 1,
                            'aisle_position' => 5,
                        ]);

                        foreach (['A', 'B', 'C', 'D'] as $rIdx => $letter) {
                            $row = SeatRow::create([
                                'space_id' => $hall->id,
                                'section_name' => 'Main Hall',
                                'row_label' => 'Row '.$letter,
                                'row_order' => $rIdx + 1,
                                'total_columns' => 10,
                            ]);

                            for ($c = 1; $c <= 10; $c++) {
                                Seat::create([
                                    'space_id' => $hall->id,
                                    'seat_row_id' => $row->id,
                                    'column_index' => $c,
                                    'seat_number' => $letter.$c,
                                    'type' => 'seat',
                                    'is_walkway' => ($c === 5),
                                    'status' => 'available',
                                    'price' => 700.00,
                                ]);
                            }
                        }
                    }

                    return response()->json([
                        'success' => true,
                        'message' => 'Library property saved',
                        'property_id' => (string) $property->id,
                        'data' => $this->buildFullPayload(),
                    ]);

                case 'delete_library':
                    $libId = (int) ($payload['id'] ?? 0);
                    $userId = auth()->id() ?? 1;
                    $user = auth()->user();
                    $property = Property::find($libId);

                    if ($property && ($property->user_id === $userId || ($user && $user->isAdmin()))) {
                        foreach ($property->spaces as $space) {
                            foreach ($space->seatRows as $row) {
                                $row->seats()->delete();
                                $row->delete();
                            }
                            $space->delete();
                        }
                        $property->bookings()->delete();
                        $property->students()->delete();
                        $property->payments()->delete();
                        $property->delete();
                    }

                    return response()->json([
                        'success' => true,
                        'message' => 'Library deleted successfully',
                        'data' => $this->buildFullPayload(),
                    ]);

                case 'save_hall':
                    $propertyId = (int) ($payload['property_id'] ?? 1);
                    $hallId = (int) ($payload['id'] ?? 0);
                    $name = $payload['name'] ?? 'Reading Hall';
                    $isAc = ! empty($payload['ac']);
                    $rows = $payload['rows'] ?? [];

                    if ($hallId > 0 && ($space = Space::find($hallId))) {
                        $space->update(['name' => $name, 'is_ac' => $isAc]);
                    } else {
                        $space = Space::create([
                            'property_id' => $propertyId,
                            'name' => $name,
                            'is_ac' => $isAc,
                            'type' => 'floor',
                            'sort_order' => 10,
                            'aisle_position' => 5,
                        ]);
                    }

                    // Update rows & seats
                    $letterArr = range('A', 'Z');
                    foreach ($rows as $rIdx => $rData) {
                        $label = 'Row '.($letterArr[$rIdx] ?? ($rIdx + 1));
                        $colCount = (int) ($rData['n'] ?? 10);
                        $aisle = (int) ($rData['aisle'] ?? 5);

                        $rowId = (int) ($rData['id'] ?? 0);
                        $row = SeatRow::find($rowId);
                        if (! $row || $row->space_id !== $space->id) {
                            $row = SeatRow::create([
                                'space_id' => $space->id,
                                'section_name' => $name,
                                'row_label' => $label,
                                'row_order' => $rIdx + 1,
                                'total_columns' => $colCount,
                            ]);
                        } else {
                            $row->update(['total_columns' => $colCount]);
                        }

                        for ($c = 1; $c <= $colCount; $c++) {
                            Seat::firstOrCreate(
                                ['space_id' => $space->id, 'seat_row_id' => $row->id, 'column_index' => $c],
                                [
                                    'seat_number' => ($letterArr[$rIdx] ?? 'R').$c,
                                    'type' => 'seat',
                                    'is_walkway' => ($c === $aisle),
                                    'status' => 'available',
                                    'price' => 700.00,
                                ]
                            );
                        }
                    }

                    return response()->json([
                        'success' => true,
                        'message' => 'Hall layout updated',
                        'data' => $this->buildFullPayload(),
                    ]);

                case 'reset_demo':
                    Artisan::call('db:seed', ['--no-interaction' => true]);

                    return response()->json([
                        'success' => true,
                        'message' => 'Database successfully reset to demo data',
                        'data' => $this->buildFullPayload(),
                    ]);

                default:
                    return response()->json(['success' => false, 'message' => 'Unknown action: '.$action], 400);
            }
        } catch (\Throwable $e) {
            Log::error('Library sync error: '.$e->getMessage(), ['trace' => $e->getTraceAsString()]);

            return response()->json([
                'success' => false,
                'message' => 'Error: '.$e->getMessage(),
            ], 500);
        }
    }
}
