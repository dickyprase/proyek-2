<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TaskResource\Pages;
use App\Filament\Resources\TaskResource\RelationManagers;
use App\Models\Task;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Columns\ImageColumn;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use Illuminate\Support\Facades\Auth;
use App\Tables\Columns\ProgressColumn; 

class TaskResource extends Resource implements HasShieldPermissions
{
    protected static ?string $model = Task::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Management';

    protected static ?int $navigationSort = 2;

    protected static ?string $navigationLabel = 'Sub-Pekerjaan';

    protected static ?string $breadcrumb = "Sub-Pekerjaan";

    public static function form(Form $form): Form
    {
        $isAdmin = Auth::user()->hasRole('super_admin');
        return $form
            ->schema([
                Forms\Components\Select::make('project_id')
                    ->relationship('project', 'name')
                    ->required()
                    ->label('Nama Proyek'),
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255)
                    ->label('Nama Sub-Pekerjaan'),
                Forms\Components\Textarea::make('description')
                    ->required()
                    ->columnSpanFull()
                    ->label('Deskripsi'),
                Forms\Components\TextInput::make('completion')
                    ->required()
                    ->numeric()
                    ->minValue(0)
                    ->maxValue(100)
                    ->label('Proggress')
                    ->default('0')
                    ->hidden($isAdmin),
                Forms\Components\DatePicker::make('start_date')
                    ->required(),
                Forms\Components\DatePicker::make('end_date')
                    ->required(),
                Forms\Components\FileUpload::make('image')
                    ->directory('task')
                    ->image()
                    ->hidden($isAdmin),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function (Builder $query) {
                $isMandor = Auth::user()->hasRole('mandor');
                if ($isMandor) {
                    $userId = Auth::user()->id;
            
                    $query->whereHas('project', function (Builder $query) use ($userId) {
                        $query->where('user_id', $userId);
                    });
                }
            })
            ->columns([
                Tables\Columns\TextColumn::make('project.name')
                    ->searchable()
                    ->label('Nama Proyek'),
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->label('Nama'),
                // Tables\Columns\TextColumn::make('completion')
                //     ->label('Proggress')
                //     ->searchable()
                //     ->alignCenter()
                //     ->formatStateUsing(fn(string $state): string => __($state . '%')),
                ProgressColumn::make('completion'), 
                Tables\Columns\TextColumn::make('start_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('end_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\ImageColumn::make('image')
                    ->label('Dokumentasi'),
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
            'index' => Pages\ListTasks::route('/'),
            'create' => Pages\CreateTask::route('/create'),
            'edit' => Pages\EditTask::route('/{record}/edit'),
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
