<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class ResetTwoFactor extends Command
{
    protected $signature = '2fa:reset {user : اسم المستخدم أو البريد الإلكتروني}';

    protected $description = 'تعطيل المصادقة الثنائية لمستخدم (لحالات فقدان الجهاز ورموز الاسترداد)';

    public function handle(): int
    {
        $needle = (string) $this->argument('user');

        $users = User::where('name', $needle)
            ->orWhere('email', $needle)
            ->get();

        if ($users->isEmpty()) {
            $this->error('لا يوجد مستخدم بهذا الاسم أو البريد: ' . $needle);

            return self::FAILURE;
        }

        foreach ($users as $user) {
            $user->forceFill([
                'two_factor_secret' => null,
                'two_factor_recovery_codes' => null,
                'two_factor_confirmed_at' => null,
            ])->save();

            $this->info('تم تعطيل المصادقة الثنائية للمستخدم #' . $user->id . ' — ' . $user->name);
        }

        $this->warn('سيُطلب من المستخدم تفعيل المصادقة من جديد عند الدخول.');

        return self::SUCCESS;
    }
}
