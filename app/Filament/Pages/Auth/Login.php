<?php

namespace App\Filament\Pages\Auth;

use App\Models\User;
use Filament\Auth\Http\Responses\Contracts\LoginResponse;
use Filament\Auth\Pages\Login as BaseLogin;
use Filament\Facades\Filament;
use Filament\Notifications\Notification;
use Illuminate\Validation\ValidationException;

class Login extends BaseLogin
{
    protected string $view = 'filament.pages.auth.login';

    /**
     * @return array<int, array{key: string, label: string, role_label: string, email: string, password: string, hint: string}>
     */
    public function getDemoAccounts(): array
    {
        return [
            [
                'key' => 'super_admin',
                'label' => 'Super Admin',
                'role_label' => 'Owner / Penuh',
                'email' => 'superadmin@fumiko.com',
                'password' => 'password123',
                'hint' => 'Akses seluruh modul, gudang, approval & master data',
            ],
            [
                'key' => 'admin',
                'label' => 'Admin Gudang',
                'role_label' => 'Operasional',
                'email' => 'admin@fumiko.com',
                'password' => 'password123',
                'hint' => 'Barang keluar, stock opname, uang kas & surat jalan',
            ],
            [
                'key' => 'sales',
                'label' => 'Sales',
                'role_label' => 'Penjualan',
                'email' => 'sales@fumiko.com',
                'password' => 'password123',
                'hint' => 'Input pesanan barang keluar & cek tagihan customer',
            ],
        ];
    }

    public function loginAsDemo(string $roleKey): ?LoginResponse
    {
        $account = collect($this->getDemoAccounts())->firstWhere('key', $roleKey);
        if (! $account) {
            throw ValidationException::withMessages([
                'data.email' => 'Role demo tidak dikenali.',
            ]);
        }

        $targetRole = $roleKey === 'sales' ? 'user' : $roleKey;

        $user = User::where('email', $account['email'])->first()
            ?? User::where('role', $targetRole)->orderBy('id')->first();

        if (! $user) {
            Notification::make()
                ->title('Akun demo belum tersedia')
                ->body('Jalankan seeder demo terlebih dahulu untuk membuat akun contoh.')
                ->danger()
                ->send();

            return null;
        }

        Filament::auth()->login($user, true);
        session()->regenerate();

        return app(LoginResponse::class);
    }
}
