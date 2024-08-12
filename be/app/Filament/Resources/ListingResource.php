<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ListingResource\Pages;
use App\Filament\Resources\ListingResource\RelationManagers;
use App\Models\Listing;
use Filament\Forms;
use Filament\Forms\Components\Card;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Support\Enums\FontWeight;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;

class ListingResource extends Resource
{
    protected static ?string $model = Listing::class;

    protected static ?string $navigationIcon = 'heroicon-o-home-modern';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Card::make()->schema([
                    Forms\Components\TextInput::make('title')
                        ->required()
                        ->unique(ignoreRecord: true)
                        ->live(onBlur: true)
                        ->afterStateUpdated(fn(Set $set, ?string $state) => $set('slug', Str::slug($state)))
                        ->maxLength(255),
                    Forms\Components\TextInput::make('slug')
                        ->disabled(),
                    Forms\Components\RichEditor::make('description')
                        ->required()
                        ->columnSpanFull(),
                    Forms\Components\TextInput::make('address')
                        ->required()
                        ->maxLength(255),
                    Forms\Components\TextInput::make('sqft')
                        ->required()
                        ->numeric()
                        ->default(0),
                    Forms\Components\TextInput::make('wifi_speed')
                        ->required()
                        ->numeric()
                        ->default(0),
                    Forms\Components\TextInput::make('max_person')
                        ->required()
                        ->numeric()
                        ->default(0),
                    Forms\Components\TextInput::make('price_per_day')
                        ->required()
                        ->numeric()
                        ->default(0),
                    Forms\Components\Checkbox
                        ::make('full_support_available')
                        ->default(0),
                    Forms\Components\Checkbox
                        ::make('gym_area_available')
                        ->default(0),
                    Forms\Components\Checkbox
                        ::make('mini_cafe_available')
                        ->default(0),
                    Forms\Components\Checkbox
                        ::make('cinema_available')
                        ->default(0),
                    Forms\Components\FileUpload::make('attachment')
                        ->directory('listings')
                        ->image()
                        ->openable(0)
                        ->multiple()
                        ->reorderable()
                        ->appendFiles(),
                ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->weight(FontWeight::Bold)
                    ->searchable(),
                Tables\Columns\TextColumn::make('sqft')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('wifi_speed')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('max_person')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('price_per_day')
                    ->numeric()
                    ->weight(FontWeight::Bold)
                    ->money('USD')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\ForceDeleteAction::make(),
                Tables\Actions\RestoreAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageListings::route('/'),
        ];
    }
}
