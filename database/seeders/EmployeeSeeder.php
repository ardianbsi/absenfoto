<?php

namespace Database\Seeders;

use App\Models\Employee;
use App\Models\User;
use Illuminate\Database\Seeder;

class EmployeeSeeder extends Seeder
{
    public function run(): void
    {
        $this->createSuperAdminEmployee();
        $this->createHrEmployees();
        $this->createManagerEmployees();
        $this->createRegularEmployees();
    }

    private function createSuperAdminEmployee(): void
    {
        Employee::create([
            'user_id' => 1,
            'nik' => '3174010101900001',
            'nip' => '199001012024011001',
            'full_name' => 'Super Admin',
            'phone' => '081234567890',
            'address' => 'Jl. Merdeka No. 10, RT 05/RW 02, Kel. Menteng, Kec. Menteng, Jakarta',
            'place_of_birth' => 'Jakarta',
            'date_of_birth' => '1990-01-01',
            'gender' => 'Laki-laki',
            'religion' => 'Islam',
            'marital_status' => 'Menikah',
            'blood_type' => 'O',
            'department_id' => 1,
            'position_id' => 2,
            'manager_id' => null,
            'join_date' => '2024-01-01',
            'work_status' => 'Permanent',
            'is_active' => true,
        ]);
    }

    private function createHrEmployees(): void
    {
        Employee::create([
            'user_id' => 2,
            'nik' => '3174020202900002',
            'nip' => '199002022024012002',
            'full_name' => 'Dewi Rahayu',
            'phone' => '081234567891',
            'address' => 'Jl. Sudirman No. 25, RT 03/RW 01, Kel. Kebon Melati, Kec. Tanah Abang, Jakarta',
            'place_of_birth' => 'Bandung',
            'date_of_birth' => '1990-02-02',
            'gender' => 'Perempuan',
            'religion' => 'Islam',
            'marital_status' => 'Menikah',
            'blood_type' => 'A',
            'department_id' => 2,
            'position_id' => 7,
            'manager_id' => null,
            'join_date' => '2024-02-01',
            'work_status' => 'Permanent',
            'is_active' => true,
        ]);

        Employee::create([
            'user_id' => 3,
            'nik' => '3174030303900003',
            'nip' => '199003032024013003',
            'full_name' => 'Fitri Handayani',
            'phone' => '081234567892',
            'address' => 'Jl. Thamrin No. 48, RT 07/RW 03, Kel. Gondangdia, Kec. Menteng, Jakarta',
            'place_of_birth' => 'Surabaya',
            'date_of_birth' => '1990-03-03',
            'gender' => 'Perempuan',
            'religion' => 'Kristen',
            'marital_status' => 'Belum Menikah',
            'blood_type' => 'B',
            'department_id' => 2,
            'position_id' => 7,
            'manager_id' => null,
            'join_date' => '2024-03-01',
            'work_status' => 'Permanent',
            'is_active' => true,
        ]);
    }

    private function createManagerEmployees(): void
    {
        $managers = [
            ['user_id' => 4, 'nik' => '3174040404900004', 'full_name' => 'Bambang Wijaya', 'phone' => '081234567893', 'dept' => 3, 'pos' => 10],
            ['user_id' => 5, 'nik' => '3174050505900005', 'full_name' => 'Ratna Lestari', 'phone' => '081234567894', 'dept' => 4, 'pos' => 14],
            ['user_id' => 6, 'nik' => '3174060606900006', 'full_name' => 'Eko Nugroho', 'phone' => '081234567895', 'dept' => 5, 'pos' => 18],
            ['user_id' => 7, 'nik' => '3174070707900007', 'full_name' => 'Sari Utami', 'phone' => '081234567896', 'dept' => 6, 'pos' => 21],
            ['user_id' => 8, 'nik' => '3174080808900008', 'full_name' => 'Hendra Kusuma', 'phone' => '081234567897', 'dept' => 7, 'pos' => 24],
        ];

        foreach ($managers as $mgr) {
            Employee::create([
                'user_id' => $mgr['user_id'],
                'nik' => $mgr['nik'],
                'nip' => '1990' . sprintf('%02d', $mgr['user_id']) . sprintf('%02d', $mgr['user_id']) . '202401' . sprintf('%03d', $mgr['user_id']),
                'full_name' => $mgr['full_name'],
                'phone' => $mgr['phone'],
                'address' => 'Jl. Gatot Subroto No. ' . ($mgr['user_id'] * 10) . ', RT 02/RW 04, Kel. Kuningan, Kec. Setiabudi, Jakarta',
                'place_of_birth' => ['Jakarta', 'Bandung', 'Semarang', 'Yogyakarta', 'Medan'][$mgr['user_id'] - 4],
                'date_of_birth' => sprintf('198%d-%02d-%02d', $mgr['user_id'] - 3, $mgr['user_id'], $mgr['user_id']),
                'gender' => 'Laki-laki',
                'religion' => 'Islam',
                'marital_status' => 'Menikah',
                'blood_type' => 'O',
                'department_id' => $mgr['dept'],
                'position_id' => $mgr['pos'],
                'manager_id' => null,
                'join_date' => '2023-06-01',
                'work_status' => 'Permanent',
                'is_active' => true,
            ]);
        }
    }

    private function createRegularEmployees(): void
    {
        $deptPosMap = [
            1 => [2, 3, 4, 5],
            2 => [7, 8, 9],
            3 => [11, 12, 13],
            4 => [15, 16, 17],
            5 => [19, 20],
            6 => [22, 23],
            7 => [25, 26],
        ];

        $employeeData = [
            ['Agus Pratama', '3174090909900009', 'Laki-laki', 'Islam', 'Bandung', '1994-09-09'],
            ['Ahmad Hidayat', '3174101010900010', 'Laki-laki', 'Islam', 'Jakarta', '1995-10-10'],
            ['Budi Santoso', '3174111111900011', 'Laki-laki', 'Kristen', 'Semarang', '1993-11-11'],
            ['Cici Wulandari', '3174121212900012', 'Perempuan', 'Islam', 'Surabaya', '1996-12-12'],
            ['Deni Saputra', '3174130113900013', 'Laki-laki', 'Hindu', 'Denpasar', '1992-01-13'],
            ['Dian Pertiwi', '3174140214900014', 'Perempuan', 'Islam', 'Jakarta', '1994-02-14'],
            ['Fajar Nugroho', '3174150315900015', 'Laki-laki', 'Islam', 'Makassar', '1991-03-15'],
            ['Gilang Hermawan', '3174160416900016', 'Laki-laki', 'Kristen', 'Medan', '1993-04-16'],
            ['Gunawan Wibowo', '3174170517900017', 'Laki-laki', 'Islam', 'Yogyakarta', '1995-05-17'],
            ['Hendra Setiawan', '3174180618900018', 'Laki-laki', 'Katolik', 'Palembang', '1990-06-18'],
            ['Herman Maulana', '3174190719900019', 'Laki-laki', 'Islam', 'Bandung', '1994-07-19'],
            ['Hesti Purwanti', '3174200820900020', 'Perempuan', 'Islam', 'Malang', '1992-08-20'],
            ['Indah Kusuma', '3174210921900021', 'Perempuan', 'Hindu', 'Mataram', '1995-09-21'],
            ['Indra Nasution', '3174221022900022', 'Laki-laki', 'Islam', 'Medan', '1993-10-22'],
            ['Intan Siregar', '3174231123900023', 'Perempuan', 'Kristen', 'Balikpapan', '1991-11-23'],
            ['Joko Purnomo', '3174241224900024', 'Laki-laki', 'Islam', 'Surakarta', '1996-12-24'],
            ['Kadek Sinaga', '3174250125900025', 'Laki-laki', 'Hindu', 'Denpasar', '1994-01-25'],
            ['Kurniawan Ginting', '3174260226900026', 'Laki-laki', 'Katolik', 'Medan', '1992-02-26'],
            ['Linda Sembiring', '3174270327900027', 'Perempuan', 'Kristen', 'Pekanbaru', '1995-03-27'],
            ['Made Tamba', '3174280428900028', 'Laki-laki', 'Hindu', 'Denpasar', '1993-04-28'],
            ['Mega Manurung', '3174290529900029', 'Perempuan', 'Kristen', 'Medan', '1991-05-29'],
            ['Nina Siahaan', '3174300630900030', 'Perempuan', 'Islam', 'Jakarta', '1994-06-30'],
            ['Nurul Simanjuntak', '3174310731900031', 'Perempuan', 'Islam', 'Padang', '1992-07-31'],
            ['Oka Sitompul', '3174320801900032', 'Laki-laki', 'Hindu', 'Denpasar', '1996-08-01'],
            ['Olivia Pasaribu', '3174330902900033', 'Perempuan', 'Kristen', 'Manado', '1993-09-02'],
            ['Pramono Situmorang', '3174341003900034', 'Laki-laki', 'Katolik', 'Aceh', '1991-10-03'],
            ['Putri Nababan', '3174351104900035', 'Perempuan', 'Islam', 'Jakarta', '1995-11-04'],
            ['Ratna Siregar', '3174361205900036', 'Perempuan', 'Islam', 'Medan', '1994-12-05'],
            ['Rina Simatupang', '3174370106900037', 'Perempuan', 'Kristen', 'Bandung', '1993-01-06'],
            ['Rudi Sirait', '3174380207900038', 'Laki-laki', 'Islam', 'Jakarta', '1992-02-07'],
            ['Sari Pangaribuan', '3174390308900039', 'Perempuan', 'Islam', 'Yogyakarta', '1995-03-08'],
            ['Slamet Napitupulu', '3174400409900040', 'Laki-laki', 'Islam', 'Semarang', '1991-04-09'],
            ['Tri Lumbantoruan', '3174410510900041', 'Laki-laki', 'Kristen', 'Medan', '1994-05-10'],
            ['Utami Panggabean', '3174420611900042', 'Perempuan', 'Islam', 'Jakarta', '1993-06-11'],
            ['Wahyu Silalahi', '3174430712900043', 'Laki-laki', 'Katolik', 'Surabaya', '1992-07-12'],
            ['Widi Situmeang', '3174440813900044', 'Perempuan', 'Kristen', 'Bandung', '1995-08-13'],
            ['Yanti Marbun', '3174450914900045', 'Perempuan', 'Islam', 'Jakarta', '1994-09-14'],
            ['Zainal Purba', '3174461015900046', 'Laki-laki', 'Islam', 'Medan', '1993-10-15'],
            ['Bagus Siahaan', '3174471116900047', 'Laki-laki', 'Hindu', 'Denpasar', '1991-11-16'],
            ['Dewi Simbolon', '3174481217900048', 'Perempuan', 'Kristen', 'Medan', '1996-12-17'],
            ['Eko Saputra', '3174490118900049', 'Laki-laki', 'Islam', 'Jakarta', '1994-01-18'],
            ['Fitriani Hasibuan', '3174500219900050', 'Perempuan', 'Islam', 'Padang', '1992-02-19'],
            ['Gita Harahap', '3174510320900051', 'Perempuan', 'Budha', 'Pontianak', '1995-03-20'],
            ['Hari Lubis', '3174520421900052', 'Laki-laki', 'Islam', 'Medan', '1993-04-21'],
            ['Irma Dalimunthe', '3174530522900053', 'Perempuan', 'Kristen', 'Manado', '1991-05-22'],
            ['Junaidi Sitepu', '3174540623900054', 'Laki-laki', 'Islam', 'Aceh', '1994-06-23'],
            ['Kartika Saragih', '3174550724900055', 'Perempuan', 'Katolik', 'Kupang', '1992-07-24'],
            ['Lukman Hutahean', '3174560825900056', 'Laki-laki', 'Kristen', 'Medan', '1995-08-25'],
            ['Murni Simamora', '3174570926900057', 'Perempuan', 'Islam', 'Jakarta', '1993-09-26'],
            ['Nurdin Sihombing', '3174581027900058', 'Laki-laki', 'Islam', 'Bandung', '1991-10-27'],
        ];

        $managerIds = [4, 5, 6, 7, 8];
        $religions = ['Islam', 'Kristen', 'Katolik', 'Hindu', 'Budha'];
        $maritalStatuses = ['Belum Menikah', 'Menikah', 'Cerai'];
        $workStatuses = ['Permanent', 'Contract', 'Probation'];
        $genders = ['Laki-laki', 'Perempuan'];
        $cities = ['Jakarta', 'Bandung', 'Surabaya', 'Semarang', 'Yogyakarta', 'Medan', 'Makassar', 'Denpasar', 'Palembang', 'Malang'];

        foreach ($employeeData as $index => $data) {
            $employeeNumber = $index + 1;
            $deptId = (($employeeNumber - 1) % 7) + 1;
            $posOptions = $deptPosMap[$deptId];
            $posId = $posOptions[($employeeNumber - 1) % count($posOptions)];

            $managerIndex = intdiv($employeeNumber - 1, 10);
            $managerId = $managerIndex < 5 ? $managerIds[$managerIndex] : 8;

            $gender = $data[2];
            $religion = $data[3];
            $city = $data[4];
            $birthDate = $data[5];
            $birthYear = (int)substr($birthDate, 0, 4);

            $joinYear = rand(2020, 2025);
            $joinMonth = rand(1, 12);
            $joinDay = rand(1, 28);

            Employee::create([
                'user_id' => $index + 9,
                'nik' => $data[1],
                'nip' => $birthYear . sprintf('%02d%02d', $index + 1, $index + 1) . '2024' . sprintf('%03d', $index + 9),
                'full_name' => $data[0],
                'phone' => '08' . str_pad(rand(1000000000, 9999999999), 10, '0', STR_PAD_LEFT),
                'address' => 'Jl. ' . ['Merdeka', 'Sudirman', 'Thamrin', 'Diponegoro', 'Pahlawan'][rand(0, 4)]
                    . ' No. ' . rand(1, 200) . ', RT ' . rand(1, 15) . '/RW ' . rand(1, 10)
                    . ', Kel. ' . ['Menteng', 'Kebon Melati', 'Gondangdia', 'Kuningan', 'Setiabudi'][rand(0, 4)]
                    . ', Kec. ' . ['Tanah Abang', 'Menteng', 'Setiabudi', 'Tebet', 'Pancoran'][rand(0, 4)]
                    . ', ' . $city,
                'place_of_birth' => $city,
                'date_of_birth' => $birthDate,
                'gender' => $gender,
                'religion' => $religion,
                'marital_status' => $maritalStatuses[rand(0, 2)],
                'blood_type' => ['A', 'B', 'AB', 'O'][rand(0, 3)],
                'department_id' => $deptId,
                'position_id' => $posId,
                'manager_id' => $managerId,
                'join_date' => sprintf('%04d-%02d-%02d', $joinYear, $joinMonth, $joinDay),
                'work_status' => $workStatuses[rand(0, 2)],
                'is_active' => true,
            ]);
        }
    }
}
