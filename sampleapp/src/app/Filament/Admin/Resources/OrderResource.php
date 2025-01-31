<?php

namespace App\Filament\Admin\Resources;

use Filament\Forms;
use App\Models\User;
use Filament\Tables;
use App\Models\Order;
use App\Models\Paket;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use App\Filament\Admin\Resources\OrderResource\Pages;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-ticket';

    protected static ?string $navigationGroup = 'Order Ticket';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // Admin yang menginput data, jadi field user_id akan diganti user_id
                Forms\Components\Select::make('user_id')
                    ->label('Admin')
                    ->options(User::all()->pluck('name', 'id'))  // Menampilkan list admin yang terdaftar
                    ->required()
                    ->searchable(),

                // Pilihan Paket Tiket
                Forms\Components\Select::make('paket_id')
                    ->label('Paket Tiket')
                    ->options(Paket::all()->pluck('jenis_paket', 'id'))  // Menampilkan paket tiket
                    ->required()
                    ->reactive()  // Menambahkan reactivity pada field
                    ->afterStateUpdated(function (callable $set, $state) {
                        $paket = Paket::find($state);
                        if ($paket) {
                            // Mengambil harga dari paket yang dipilih
                            $set('harga', $paket->harga);  // Set harga berdasarkan paket
                        }
                    }),

                // Nama Pelanggan
                Forms\Components\TextInput::make('nama_pelanggan')
                    ->label('Nama Pelanggan')
                    ->maxLength(255)
                    ->required(),

                // Total Harga otomatis
                Forms\Components\TextInput::make('total_harga')
                    ->label('Total Harga')
                    ->disabled()  // Total harga hanya akan ditampilkan, tidak bisa diedit
                    ->default(0)  // Default awal total harga adalah 0
                    ->afterStateUpdated(function (callable $set, $state) {
                        $paket = Paket::find($state);
                        if ($paket) {
                            $set('total_harga', $paket->harga);  // Menghitung harga berdasarkan paket yang dipilih
                        }
                    }),

                // Opsional: Tambahkan informasi tambahan atau tanggal pemesanan jika diperlukan
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user_id')
                    ->label('Admin')
                    ->sortable()
                    ->searchable()
                    ->getStateUsing(function ($record) {
                        return $record->user->name ?? '';  // Menampilkan nama admin yang terkait
                    }),

                Tables\Columns\TextColumn::make('paket_id')
                    ->label('Paket Tiket')
                    ->sortable()
                    ->getStateUsing(function ($record) {
                        return $record->paket->jenis_paket ?? '';  // Menampilkan jenis paket tiket
                    }),

                Tables\Columns\TextColumn::make('nama_pelanggan')
                    ->label('Nama Pelanggan')
                    ->searchable(),

                Tables\Columns\TextColumn::make('total_harga')
                    ->label('Total Harga'),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([ /* Filter can be added later */ ])
            ->actions([
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }
}
