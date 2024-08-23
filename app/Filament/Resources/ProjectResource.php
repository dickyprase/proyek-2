<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProjectResource\Pages;
use App\Filament\Resources\ProjectResource\RelationManagers;
use App\Models\Project;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Njxqlus\FilamentProgressbar\FilamentProgressbarPlugin;
use App\Tables\Columns\ProgressColumn; 
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use Illuminate\Support\Facades\Auth;

class ProjectResource extends Resource implements HasShieldPermissions
{
    protected static ?string $model = Project::class;

    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';

    protected static ?string $navigationGroup = 'Management';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        $isMandor = Auth::user()->hasRole('mandor');

        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255)
                    ->disabled($isMandor),
                Forms\Components\Select::make('user_id')
                    ->required()
                    ->relationship('user','name')
                    ->label('User')
                    ->disabled($isMandor),
                Forms\Components\Textarea::make('description')
                    ->required()
                    ->columnSpanFull()
                    ->disabled($isMandor),
                Forms\Components\TextInput::make('budget')
                    ->required()
                    ->numeric()
                    ->disabled($isMandor),
                Forms\Components\TextInput::make('completion')
                    ->required()
                    ->numeric()
                    ->minValue(1)
                    ->maxValue(100)
                    ->label('Proggress'),
                Forms\Components\DatePicker::make('start_date')
                    ->required()
                    ->disabled($isMandor),
                Forms\Components\DatePicker::make('end_date')
                    ->required()
                    ->disabled($isMandor),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function (Builder $query) {
                $isMandor = Auth::user()->hasRole('mandor');
                if ($isMandor) {
                    $userId = Auth::user()->id;
                    $query->where('user_id', $userId);
                }
            })
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('completion')
                    ->label('Proggress')
                    ->searchable()
                    ->alignCenter()
                    ->formatStateUsing(fn(string $state): string => __($state . '%')),
                Tables\Columns\TextColumn::make('budget')
                    ->numeric()
                    ->sortable()
                    ->alignCenter()
                    ->money('Rp.', true),
                Tables\Columns\TextColumn::make('start_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('end_date')
                    ->date()
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
                //
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                ]),
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
            'index' => Pages\ListProjects::route('/'),
            'create' => Pages\CreateProject::route('/create'),
            'edit' => Pages\EditProject::route('/{record}/edit'),
        ];
    }

    public static function getPermissionPrefixes(): array
    {
        return [
            'view',
            'view_any',
            'create',
            'update',
            'delete',
            'delete_any'
        ];
    }
    
}
