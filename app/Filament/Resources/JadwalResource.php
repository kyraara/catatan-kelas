<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Jadwal;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\JadwalResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\JadwalResource\RelationManagers;
use pxlrbt\FilamentExcel\Actions\Tables\ExportAction;


class JadwalResource extends Resource
{
    public static ?string $navigationGroup = 'Jadwal & Catatan';
    public static ?int $navigationSort = 10;
    protected static ?string $model = Jadwal::class;
    public static ?string $navigationLabel = 'Jadwal';
    public static ?string $pluralModelLabel = 'Daftar Jadwal';

    protected static ?string $navigationIcon = 'heroicon-o-calendar';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('kelas_id')->relationship('kelas', 'nama')->required(),
                Forms\Components\TextInput::make('jam_ke')->numeric()->required(),
                Forms\Components\Select::make('mapel_id')->relationship('mataPelajaran', 'nama')->required(),
                Forms\Components\Select::make('guru_id')
                    ->options(\App\Models\User::role('guru')->pluck('name', 'id'))
                    ->live()
                    ->afterStateUpdated(function ($state, callable $set) {
                        $guru = \App\Models\User::find($state);
                        $set('kode_guru', $guru?->kode_guru ?: $guru?->nip); // pilih field sesuai DB
                    })
                    ->searchable()
                    ->required(),
                Forms\Components\TextInput::make('kode_guru')
                    ->label('Kode Guru')
                    ->disabled()
                    ->dehydrated(),
                Forms\Components\Select::make('hari')
                    ->options([
                        'Senin' => 'Senin',
                        'Selasa' => 'Selasa',
                        'Rabu' => 'Rabu',
                        'Kamis' => 'Kamis',
                        'Jumat' => 'Jumat',
                        'Sabtu' => 'Sabtu',
                    ])
                    ->required(),
                Forms\Components\Textarea::make('catatan')->label('Catatan/Jadwal Khusus')->rows(2)->nullable(),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('kelas.nama')->label('Kelas')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('jam_ke')->label('Jam Ke')->sortable(),
                Tables\Columns\TextColumn::make('mataPelajaran.nama')->label('Mata Pelajaran')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('guru.name')->label('Guru')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('kode_guru')->label('Kode Guru'),
                Tables\Columns\TextColumn::make('hari')->label('Hari')->sortable(),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->label('Dibuat Pada')->sortable(),
                Tables\Columns\TextColumn::make('updated_at')->dateTime()->label('Diubah Pada')->sortable(),
            ])

            ->filters([
                SelectFilter::make('kelas_id')
                    ->relationship('kelas', 'nama')->label('Kelas'),

                SelectFilter::make('hari')
                    ->options([
                        'Senin' => 'Senin',
                        'Selasa' => 'Selasa',
                        'Rabu' => 'Rabu',
                        'Kamis' => 'Kamis',
                        'Jumat' => 'Jumat',
                        'Sabtu' => 'Sabtu',
                    ]),

                SelectFilter::make('guru_id')
                    ->relationship('guru', 'name')->label('Guru'),

                SelectFilter::make('mapel_id')
                    ->relationship('mataPelajaran', 'nama')->label('Mapel'),
            ])
            ->actions([
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
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListJadwals::route('/'),
            'create' => Pages\CreateJadwal::route('/create'),
            'edit' => Pages\EditJadwal::route('/{record}/edit'),
        ];
    }
}
