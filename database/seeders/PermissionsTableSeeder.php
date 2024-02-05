<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionsTableSeeder extends Seeder
{
    public function run()
    {
        $permissions = [
            [
                'id'    => 1,
                'title' => 'user_management_access',
            ],
            [
                'id'    => 2,
                'title' => 'permission_create',
            ],
            [
                'id'    => 3,
                'title' => 'permission_edit',
            ],
            [
                'id'    => 4,
                'title' => 'permission_show',
            ],
            [
                'id'    => 5,
                'title' => 'permission_delete',
            ],
            [
                'id'    => 6,
                'title' => 'permission_access',
            ],
            [
                'id'    => 7,
                'title' => 'role_create',
            ],
            [
                'id'    => 8,
                'title' => 'role_edit',
            ],
            [
                'id'    => 9,
                'title' => 'role_show',
            ],
            [
                'id'    => 10,
                'title' => 'role_delete',
            ],
            [
                'id'    => 11,
                'title' => 'role_access',
            ],
            [
                'id'    => 12,
                'title' => 'user_create',
            ],
            [
                'id'    => 13,
                'title' => 'user_edit',
            ],
            [
                'id'    => 14,
                'title' => 'user_show',
            ],
            [
                'id'    => 15,
                'title' => 'user_delete',
            ],
            [
                'id'    => 16,
                'title' => 'user_access',
            ],
            [
                'id'    => 17,
                'title' => 'content_management_access',
            ],
            [
                'id'    => 18,
                'title' => 'content_category_create',
            ],
            [
                'id'    => 19,
                'title' => 'content_category_edit',
            ],
            [
                'id'    => 20,
                'title' => 'content_category_show',
            ],
            [
                'id'    => 21,
                'title' => 'content_category_delete',
            ],
            [
                'id'    => 22,
                'title' => 'content_category_access',
            ],
            [
                'id'    => 23,
                'title' => 'content_tag_create',
            ],
            [
                'id'    => 24,
                'title' => 'content_tag_edit',
            ],
            [
                'id'    => 25,
                'title' => 'content_tag_show',
            ],
            [
                'id'    => 26,
                'title' => 'content_tag_delete',
            ],
            [
                'id'    => 27,
                'title' => 'content_tag_access',
            ],
            [
                'id'    => 28,
                'title' => 'content_page_create',
            ],
            [
                'id'    => 29,
                'title' => 'content_page_edit',
            ],
            [
                'id'    => 30,
                'title' => 'content_page_show',
            ],
            [
                'id'    => 31,
                'title' => 'content_page_delete',
            ],
            [
                'id'    => 32,
                'title' => 'content_page_access',
            ],
            [
                'id'    => 33,
                'title' => 'faq_management_access',
            ],
            [
                'id'    => 34,
                'title' => 'faq_category_create',
            ],
            [
                'id'    => 35,
                'title' => 'faq_category_edit',
            ],
            [
                'id'    => 36,
                'title' => 'faq_category_show',
            ],
            [
                'id'    => 37,
                'title' => 'faq_category_delete',
            ],
            [
                'id'    => 38,
                'title' => 'faq_category_access',
            ],
            [
                'id'    => 39,
                'title' => 'faq_question_create',
            ],
            [
                'id'    => 40,
                'title' => 'faq_question_edit',
            ],
            [
                'id'    => 41,
                'title' => 'faq_question_show',
            ],
            [
                'id'    => 42,
                'title' => 'faq_question_delete',
            ],
            [
                'id'    => 43,
                'title' => 'faq_question_access',
            ],
            [
                'id'    => 44,
                'title' => 'clients_menu_access',
            ],
            [
                'id'    => 45,
                'title' => 'client_create',
            ],
            [
                'id'    => 46,
                'title' => 'client_edit',
            ],
            [
                'id'    => 47,
                'title' => 'client_show',
            ],
            [
                'id'    => 48,
                'title' => 'client_delete',
            ],
            [
                'id'    => 49,
                'title' => 'client_access',
            ],
            [
                'id'    => 50,
                'title' => 'country_create',
            ],
            [
                'id'    => 51,
                'title' => 'country_edit',
            ],
            [
                'id'    => 52,
                'title' => 'country_show',
            ],
            [
                'id'    => 53,
                'title' => 'country_delete',
            ],
            [
                'id'    => 54,
                'title' => 'country_access',
            ],
            [
                'id'    => 55,
                'title' => 'company_create',
            ],
            [
                'id'    => 56,
                'title' => 'company_edit',
            ],
            [
                'id'    => 57,
                'title' => 'company_show',
            ],
            [
                'id'    => 58,
                'title' => 'company_delete',
            ],
            [
                'id'    => 59,
                'title' => 'company_access',
            ],
            [
                'id'    => 60,
                'title' => 'payment_create',
            ],
            [
                'id'    => 61,
                'title' => 'payment_edit',
            ],
            [
                'id'    => 62,
                'title' => 'payment_show',
            ],
            [
                'id'    => 63,
                'title' => 'payment_delete',
            ],
            [
                'id'    => 64,
                'title' => 'payment_access',
            ],
            [
                'id'    => 65,
                'title' => 'spot_create',
            ],
            [
                'id'    => 66,
                'title' => 'spot_edit',
            ],
            [
                'id'    => 67,
                'title' => 'spot_show',
            ],
            [
                'id'    => 68,
                'title' => 'spot_delete',
            ],
            [
                'id'    => 69,
                'title' => 'spot_access',
            ],
            [
                'id'    => 70,
                'title' => 'slot_create',
            ],
            [
                'id'    => 71,
                'title' => 'slot_edit',
            ],
            [
                'id'    => 72,
                'title' => 'slot_show',
            ],
            [
                'id'    => 73,
                'title' => 'slot_delete',
            ],
            [
                'id'    => 74,
                'title' => 'slot_access',
            ],
            [
                'id'    => 75,
                'title' => 'profile_password_edit',
            ],
        ];

        Permission::insert($permissions);
    }
}
