<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class FixCompanyTenants extends Command
{
    protected $signature = 'fix:company-tenants';
    protected $description = 'Create separate tenants for companies sharing the same tenant';

    public function handle()
    {
        $companies = DB::table('companies')->orderBy('id')->get();
        $tenants = DB::table('tenants')->orderBy('id')->get();

        $this->info('Companies:');
        foreach ($companies as $c) {
            $this->line("  id={$c->id} name={$c->name} tenant_id={$c->tenant_id}");
        }

        $this->info('Tenants:');
        foreach ($tenants as $t) {
            $this->line("  id={$t->id} name={$t->name}");
        }

        $grouped = $companies->groupBy('tenant_id');

        foreach ($grouped as $tenantId => $comps) {
            if ($comps->count() <= 1) continue;

            $this->warn("Tenant {$tenantId} has " . $comps->count() . " companies. Fixing...");

            foreach ($comps as $i => $company) {
                if ($i === 0) continue;

                $newTenantId = DB::table('tenants')->insertGetId([
                    'name' => $company->name,
                    'slug' => 'company-' . $company->id . '-' . uniqid(),
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                DB::table('companies')->where('id', $company->id)->update(['tenant_id' => $newTenantId]);

                $users = DB::table('users')->where('tenant_id', $tenantId)->get();
                foreach ($users as $user) {
                    $exists = DB::table('tenant_user')
                        ->where('tenant_id', $newTenantId)
                        ->where('user_id', $user->id)
                        ->exists();
                    if (!$exists) {
                        DB::table('tenant_user')->insert([
                            'tenant_id' => $newTenantId,
                            'user_id' => $user->id,
                            'role' => $user->role,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                }

                $this->info("  Company '{$company->name}' (id={$company->id}) moved to new tenant id={$newTenantId}");
            }
        }

        $this->info('Done!');
    }
}
