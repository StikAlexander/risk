<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InvoiceResource\Pages;
use App\Models\Invoice;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Joaopaulolndev\FilamentPdfViewer\Forms\Components\PdfViewerField;
use Filament\Tables\Actions\Action;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rule;
use Filament\Tables\Columns\ColorColumn;

class InvoiceResource extends Resource
{
    protected static ?string $model = Invoice::class;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Grid::make(2)->schema([
                Forms\Components\TextInput::make('invoice_number')
                ->label('Numero de Factura')
                ->prefix('FEVD')
                ->required()
                ->numeric()
                ->rules([
                    'regex:/^\d+$/',
                    'not_in:e,E',
                ])
                ->extraAttributes(['onkeydown' => 'if(event.key === "e" || event.key === "E") event.preventDefault();'])
                ->live()
                ->debounce(500)
                ->afterStateUpdated(function (Get $get, $state, Set $set) {
                    $currentId = $get('id');
                    $exists = DB::table('invoices')
                        ->where('invoice_number', $state)
                        ->when($currentId, function ($query) use ($currentId) {
                            return $query->where('id', '!=', $currentId);
                        })
                        ->exists();
            
                    if ($exists) {
                        $set('invoice_number_error', 'Este número de factura ya está siendo usado.');
                    } else {
                        $set('invoice_number_error', null);
                    }
                })
                ->hint(fn (Get $get) => $get('invoice_number_error'))
                ->hintColor('danger'),

                Forms\Components\DatePicker::make('issue_date')
                    ->required()
                    ->reactive()
                    ->afterStateUpdated(function ($state, callable $set) {
                        $dueDate = Carbon::parse($state)->addDays(30)->format('Y-m-d');
                        $set('due_date', $dueDate);
                    }),

                Forms\Components\DatePicker::make('due_date')
                    ->required()
                    ->disabled(),

                Forms\Components\TextInput::make('total_amount')
                    ->required()
                    ->numeric()
                    ->extraAttributes(['onkeydown' => 'if(event.key === "e" || event.key === "E") event.preventDefault();'])
                    ->reactive()
                    ->debounce('500ms')
                    ->afterStateUpdated(function ($state, callable $set) {
                        $set('total_paid', null);
                        $set('pending_amount', $state);
                    }),

                Forms\Components\TextInput::make('total_paid')
                    ->required()
                    ->numeric()
                    ->extraAttributes(['onkeydown' => 'if(event.key === "e" || event.key === "E") event.preventDefault();'])
                    ->reactive()
                    ->debounce('500ms')
                    ->disabled(fn (callable $get) => empty($get('total_amount')))
                    ->afterStateUpdated(function ($state, callable $set, callable $get) {
                        $totalAmount = (float)($get('total_amount') ?? 0);
                        if ((float)$state > $totalAmount) {
                            $state = $totalAmount;
                            $set('total_paid', $state);
                            $pendingAmount = $totalAmount - (float)$state;
                            $set('pending_amount', $pendingAmount);
                        }
                    }),

                Forms\Components\TextInput::make('pending_amount')
                    ->required()
                    ->disabled(),

                Forms\Components\Select::make('client_id')
                    ->label('Cliente')
                    ->required()
                    ->relationship('client', 'name')
                    ->options(function () {
                        return \App\Models\User::whereHas('roles', function ($query) {
                            $query->where('name', 'client');
                        })->pluck('name', 'id');
                    })
                    ->searchable()
                    ->preload(),
            ]),

            Forms\Components\Section::make('Additional Information')
                ->schema([
                    Forms\Components\Select::make('status')
                        ->options([
                            'Pending' => 'Pending',
                            'Paid' => 'Paid',
                            'Cancelled' => 'Cancelled',
                            'Partially Paid' => 'Partially Paid',
                        ])
                        ->default('Pending')
                        ->disabled(),

                    Forms\Components\TextInput::make('epayco_ref')
                        ->hidden(true),

                    Forms\Components\TextInput::make('epayco_status')
                        ->hidden(true),

                        Forms\Components\FileUpload::make('invoice_pdf')
                        ->label('Invoice PDF')
                        ->required()
                        ->directory('invoices')
                        ->acceptedFileTypes(['application/pdf'])
                        ->maxSize(10240),

                    PdfViewerField::make('invoice_pdf')
                        ->label('View the PDF')
                        ->minHeight('40svh'),

                    Forms\Components\Textarea::make('description'),

                    Forms\Components\TextInput::make('currency')
                        ->default('COP')
                        ->hidden(true),

                    Forms\Components\Placeholder::make('created_by')
                        ->label('Creado por')
                        ->content(fn (Invoice $record): string => $record->createdBy?->name ?? '-')
                        ->visible(fn (?Invoice $record): bool => $record !== null),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('invoice_number')->sortable()->searchable()
                    ->label('Numero de Factura')->limit(50)
                    ->formatStateUsing(fn (string $state): string => 'FEVD' . $state), 
                Tables\Columns\TextColumn::make('client.name')->sortable()->searchable()->label('Client'),
                Tables\Columns\TextColumn::make('createdBy.name')
                    ->label('Creado por')
                    ->placeholder('-')
                    ->searchable()
                    ->hidden(true),
                Tables\Columns\TextColumn::make('issue_date')->date()->sortable(),
                Tables\Columns\TextColumn::make('due_date')->date()->sortable(),
                Tables\Columns\TextColumn::make('total_amount')->money('COP')->sortable(),
                Tables\Columns\TextColumn::make('total_paid')->money('COP')->sortable(),
                Tables\Columns\TextColumn::make('pending_amount')->money('COP')->sortable(),
                TextColumn::make('status')
                ->label('Estado')
                ->badge()  // This will make the text appear as a badge
                ->color(fn (string $state): string => match ($state) {
                    'Pending' => 'warning',
                    'Paid' => 'success',
                    'Cancelled' => 'danger',
                    'Partially Paid' => 'info',
                    default => 'secondary',  // Use this for states that don't match
                })
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Action::make('viewPdf')
                ->label('View PDF')
                ->icon('heroicon-o-document-text')
                ->url(fn ($record) => Storage::url($record->invoice_pdf))
                ->openUrlInNewTab(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            // Define relation managers here
        ];
    }
    public function saveInvoicePdf($file, $invoiceNumber)
{
    $filename = 'FEVD' . $invoiceNumber . '.' . $file->getClientOriginalExtension();
    $path = $file->storeAs('invoices', $filename);

    return $path;
}

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListInvoices::route('/'),
            'create' => Pages\CreateInvoice::route('/create'),
            'edit' => Pages\EditInvoice::route('/{record}/edit'),
        ];
    }
}