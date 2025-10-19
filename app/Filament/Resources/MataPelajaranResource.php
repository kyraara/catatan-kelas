<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MataPelajaranResource\Pages;
use App\Filament\Resources\MataPelajaranResource\RelationManagers;
use App\Models\MataPelajaran;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class MataPelajaranResource extends Resource
{
    public static ?string $navigationGroup = 'Master Data';
    protected static ?string $model = MataPelajaran::class;
    public static ?int $navigationSort = 1;
    public static ?string $navigationLabel = 'Mapel';
    public static ?string $pluralModelLabel = 'Data Mapel';

    protected static ?string $navigationIcon = 'heroicon-o-book-open';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nama')
                    ->label('Nama Mapel')
                    ->live()
                    ->afterStateUpdated(function ($state, callable $set) {
                        if ($state) {
                            $kode = \App\Models\MataPelajaran::generateKodeMapel($state);
                            $set('kode_mapel', $kode);
                        }
                    })
                    ->required(),

                Forms\Components\TextInput::make('kode_mapel')
                    ->label('Kode Mapel')
                    ->disabled()
                    ->dehydrated(),


            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
                Tables\Columns\TextColumn::make('nama')->label('Nama Mata Pelajaran')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('kode_mapel')->label('Kode Mapel'),
            ])
            ->filters([
                //
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
            'index' => Pages\ListMataPelajarans::route('/'),
            'create' => Pages\CreateMataPelajaran::route('/create'),
            'edit' => Pages\EditMataPelajaran::route('/{record}/edit'),
        ];
    }
}
