<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\Payment;
use App\Models\Property;
use App\Models\Seat;
use App\Models\SeatRow;
use App\Models\Space;
use App\Models\Student;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database with libraries, shifts, seats, students, and payments.
     */
    public function run(): void
    {
        $this->call(AdminSeeder::class);

        // Vendor / Library Owner account
        $owner = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Udaan Admin',
                'password' => Hash::make('password'),
                'role' => 'vendor',
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );

        // Helper for default 3 shifts
        $defaultShifts = function (int $full, int $morning, int $evening) {
            return [
                'full' => [
                    'label' => 'Full day',
                    'time' => '6 AM – 10 PM',
                    'price' => $full,
                ],
                'morning' => [
                    'label' => 'Morning',
                    'time' => '6 AM – 2 PM',
                    'price' => $morning,
                ],
                'evening' => [
                    'label' => 'Evening',
                    'time' => '2 PM – 10 PM',
                    'price' => $evening,
                ],
            ];
        };

        // 1. Udaan Study Library (Owner Library)
        $udaan = Property::updateOrCreate(
            ['name' => 'Udaan Study Library'],
            [
                'user_id' => $owner->id,
                'category' => 'library',
                'area' => 'Mansarovar',
                'city' => 'Jaipur',
                'address' => 'Plot 14, Shipra Path, near Technology Circle, Mansarovar, Jaipur 302020',
                'phone' => '9829012345',
                'upi_id' => 'udaan.library@upi',
                'open_time' => '6:00 AM',
                'close_time' => '10:00 PM',
                'shifts' => $defaultShifts(1200, 700, 700),
                'facilities' => ['AC', 'Wi-Fi', 'Locker', 'RO water', 'CCTV', 'Power backup', 'Girls-only section'],
                'description' => 'Silent self-study hall for RAS, UPSC, SSC and REET aspirants. Separate AC and non-AC halls, personal lockers, 24-hour power backup and filtered water. Monthly passes by shift.',
            ]
        );

        // Udaan Halls
        $udaanGround = Space::firstOrCreate(
            ['property_id' => $udaan->id, 'name' => 'Ground floor'],
            [
                'is_ac' => true,
                'type' => 'floor',
                'sort_order' => 1,
                'aisle_position' => 5,
                'description' => 'Fully air-conditioned silent hall with ergonomic cabin desks.',
            ]
        );

        $udaanFirst = Space::firstOrCreate(
            ['property_id' => $udaan->id, 'name' => 'First floor'],
            [
                'is_ac' => false,
                'type' => 'floor',
                'sort_order' => 2,
                'aisle_position' => 6,
                'description' => 'Natural ventilation, high ceiling, spacious study desks.',
            ]
        );

        // Generate seats for Ground Floor (6 rows x 10 seats, aisle at 5)
        $lettersGround = ['A', 'B', 'C', 'D', 'E', 'F'];
        $groundSeats = [];
        foreach ($lettersGround as $rIdx => $letter) {
            $row = SeatRow::firstOrCreate(
                ['space_id' => $udaanGround->id, 'row_label' => 'Row '.$letter],
                ['section_name' => 'Ground Floor Hall', 'row_order' => $rIdx + 1, 'total_columns' => 10]
            );

            for ($col = 1; $col <= 10; $col++) {
                $isAisle = ($col === 5);
                $seat = Seat::firstOrCreate(
                    ['space_id' => $udaanGround->id, 'seat_row_id' => $row->id, 'column_index' => $col],
                    [
                        'seat_number' => $letter.$col,
                        'type' => 'seat',
                        'is_walkway' => $isAisle,
                        'status' => 'available',
                        'price' => 700.00,
                    ]
                );
                if (! $isAisle) {
                    $groundSeats[] = $seat;
                }
            }
        }

        // Generate seats for First Floor (4 rows x 12 seats, aisle at 6)
        $lettersFirst = ['A', 'B', 'C', 'D'];
        $firstSeats = [];
        foreach ($lettersFirst as $rIdx => $letter) {
            $row = SeatRow::firstOrCreate(
                ['space_id' => $udaanFirst->id, 'row_label' => 'Row '.$letter],
                ['section_name' => 'First Floor Hall', 'row_order' => $rIdx + 1, 'total_columns' => 12]
            );

            for ($col = 1; $col <= 12; $col++) {
                $isAisle = ($col === 6);
                $seat = Seat::firstOrCreate(
                    ['space_id' => $udaanFirst->id, 'seat_row_id' => $row->id, 'column_index' => $col],
                    [
                        'seat_number' => $letter.$col,
                        'type' => 'seat',
                        'is_walkway' => $isAisle,
                        'status' => 'available',
                        'price' => 550.00,
                    ]
                );
                if (! $isAisle) {
                    $firstSeats[] = $seat;
                }
            }
        }

        // 2. Shiksha Self Study Point
        $shiksha = Property::updateOrCreate(
            ['name' => 'Shiksha Self Study Point'],
            [
                'user_id' => $owner->id,
                'category' => 'library',
                'area' => 'Vaishali Nagar',
                'city' => 'Jaipur',
                'address' => 'B-42, Amrapali Marg, Vaishali Nagar, Jaipur',
                'phone' => '9414055501',
                'upi_id' => 'shiksha@upi',
                'open_time' => '6:00 AM',
                'close_time' => '11:00 PM',
                'shifts' => $defaultShifts(1400, 800, 800),
                'facilities' => ['AC', 'Wi-Fi', 'Locker', 'CCTV'],
                'description' => 'Quiet AC reading hall with cabin-style desks.',
            ]
        );
        $shikshaHall = Space::firstOrCreate(
            ['property_id' => $shiksha->id, 'name' => 'Main hall'],
            ['is_ac' => true, 'type' => 'floor', 'sort_order' => 1, 'aisle_position' => 6]
        );
        foreach (['A', 'B', 'C', 'D', 'E'] as $rIdx => $letter) {
            $row = SeatRow::firstOrCreate(
                ['space_id' => $shikshaHall->id, 'row_label' => 'Row '.$letter],
                ['section_name' => 'Main Hall', 'row_order' => $rIdx + 1, 'total_columns' => 12]
            );
            for ($c = 1; $c <= 12; $c++) {
                Seat::firstOrCreate(
                    ['space_id' => $shikshaHall->id, 'seat_row_id' => $row->id, 'column_index' => $c],
                    ['seat_number' => $letter.$c, 'type' => 'seat', 'is_walkway' => ($c === 6), 'status' => 'available', 'price' => 800.00]
                );
            }
        }

        // 3. Lakshya Library
        $lakshya = Property::updateOrCreate(
            ['name' => 'Lakshya Library'],
            [
                'user_id' => $owner->id,
                'category' => 'library',
                'area' => 'Malviya Nagar',
                'city' => 'Jaipur',
                'address' => 'Sector 3, near Gaurav Tower, Malviya Nagar, Jaipur',
                'phone' => '9660012233',
                'upi_id' => 'lakshya@upi',
                'open_time' => '7:00 AM',
                'close_time' => '10:00 PM',
                'shifts' => $defaultShifts(900, 550, 550),
                'facilities' => ['Wi-Fi', 'RO water', 'Newspapers', 'Power backup'],
                'description' => 'Affordable non-AC library close to coaching hubs.',
            ]
        );
        $lakshyaHall = Space::firstOrCreate(
            ['property_id' => $lakshya->id, 'name' => 'Hall A'],
            ['is_ac' => false, 'type' => 'floor', 'sort_order' => 1, 'aisle_position' => 5]
        );
        foreach (['A', 'B', 'C', 'D'] as $rIdx => $letter) {
            $row = SeatRow::firstOrCreate(
                ['space_id' => $lakshyaHall->id, 'row_label' => 'Row '.$letter],
                ['section_name' => 'Hall A', 'row_order' => $rIdx + 1, 'total_columns' => 10]
            );
            for ($c = 1; $c <= 10; $c++) {
                Seat::firstOrCreate(
                    ['space_id' => $lakshyaHall->id, 'seat_row_id' => $row->id, 'column_index' => $c],
                    ['seat_number' => $letter.$c, 'type' => 'seat', 'is_walkway' => ($c === 5), 'status' => 'available', 'price' => 550.00]
                );
            }
        }

        // 4. Ekagra Reading Hall (Sikar)
        $ekagra = Property::updateOrCreate(
            ['name' => 'Ekagra Reading Hall'],
            [
                'user_id' => $owner->id,
                'category' => 'library',
                'area' => 'Piprali Road',
                'city' => 'Sikar',
                'address' => 'Opp. Kalyan Circle, Piprali Road, Sikar',
                'phone' => '9887766554',
                'upi_id' => 'ekagra@upi',
                'open_time' => '5:30 AM',
                'close_time' => '10:30 PM',
                'shifts' => $defaultShifts(1100, 650, 650),
                'facilities' => ['AC', 'Wi-Fi', 'Locker', 'Girls-only section', 'Parking'],
                'description' => 'For NEET and JEE students. Separate girls section and personal lockers.',
            ]
        );
        $ekagraHall = Space::firstOrCreate(
            ['property_id' => $ekagra->id, 'name' => 'AC hall'],
            ['is_ac' => true, 'type' => 'floor', 'sort_order' => 1, 'aisle_position' => 5]
        );
        foreach (['A', 'B', 'C', 'D', 'E'] as $rIdx => $letter) {
            $row = SeatRow::firstOrCreate(
                ['space_id' => $ekagraHall->id, 'row_label' => 'Row '.$letter],
                ['section_name' => 'AC Hall', 'row_order' => $rIdx + 1, 'total_columns' => 10]
            );
            for ($c = 1; $c <= 10; $c++) {
                Seat::firstOrCreate(
                    ['space_id' => $ekagraHall->id, 'seat_row_id' => $row->id, 'column_index' => $c],
                    ['seat_number' => $letter.$c, 'type' => 'seat', 'is_walkway' => ($c === 5), 'status' => 'available', 'price' => 650.00]
                );
            }
        }

        // 5. Pratibha Digital Library (Jodhpur)
        $pratibha = Property::updateOrCreate(
            ['name' => 'Pratibha Digital Library'],
            [
                'user_id' => $owner->id,
                'category' => 'library',
                'area' => 'Ratanada',
                'city' => 'Jodhpur',
                'address' => 'Near Circuit House Road, Ratanada, Jodhpur',
                'phone' => '9928344556',
                'upi_id' => 'pratibha@upi',
                'open_time' => '6:00 AM',
                'close_time' => '10:00 PM',
                'shifts' => $defaultShifts(1000, 600, 600),
                'facilities' => ['AC', 'Wi-Fi', 'CCTV', 'Discussion room'],
                'description' => 'Bright AC hall with a discussion room for group study.',
            ]
        );
        $pratibhaHall = Space::firstOrCreate(
            ['property_id' => $pratibha->id, 'name' => 'Main hall'],
            ['is_ac' => true, 'type' => 'floor', 'sort_order' => 1, 'aisle_position' => 6]
        );
        foreach (['A', 'B', 'C', 'D'] as $rIdx => $letter) {
            $row = SeatRow::firstOrCreate(
                ['space_id' => $pratibhaHall->id, 'row_label' => 'Row '.$letter],
                ['section_name' => 'Main Hall', 'row_order' => $rIdx + 1, 'total_columns' => 11]
            );
            for ($c = 1; $c <= 11; $c++) {
                Seat::firstOrCreate(
                    ['space_id' => $pratibhaHall->id, 'seat_row_id' => $row->id, 'column_index' => $c],
                    ['seat_number' => $letter.$c, 'type' => 'seat', 'is_walkway' => ($c === 6), 'status' => 'available', 'price' => 600.00]
                );
            }
        }

        // 6. Realistic Students for Udaan Study Library
        // Let's seed active students with various due states!
        $firstNames = ['Aarav', 'Priya', 'Rohit', 'Neha', 'Vikas', 'Pooja', 'Ankit', 'Sneha', 'Manish', 'Kavita', 'Deepak', 'Ritu', 'Sandeep', 'Anjali', 'Rahul', 'Nisha', 'Amit', 'Komal', 'Suresh', 'Payal', 'Gaurav', 'Shivani', 'Mohit', 'Divya', 'Lokesh', 'Megha', 'Hemant', 'Sakshi', 'Yogesh', 'Bhavna', 'Naveen', 'Jyoti', 'Pankaj', 'Tanvi', 'Rakesh', 'Monika'];
        $lastNames = ['Sharma', 'Meena', 'Choudhary', 'Jat', 'Saini', 'Gupta', 'Yadav', 'Kumawat', 'Verma', 'Agarwal', 'Singh', 'Jangid', 'Soni', 'Bairwa', 'Khandelwal', 'Rathore'];
        $exams = ['RAS', 'UPSC', 'NEET', 'JEE', 'SSC CGL', 'REET', 'CA Inter', 'Banking PO', 'Rajasthan SI', '12th Board'];

        // Sample students to explicitly demonstrate due states:
        $sampleStudents = [
            // Overdue students (red badges)
            ['name' => 'Pankaj Sharma', 'shift' => 'morning', 'days_offset' => -12, 'exam' => 'RAS', 'seat_idx' => 0],
            ['name' => 'Rohit Meena', 'shift' => 'evening', 'days_offset' => -6, 'exam' => 'REET', 'seat_idx' => 1],
            ['name' => 'Pooja Choudhary', 'shift' => 'full', 'days_offset' => -3, 'exam' => 'NEET', 'seat_idx' => 2],
            ['name' => 'Deepak Saini', 'shift' => 'morning', 'days_offset' => -1, 'exam' => 'SSC CGL', 'seat_idx' => 3],
            // Due today (red)
            ['name' => 'Anjali Gupta', 'shift' => 'morning', 'days_offset' => 0, 'exam' => 'UPSC', 'seat_idx' => 4],
            ['name' => 'Vikas Yadav', 'shift' => 'evening', 'days_offset' => 0, 'exam' => 'Rajasthan SI', 'seat_idx' => 4], // Shares seat with Anjali!
            // Due tomorrow (half red)
            ['name' => 'Rahul Verma', 'shift' => 'morning', 'days_offset' => 1, 'exam' => 'JEE', 'seat_idx' => 5],
            // Due in 2-3 days (soon - red outline)
            ['name' => 'Sneha Agarwal', 'shift' => 'full', 'days_offset' => 2, 'exam' => 'CA Inter', 'seat_idx' => 6],
            ['name' => 'Sandeep Singh', 'shift' => 'morning', 'days_offset' => 3, 'exam' => 'Banking PO', 'seat_idx' => 7],
            ['name' => 'Ritu Jangid', 'shift' => 'evening', 'days_offset' => 3, 'exam' => 'RAS', 'seat_idx' => 7], // Shares seat with Sandeep!
            // Paid well in advance (green)
            ['name' => 'Manish Soni', 'shift' => 'full', 'days_offset' => 18, 'exam' => 'NEET', 'seat_idx' => 8],
            ['name' => 'Kavita Rathore', 'shift' => 'morning', 'days_offset' => 22, 'exam' => 'REET', 'seat_idx' => 9],
            ['name' => 'Amit Bairwa', 'shift' => 'evening', 'days_offset' => 15, 'exam' => 'SSC CGL', 'seat_idx' => 9],
            ['name' => 'Gaurav Khandelwal', 'shift' => 'full', 'days_offset' => 25, 'exam' => 'UPSC', 'seat_idx' => 10],
            ['name' => 'Nisha Prajapat', 'shift' => 'morning', 'days_offset' => 12, 'exam' => 'RAS', 'seat_idx' => 11],
            ['name' => 'Mohit Mali', 'shift' => 'evening', 'days_offset' => 20, 'exam' => 'Banking PO', 'seat_idx' => 12],
            ['name' => 'Divya Joshi', 'shift' => 'morning', 'days_offset' => 16, 'exam' => 'NEET', 'seat_idx' => 13],
            ['name' => 'Lokesh Swami', 'shift' => 'full', 'days_offset' => 14, 'exam' => 'Rajasthan SI', 'seat_idx' => 14],
        ];

        $today = Carbon::today();
        foreach ($sampleStudents as $sData) {
            $seat = $groundSeats[$sData['seat_idx']] ?? null;
            if (! $seat) {
                continue;
            }

            $fee = $sData['shift'] === 'full' ? 1200 : 700;
            $paidUntil = $today->copy()->addDays($sData['days_offset']);
            $joinedOn = $paidUntil->copy()->subMonths(2);

            $phone = '9829'.rand(100000, 999999);
            $parentPhone = '9414'.rand(100000, 999999);

            $student = Student::create([
                'property_id' => $udaan->id,
                'space_id' => $udaanGround->id,
                'seat_id' => $seat->id,
                'shift' => $sData['shift'],
                'name' => $sData['name'],
                'phone' => $phone,
                'parent_phone' => $parentPhone,
                'exam' => $sData['exam'],
                'monthly_fee' => $fee,
                'joined_on' => $joinedOn,
                'paid_until' => $paidUntil,
                'status' => 'active',
            ]);

            // Add previous payment
            Payment::create([
                'student_id' => $student->id,
                'property_id' => $udaan->id,
                'amount' => $fee,
                'months' => 1,
                'mode' => rand(0, 1) ? 'UPI' : 'Cash',
                'paid_on' => $paidUntil->copy()->subMonth(),
            ]);
        }

        // 7. Seed pending booking requests
        Booking::create([
            'property_id' => $udaan->id,
            'space_id' => $udaanGround->id,
            'seat_id' => $groundSeats[18]->id,
            'seat_number' => $groundSeats[18]->seat_number,
            'user_id' => $owner->id,
            'shift' => 'morning',
            'customer_name' => 'Harshvardhan Rathore',
            'customer_phone' => '9829554433',
            'parent_phone' => '9414887766',
            'exam' => 'RAS 2026',
            'start_date' => $today->copy()->addDays(2),
            'monthly_price' => 700.00,
            'status' => 'pending',
            'notes' => 'Need a quiet desk in AC hall near power socket for laptop study.',
        ]);

        Booking::create([
            'property_id' => $udaan->id,
            'space_id' => $udaanGround->id,
            'seat_id' => $groundSeats[19]->id,
            'seat_number' => $groundSeats[19]->seat_number,
            'user_id' => $owner->id,
            'shift' => 'full',
            'customer_name' => 'Megha Kumawat',
            'customer_phone' => '9782112233',
            'parent_phone' => '9829334455',
            'exam' => 'NEET UG',
            'start_date' => $today->copy()->addDays(1),
            'monthly_price' => 1200.00,
            'status' => 'pending',
            'notes' => '12-hour continuous study for medical entrance. Prefer ground floor AC.',
        ]);
    }
}
