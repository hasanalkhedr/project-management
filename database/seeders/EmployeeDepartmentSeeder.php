<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Department;
use App\Models\Employee;

class EmployeeDepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // أقسام تجريبية
        $departments = [
            [
                'name_ar' => 'قسم الموارد البشرية',
                'name_en' => 'Human Resources',
                'description' => 'مسؤول عن إدارة شؤون الموظفين والتوظيف',
            ],
            [
                'name_ar' => 'قسم تكنولوجيا المعلومات',
                'name_en' => 'IT Department',
                'description' => 'مسؤول عن البنية التحتية التقنية والدعم الفني',
            ],
            [
                'name_ar' => 'قسم المحاسبة والمالية',
                'name_en' => 'Accounting & Finance',
                'description' => 'مسؤول عن الشؤون المالية والمحاسبة',
            ],
            [
                'name_ar' => 'قسم التسويق والمبيعات',
                'name_en' => 'Marketing & Sales',
                'description' => 'مسؤول عن التسويق والمبيعات والعلاقات مع العملاء',
            ],
            [
                'name_ar' => 'قسم العمليات',
                'name_en' => 'Operations',
                'description' => 'مسؤول عن العمليات اليومية والإنتاج',
            ],
        ];

        foreach ($departments as $dept) {
            Department::create($dept);
        }

        // موظفين تجريبيين
        $employees = [
            [
                'name_ar' => 'أحمد محمد علي',
                'name_en' => 'Ahmed Mohammed Ali',
                'nationality' => 'syrian',
                'date_of_birth' => '1990-05-15',
                'gender' => 'male',
                'marital_status' => 'married',
                'national_id' => '12345678901',
                'place_of_registration' => 'دمشق',
                'phone' => '+963944123456',
                'email' => 'ahmed@example.com',
                'current_address' => 'دمشق، المزة',
                'emergency_contact_name' => 'محمد علي',
                'emergency_contact_phone' => '+963944654321',
                'department_id' => 1,
                'job_title_id' => null,
                'direct_manager_id' => null,
                'project_id' => null,
                'contract_type' => 'full_time',
                'employment_status' => 'active',
                'hire_date' => '2020-01-15',
                'probation_period' => 90,
                'contract_start_date' => '2020-01-15',
                'contract_end_date' => '2025-01-15',
                'basic_salary' => 500000,
                'housing_allowance' => 100000,
                'transportation_allowance' => 50000,
                'other_allowances' => 30000,
                'total_salary' => 680000,
                'payment_method' => 'bank_transfer',
                'bank_account_number' => 'SY1234567890',
                'bank_name' => 'بنك سوريا',
            ],
            [
                'name_ar' => 'فاطمة خالد حسن',
                'name_en' => 'Fatima Khalid Hassan',
                'nationality' => 'syrian',
                'date_of_birth' => '1992-08-20',
                'gender' => 'female',
                'marital_status' => 'single',
                'national_id' => '12345678902',
                'place_of_registration' => 'حلب',
                'phone' => '+963944234567',
                'email' => 'fatima@example.com',
                'current_address' => 'حلب، السكري',
                'emergency_contact_name' => 'خالد حسن',
                'emergency_contact_phone' => '+963944765432',
                'department_id' => 2,
                'job_title_id' => null,
                'direct_manager_id' => null,
                'project_id' => null,
                'contract_type' => 'full_time',
                'employment_status' => 'active',
                'hire_date' => '2021-03-01',
                'probation_period' => 90,
                'contract_start_date' => '2021-03-01',
                'contract_end_date' => '2026-03-01',
                'basic_salary' => 450000,
                'housing_allowance' => 80000,
                'transportation_allowance' => 40000,
                'other_allowances' => 25000,
                'total_salary' => 595000,
                'payment_method' => 'cash',
                'bank_account_number' => null,
                'bank_name' => null,
            ],
            [
                'name_ar' => 'محمود سعيد أحمد',
                'name_en' => 'Mahmoud Saeed Ahmed',
                'nationality' => 'syrian',
                'date_of_birth' => '1988-12-10',
                'gender' => 'male',
                'marital_status' => 'married',
                'national_id' => '12345678903',
                'place_of_registration' => 'اللاذقية',
                'phone' => '+963944345678',
                'email' => 'mahmoud@example.com',
                'current_address' => 'اللاذقية، الميناء',
                'emergency_contact_name' => 'سعيد أحمد',
                'emergency_contact_phone' => '+963944876543',
                'department_id' => 3,
                'job_title_id' => null,
                'direct_manager_id' => null,
                'project_id' => null,
                'contract_type' => 'full_time',
                'employment_status' => 'active',
                'hire_date' => '2019-06-15',
                'probation_period' => 90,
                'contract_start_date' => '2019-06-15',
                'contract_end_date' => '2024-06-15',
                'basic_salary' => 550000,
                'housing_allowance' => 120000,
                'transportation_allowance' => 60000,
                'other_allowances' => 40000,
                'total_salary' => 770000,
                'payment_method' => 'bank_transfer',
                'bank_account_number' => 'SY2345678901',
                'bank_name' => 'بنك البحر المتوسط',
            ],
            [
                'name_ar' => 'ليلى يوسف علي',
                'name_en' => 'Layla Youssef Ali',
                'nationality' => 'syrian',
                'date_of_birth' => '1995-03-25',
                'gender' => 'female',
                'marital_status' => 'single',
                'national_id' => '12345678904',
                'place_of_registration' => 'حمص',
                'phone' => '+963944456789',
                'email' => 'layla@example.com',
                'current_address' => 'حمص، الحمرا',
                'emergency_contact_name' => 'يوسف علي',
                'emergency_contact_phone' => '+963944987654',
                'department_id' => 4,
                'job_title_id' => null,
                'direct_manager_id' => null,
                'project_id' => null,
                'contract_type' => 'full_time',
                'employment_status' => 'active',
                'hire_date' => '2022-02-01',
                'probation_period' => 90,
                'contract_start_date' => '2022-02-01',
                'contract_end_date' => '2027-02-01',
                'basic_salary' => 400000,
                'housing_allowance' => 70000,
                'transportation_allowance' => 35000,
                'other_allowances' => 20000,
                'total_salary' => 525000,
                'payment_method' => 'cash',
                'bank_account_number' => null,
                'bank_name' => null,
            ],
            [
                'name_ar' => 'عمر فاروق محمد',
                'name_en' => 'Omar Farouk Mohammed',
                'nationality' => 'syrian',
                'date_of_birth' => '1991-07-30',
                'gender' => 'male',
                'marital_status' => 'married',
                'national_id' => '12345678905',
                'place_of_registration' => 'درعا',
                'phone' => '+963944567890',
                'email' => 'omar@example.com',
                'current_address' => 'دمشق، الشعلان',
                'emergency_contact_name' => 'فاروق محمد',
                'emergency_contact_phone' => '+963944098765',
                'department_id' => 5,
                'job_title_id' => null,
                'direct_manager_id' => null,
                'project_id' => null,
                'contract_type' => 'full_time',
                'employment_status' => 'active',
                'hire_date' => '2020-09-01',
                'probation_period' => 90,
                'contract_start_date' => '2020-09-01',
                'contract_end_date' => '2025-09-01',
                'basic_salary' => 480000,
                'housing_allowance' => 90000,
                'transportation_allowance' => 45000,
                'other_allowances' => 28000,
                'total_salary' => 643000,
                'payment_method' => 'bank_transfer',
                'bank_account_number' => 'SY3456789012',
                'bank_name' => 'بنك التجاري',
            ],
        ];

        foreach ($employees as $emp) {
            Employee::create($emp);
        }

        // تحديث مديري الأقسام
        Department::find(1)->update(['manager_id' => 1]);
        Department::find(2)->update(['manager_id' => 2]);
        Department::find(3)->update(['manager_id' => 3]);
        Department::find(4)->update(['manager_id' => 4]);
        Department::find(5)->update(['manager_id' => 5]);
    }
}
