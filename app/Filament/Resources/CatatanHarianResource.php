<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\CatatanHarian;
use Filament\Resources\Resource;
use Filament\Support\Enums\Alignment;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\Placeholder;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\DatePicker;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\CatatanHarianResource\Pages;
use Filament\Tables\Actions\Action;
use App\Filament\Resources\CatatanHarianResource\RelationManagers;

class CatatanHarianResource extends Resource
{
    public static ?string $navigationGroup = 'Jadwal & Catatan';
    public static ?int $navigationSort = 10;
    protected static ?string $model = CatatanHarian::class;
    public static ?string $navigationLabel = 'Catatan Harian';
    public static ?string $pluralModelLabel = 'Daftar Catatan Harian';

    public static ?string $navigationIcon = 'heroicon-o-document-text';


    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Hidden::make('user_id')
                ->default(fn() => auth()->id()),
            Forms\Components\Select::make('jadwal_id')
                ->label('Jadwal Kelas/Mapel/Guru')
                ->options(
                    \App\Models\Jadwal::with(['kelas', 'mataPelajaran', 'guru'])->get()
                        ->mapWithKeys(fn($j) => [
                            $j->id => $j->kelas->nama . ' - ' . $j->mataPelajaran->nama . ' (' . $j->guru->name . ')'
                        ])
                )
                ->searchable()
                ->required(),
            Forms\Components\DatePicker::make('tanggal')->label('Tanggal')->required()->default(now()),
            Forms\Components\Textarea::make('materi')->label('Materi')->rows(2)->nullable(),
            Forms\Components\Textarea::make('murid_tidak_hadir')->label('Murid Tidak Hadir')->rows(2)->nullable(),
            Forms\Components\Textarea::make('jam_kosong')->label('Jam Kosong')->rows(2)->nullable(),
            Forms\Components\Textarea::make('catatan')->label('Catatan')->rows(3)->nullable(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('tanggal')->label('Tanggal')->sortable()->searchable(),
            TextColumn::make('jadwal.kelas.nama')->label('Kelas')->searchable(),
            TextColumn::make('jadwal.hari')->label('Hari')->searchable(),
            TextColumn::make('jadwal.jam_ke')->searchable(),
            TextColumn::make('jadwal.mataPelajaran.nama')->label('Mapel')->searchable(),
            TextColumn::make('jadwal.guru.name')->label('Guru')->searchable(),
            TextColumn::make('materi')->label('Materi')->limit(40)->searchable(),
            TextColumn::make('catatan')->label('Catatan')->limit(30)->searchable(),
            TextColumn::make('approved_at')
                ->label('Status')
                ->formatStateUsing(fn($state) => $state ? '✔ Approved' : 'Pending')
                ->badge()
                ->color(fn($state) => $state ? 'success' : 'warning'),
        ])
            ->filters([

                SelectFilter::make('kelas')
                    ->label('Kelas')
                    ->relationship('jadwal.kelas', 'nama'),

                SelectFilter::make('mapel')
                    ->label('Mapel')
                    ->relationship('jadwal.mataPelajaran', 'nama'),

                SelectFilter::make('guru')
                    ->label('Guru')
                    ->relationship('jadwal.guru', 'name'),

                Filter::make('tanggal')
                    ->form([
                        DatePicker::make('from')->label('Mulai'),
                        DatePicker::make('to')->label('Sampai'),
                    ])
                    ->query(function (Builder $query, array $data) {
                        return $query
                            ->when($data['from'], fn($q) => $q->where('tanggal', '>=', $data['from']))
                            ->when($data['to'], fn($q) => $q->where('tanggal', '<=', $data['to']));
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->modalHeading('Detail Catatan Harian')
                    ->modalButton('Tutup')
                    ->record(
                        fn($record) => $record->load(['jadwal.kelas', 'jadwal.mataPelajaran', 'jadwal.guru', 'user'])
                    ),
                Tables\Actions\EditAction::make()
                    ->visible(fn($record) => auth()->user()->hasRole('admin')
                        || (auth()->user()->hasRole('guru') && $record->user_id == auth()->id())
                        || (auth()->user()->hasRole('wali_kelas') &&
                            $record->jadwal->kelas->wali_kelas_id == auth()->id())),
                Action::make('approve')
                    ->label('Approve')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(
                        fn($record) =>
                        auth()->user()->hasRole('admin') ||
                            (
                                auth()->user()->hasRole('wali_kelas')
                                && $record->jadwal->kelas->wali_kelas_id == auth()->id()
                            )
                            && !$record->approved_at
                    )
                    ->requiresConfirmation()
                    ->action(fn($record) => $record->update([
                        'approved_at' => now(),
                        'approved_by' => auth()->id(),
                    ]))
                    ->disabled(fn($record) => $record->approved_at !== null)

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

    public static function getViewRecordLayout(): array
    {
        return [
            Section::make('Detail Catatan Harian')
                ->description('Informasi lengkap setiap catatan harian.')
                ->schema([
                    Placeholder::make('jadwal.kelas.nama')->label('Kelas'),
                    Placeholder::make('jadwal.hari')->label('Hari'),
                    Placeholder::make('jadwal.jam_ke')->label('Jam Ke'),
                    Placeholder::make('jadwal.mataPelajaran.nama')->label('Mapel'),
                    Placeholder::make('jadwal.guru.name')->label('Guru'),
                    Placeholder::make('tanggal')->label('Tanggal'),

                    Textarea::make('materi')->label('Materi')->disabled(),
                    Textarea::make('catatan')->label('Catatan')->disabled(),
                    Textarea::make('murid_tidak_hadir')->label('Murid Tidak Hadir')->disabled(),
                    Textarea::make('jam_kosong')->label('Jam Kosong')->disabled(),

                    Placeholder::make('user.name')->label('Diinput oleh'),
                    Placeholder::make('updated_at')->label('Update Terakhir')->content(fn($record) => $record->updated_at->format('d-M Y H:i')),
                ])->alignment(Alignment::Left),
        ];
    }
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCatatanHarians::route('/'),
            'create' => Pages\CreateCatatanHarian::route('/create'),
            'edit' => Pages\EditCatatanHarian::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $user = auth()->user();

        if ($user->hasRole('admin')) {
            return parent::getEloquentQuery();
        }

        $kelasIdList = $user->kelas->pluck('id')->toArray();
        $isGuru = $user->hasRole('guru');
        $isWaliKelas = $user->hasRole('wali_kelas');

        return parent::getEloquentQuery()
            ->where(function ($q) use ($isGuru, $isWaliKelas, $user, $kelasIdList) {
                if ($isGuru) {
                    $q->orWhere('user_id', $user->id);
                }
                if ($isWaliKelas && count($kelasIdList) > 0) {
                    $q->orWhereHas('jadwal', fn($jadwal) => $jadwal->whereIn('kelas_id', $kelasIdList));
                }
            });
    }
}
