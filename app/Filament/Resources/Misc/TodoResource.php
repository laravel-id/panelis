<?php

namespace App\Filament\Resources\Misc;

use App\Filament\Resources\Misc\TodoResource\Pages;
use App\Filament\Resources\Misc\TodoResource\RelationManagers;
use App\Models\Misc\Todo;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;

class TodoResource extends Resource
{
    const Completed = 'completed';

    protected static ?string $model = Todo::class;

    protected static ?string $navigationIcon = 'heroicon-o-queue-list';
    private static array $statuses = [
        'new' => 'New',
        'in progress' => 'In progress',
        'pending' => 'Pending',
        'completed' => 'Completed',
        'archived' => 'Archived',
    ];

    private static array $priorities = [
        'low' => 'Low',
        'medium' => 'Medium',
        'high' => 'High',
    ];

    /**
     * @return string|null
     */
    public static function getNavigationGroup(): ?string
    {
        return __('Misc');
    }

    public static function getLabel(): ?string
    {
        return __('Todo');
    }

    /**
     * @return string|null
     */
    public static function getActiveNavigationIcon(): ?string
    {
        return 'heroicon-s-queue-list';
    }


    public static function getNavigationBadge(): ?string
    {
        return Todo::whereIn('status', ['new', 'pending', 'in progress'])
            ->whereHas('users', function (Builder $builder): Builder {
                return $builder->whereUserId(Auth::id());
            })
            ->count();
    }

    public static function shouldRegisterNavigation(): bool
    {
        return Auth::user()->can('View todo') || Auth::user()->can('View all todos');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label(__('Title'))
                    ->columnSpanFull()
                    ->required()
                    ->minLength(3)
                    ->maxLength(250),

                Forms\Components\Textarea::make('description')
                    ->translateLabel()
                    ->columnSpanFull()
                    ->maxLength(250),

                Forms\Components\Select::make('user_id')
                    ->label(__('Assignee'))
                    ->columnSpanFull()
                    ->relationship('users', 'name')
                    ->multiple()
                    ->searchable()
                    ->preload()
                    ->required(),

                Forms\Components\Select::make('status')
                    ->translateLabel()
                    ->default('new')
                    ->options(
                        collect(self::$statuses)
                            ->mapWithKeys(function (string $val, string $key): array {
                                return [$key => __($val)];
                            })->toArray(),
                    )
                    ->required()
                    ->in(array_keys(self::$statuses)),

                Forms\Components\Select::make('priority')
                    ->translateLabel()
                    ->default('medium')
                    ->options(
                        collect(self::$priorities)
                            ->mapWithKeys(function (string $val, string $key): array {
                                return [$key => __($val)];
                            }),
                    )
                    ->required()
                    ->in(array_keys(self::$priorities)),

                Forms\Components\DateTimePicker::make('due_at')
                    ->translateLabel()
                    ->columnSpanFull()
                    ->seconds(false)
                    ->native(false)
                    ->minutesStep(10)
                    ->hoursStep(2)
                    ->minDate(now())
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        $canUpdate = Auth::user()->can('Update todo');
        $canDelete = Auth::user()->can('Delete todo');

        return $table
            ->description(__('Get. Things. Done.'))
            ->modifyQueryUsing(function (Builder $query): Builder {
                return $query->when(!Auth::user()->can('View all todos'), function (Builder $query): Builder {
                    return $query->whereHas('users', function (Builder $user): Builder {
                        return $user->whereUserId(Auth::id());
                    });
                });
            })
            ->defaultGroup('priority')
            ->groups([
                Tables\Grouping\Group::make('priority')
                    ->label(__('Priority'))
                    ->getTitleFromRecordUsing(fn(Todo $todo): string => __(ucfirst($todo->priority)))
                    ->collapsible(),

                Tables\Grouping\Group::make('status')
                    ->label(__('Status'))
                    ->getTitleFromRecordUsing(fn(Todo $todo): string => __(ucfirst($todo->status)))
                    ->collapsible(),
            ])
            ->columns([
                Tables\Columns\TextColumn::make('status')
                    ->translateLabel()
                    ->sortable()
                    ->formatStateUsing(fn(string $state): string => __(ucfirst($state)))
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'new' => 'primary',
                        'pending' => 'warning',
                        'in progress' => 'info',
                        'completed' => 'success',
                        'archived' => 'gray',
                    }),

                Tables\Columns\TextColumn::make('name')
                    ->translateLabel()
                    ->searchable()
                    ->description(fn(?Model $model): string => $model->description ?? '')
                    ->formatStateUsing(function (?Model $record, string $state): string {
                        if ($record->status == self::Completed) {
                            return sprintf('~~%s~~', $state);
                        }

                        return $state;
                    })
                    ->markdown(),

                Tables\Columns\TextColumn::make('users.name')
                    ->limitList(2)
                    ->label(__('Assignee')),

                Tables\Columns\TextColumn::make('due_at')
                    ->translateLabel()
                    ->sortable()
                    ->tooltip(fn(?Model $record): string => $record->due_at->format('H:i'))
                    ->date(),

                Tables\Columns\TextColumn::make('priority')
                    ->translateLabel()
                    ->sortable()
                    ->badge()
                    ->formatStateUsing(fn(string $state): string => __(ucfirst($state)))
                    ->color(fn(string $state): string => match ($state) {
                        'low' => 'success',
                        'medium' => 'warning',
                        'high' => 'danger',
                    })
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->translateLabel()
                    ->options(self::$statuses)
                    ->multiple(),

                Tables\Filters\SelectFilter::make('priority')
                    ->translateLabel()
                    ->options(self::$priorities)
                    ->multiple(),

                Tables\Filters\SelectFilter::make('users')
                    ->label(__('Assignee'))
                    ->relationship('users', 'name')
                    ->multiple()
                    ->searchable()
                    ->preload(),

                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\Action::make('complete')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->requiresConfirmation()
                    ->disabled(function (?Model $todo): bool {
                        return $todo->status === self::Completed
                            || !in_array(Auth::id(), $todo->users->pluck('id')->toArray());
                    })
                    ->action(function (?Model $record): void {
                        $record->status = self::Completed;
                        $record->save();
                    }),
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\EditAction::make()->visible($canUpdate),
                    Tables\Actions\DeleteAction::make()->visible($canDelete),
                    Tables\Actions\ForceDeleteAction::make()->visible($canDelete),
                    Tables\Actions\RestoreAction::make()->visible($canUpdate),
                ])
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageTodos::route('/'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
