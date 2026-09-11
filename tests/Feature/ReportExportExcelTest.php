<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportExportExcelTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_export_daily_report_to_excel(): void
    {
        $user = User::factory()->create();
        $customer = Customer::create(['name' => 'Budi Customer', 'category' => 'Loyal']);

        Transaction::create([
            'invoice_number' => 'INV-20260911-0001',
            'customer_id' => $customer->id,
            'user_id' => $user->id,
            'subtotal' => 50000,
            'total' => 50000,
            'payment_amount' => 50000,
            'payment_method' => 'cash',
            'status' => 'completed',
        ]);

        $res = $this->actingAs($user)->get(route('reports.export-excel', [
            'period_type' => 'daily',
            'date' => today()->toDateString(),
        ]));

        $res->assertStatus(200);
        $this->assertStringContainsString('application/vnd.ms-excel', $res->headers->get('Content-Type'));
        $this->assertStringContainsString('laporan-penjualan-harian-', $res->headers->get('Content-Disposition'));
        $res->assertSee('INV-20260911-0001');
        $res->assertSee('Budi Customer');
        $res->assertSee('Loyal');
    }

    public function test_can_export_weekly_report_to_excel(): void
    {
        $user = User::factory()->create();

        $res = $this->actingAs($user)->get(route('reports.export-excel', [
            'period_type' => 'weekly',
            'start_date' => today()->subDays(6)->toDateString(),
            'end_date' => today()->toDateString(),
        ]));

        $res->assertStatus(200);
        $this->assertStringContainsString('laporan-penjualan-mingguan-', $res->headers->get('Content-Disposition'));
    }

    public function test_can_export_monthly_report_to_excel(): void
    {
        $user = User::factory()->create();

        $res = $this->actingAs($user)->get(route('reports.export-excel', [
            'period_type' => 'monthly',
            'month' => today()->format('Y-m'),
        ]));

        $res->assertStatus(200);
        $this->assertStringContainsString('laporan-penjualan-bulanan-', $res->headers->get('Content-Disposition'));
    }

    public function test_can_export_custom_period_report_to_excel(): void
    {
        $user = User::factory()->create();

        $res = $this->actingAs($user)->get(route('reports.export-excel', [
            'period_type' => 'custom',
            'start_date' => today()->startOfMonth()->toDateString(),
            'end_date' => today()->toDateString(),
        ]));

        $res->assertStatus(200);
        $this->assertStringContainsString('laporan-penjualan-periode-', $res->headers->get('Content-Disposition'));
    }
}
