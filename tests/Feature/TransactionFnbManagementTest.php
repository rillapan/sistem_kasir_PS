<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Device;
use App\Models\Playstation;
use App\Models\Transaction;
use App\Models\Fnb;
use App\Models\TransactionFnb;
use App\Models\StockMutation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;

class TransactionFnbManagementTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $device;
    protected $playstation;
    protected $fnb1;
    protected $fnb2;
    protected $transaction;

    protected function setUp(): void
    {
        parent::setUp();

        // Create admin user
        $this->admin = User::factory()->create([
            'role' => 'admin',
            'email' => 'admin@test.com'
        ]);

        // Create playstation
        $this->playstation = Playstation::create([
            'nama' => 'PS5',
            'harga' => 10000
        ]);

        // Create device
        $this->device = Device::create([
            'nama' => 'PS5-01',
            'playstation_id' => $this->playstation->id,
            'status' => 'Tersedia'
        ]);

        // Create FnB items
        $this->fnb1 = Fnb::create([
            'nama' => 'Indomie Goreng',
            'harga_beli' => 3000,
            'harga_jual' => 5000,
            'stok' => 10
        ]);

        $this->fnb2 = Fnb::create([
            'nama' => 'Aqua 600ml',
            'harga_beli' => 2000,
            'harga_jual' => 3000,
            'stok' => 20
        ]);

        // Create unpaid transaction
        $this->transaction = Transaction::create([
            'nama' => 'Test Customer',
            'device_id' => $this->device->id,
            'playstation_id' => $this->playstation->id,
            'user_id' => $this->admin->id,
            'tipe_transaksi' => 'prepaid',
            'waktu_mulai' => '10:00',
            'waktu_Selesai' => '12:00',
            'jam_main' => 2,
            'harga' => 10000,
            'total' => 20000,
            'status_transaksi' => 'sukses',
            'payment_status' => 'unpaid',
            'status' => 'aktif' // Add missing status field
        ]);
    }

    /** @test */
    public function admin_can_view_transaction_edit_page_with_fnb_management()
    {
        $response = $this->actingAs($this->admin)
            ->get(route('transaction.edit', $this->transaction->id_transaksi));

        $response->assertStatus(200);
        $response->assertViewIs('transaction.edit');
        $response->assertViewHas('fnbs');
        $response->assertSee('Kelola FnB Pesanan');
    }

    /** @test */
    public function can_add_new_fnb_to_transaction()
    {
        $response = $this->actingAs($this->admin)
            ->postJson(route('transaction.fnb.add', $this->transaction->id_transaksi), [
                'fnb_id' => $this->fnb1->id,
                'qty' => 2
            ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'message' => 'FnB berhasil ditambahkan'
        ]);

        // Check database
        $this->assertDatabaseHas('transaction_fnbs', [
            'transaction_id' => $this->transaction->id_transaksi,
            'fnb_id' => $this->fnb1->id,
            'qty' => 2
        ]);

        // Check stock decreased
        $this->assertEquals(8, $this->fnb1->fresh()->stok);

        // Check stock mutation created
        $this->assertDatabaseHas('stock_mutations', [
            'fnb_id' => $this->fnb1->id,
            'type' => 'out',
            'qty' => 2,
            'note' => 'Tambah FnB - Transaksi #' . $this->transaction->id_transaksi
        ]);
    }

    /** @test */
    public function adding_fnb_updates_transaction_total()
    {
        $originalTotal = $this->transaction->total;

        $this->actingAs($this->admin)
            ->postJson(route('transaction.fnb.add', $this->transaction->id_transaksi), [
                'fnb_id' => $this->fnb1->id,
                'qty' => 2
            ]);

        $newTotal = $this->transaction->fresh()->total;
        $expectedTotal = $originalTotal + (2 * $this->fnb1->harga_jual);

        $this->assertEquals($expectedTotal, $newTotal);
    }

    /** @test */
    public function cannot_add_fnb_with_insufficient_stock()
    {
        $response = $this->actingAs($this->admin)
            ->postJson(route('transaction.fnb.add', $this->transaction->id_transaksi), [
                'fnb_id' => $this->fnb1->id,
                'qty' => 15 // More than available stock (10)
            ]);

        $response->assertStatus(400);
        $response->assertJson([
            'success' => false
        ]);

        // Check stock unchanged
        $this->assertEquals(10, $this->fnb1->fresh()->stok);
    }

    /** @test */
    public function can_update_fnb_quantity_in_transaction()
    {
        // First add FnB
        TransactionFnb::create([
            'transaction_id' => $this->transaction->id_transaksi,
            'fnb_id' => $this->fnb1->id,
            'qty' => 2,
            'harga_jual' => $this->fnb1->harga_jual,
            'harga_beli' => $this->fnb1->harga_beli
        ]);
        $this->fnb1->update(['stok' => 8]); // Simulate stock reduction

        // Update quantity
        $response = $this->actingAs($this->admin)
            ->putJson(route('transaction.fnb.update', [
                'id' => $this->transaction->id_transaksi,
                'fnbId' => $this->fnb1->id
            ]), [
                'qty' => 5,
                'harga_jual' => $this->fnb1->harga_jual
            ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'message' => 'FnB berhasil diupdate'
        ]);

        // Check database updated
        $this->assertDatabaseHas('transaction_fnbs', [
            'transaction_id' => $this->transaction->id_transaksi,
            'fnb_id' => $this->fnb1->id,
            'qty' => 5
        ]);

        // Check stock adjusted (8 - 3 = 5)
        $this->assertEquals(5, $this->fnb1->fresh()->stok);
    }

    /** @test */
    public function can_update_fnb_price_in_transaction()
    {
        // First add FnB
        TransactionFnb::create([
            'transaction_id' => $this->transaction->id_transaksi,
            'fnb_id' => $this->fnb1->id,
            'qty' => 2,
            'harga_jual' => $this->fnb1->harga_jual,
            'harga_beli' => $this->fnb1->harga_beli
        ]);

        $newPrice = 6000;

        $response = $this->actingAs($this->admin)
            ->putJson(route('transaction.fnb.update', [
                'id' => $this->transaction->id_transaksi,
                'fnbId' => $this->fnb1->id
            ]), [
                'qty' => 2,
                'harga_jual' => $newPrice
            ]);

        $response->assertStatus(200);

        // Check price updated
        $this->assertDatabaseHas('transaction_fnbs', [
            'transaction_id' => $this->transaction->id_transaksi,
            'fnb_id' => $this->fnb1->id,
            'harga_jual' => $newPrice
        ]);
    }

    /** @test */
    public function decreasing_quantity_restores_stock()
    {
        // Add FnB with qty 5
        TransactionFnb::create([
            'transaction_id' => $this->transaction->id_transaksi,
            'fnb_id' => $this->fnb1->id,
            'qty' => 5,
            'harga_jual' => $this->fnb1->harga_jual,
            'harga_beli' => $this->fnb1->harga_beli
        ]);
        $this->fnb1->update(['stok' => 5]); // 10 - 5 = 5

        // Decrease to qty 2
        $this->actingAs($this->admin)
            ->putJson(route('transaction.fnb.update', [
                'id' => $this->transaction->id_transaksi,
                'fnbId' => $this->fnb1->id
            ]), [
                'qty' => 2,
                'harga_jual' => $this->fnb1->harga_jual
            ]);

        // Stock should increase by 3 (5 - 2 = 3 returned)
        $this->assertEquals(8, $this->fnb1->fresh()->stok);

        // Check stock mutation
        $this->assertDatabaseHas('stock_mutations', [
            'fnb_id' => $this->fnb1->id,
            'type' => 'in',
            'qty' => 3
        ]);
    }

    /** @test */
    public function can_delete_fnb_from_transaction()
    {
        // Add FnB
        TransactionFnb::create([
            'transaction_id' => $this->transaction->id_transaksi,
            'fnb_id' => $this->fnb1->id,
            'qty' => 3,
            'harga_jual' => $this->fnb1->harga_jual,
            'harga_beli' => $this->fnb1->harga_beli
        ]);
        $this->fnb1->update(['stok' => 7]); // 10 - 3

        $originalTotal = $this->transaction->total;

        $response = $this->actingAs($this->admin)
            ->deleteJson(route('transaction.fnb.delete', [
                'id' => $this->transaction->id_transaksi,
                'fnbId' => $this->fnb1->id
            ]));

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'message' => 'FnB berhasil dihapus'
        ]);

        // Check deleted from database
        $this->assertDatabaseMissing('transaction_fnbs', [
            'transaction_id' => $this->transaction->id_transaksi,
            'fnb_id' => $this->fnb1->id
        ]);

        // Check stock restored
        $this->assertEquals(10, $this->fnb1->fresh()->stok);

        // Check stock mutation
        $this->assertDatabaseHas('stock_mutations', [
            'fnb_id' => $this->fnb1->id,
            'type' => 'in',
            'qty' => 3,
            'note' => 'Hapus FnB - Transaksi #' . $this->transaction->id_transaksi
        ]);
    }

    /** @test */
    public function deleting_fnb_updates_transaction_total()
    {
        TransactionFnb::create([
            'transaction_id' => $this->transaction->id_transaksi,
            'fnb_id' => $this->fnb1->id,
            'qty' => 2,
            'harga_jual' => $this->fnb1->harga_jual,
            'harga_beli' => $this->fnb1->harga_beli
        ]);

        $this->transaction->update(['total' => $this->transaction->total + (2 * $this->fnb1->harga_jual)]);
        $totalWithFnb = $this->transaction->total;

        $this->actingAs($this->admin)
            ->deleteJson(route('transaction.fnb.delete', [
                'id' => $this->transaction->id_transaksi,
                'fnbId' => $this->fnb1->id
            ]));

        $newTotal = $this->transaction->fresh()->total;
        $expectedTotal = $totalWithFnb - (2 * $this->fnb1->harga_jual);

        $this->assertEquals($expectedTotal, $newTotal);
    }

    /** @test */
    public function cannot_modify_fnb_on_paid_transaction()
    {
        $this->transaction->update(['payment_status' => 'paid']);

        $response = $this->actingAs($this->admin)
            ->postJson(route('transaction.fnb.add', $this->transaction->id_transaksi), [
                'fnb_id' => $this->fnb1->id,
                'qty' => 1
            ]);

        $response->assertStatus(403);
        $response->assertJson([
            'success' => false,
            'message' => 'Hanya transaksi yang belum dibayar yang dapat diedit.'
        ]);
    }

    /** @test */
    public function unlimited_stock_fnb_works_correctly()
    {
        $unlimitedFnb = Fnb::create([
            'nama' => 'Teh Poci',
            'harga_beli' => 500,
            'harga_jual' => 1000,
            'stok' => -1 // Unlimited
        ]);

        $response = $this->actingAs($this->admin)
            ->postJson(route('transaction.fnb.add', $this->transaction->id_transaksi), [
                'fnb_id' => $unlimitedFnb->id,
                'qty' => 100 // Large quantity
            ]);

        $response->assertStatus(200);

        // Stock should remain -1
        $this->assertEquals(-1, $unlimitedFnb->fresh()->stok);

        // Transaction FnB should be created
        $this->assertDatabaseHas('transaction_fnbs', [
            'transaction_id' => $this->transaction->id_transaksi,
            'fnb_id' => $unlimitedFnb->id,
            'qty' => 100
        ]);
    }
}
