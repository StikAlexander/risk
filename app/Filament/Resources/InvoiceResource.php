<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InvoiceResource\Pages;
use App\Models\Invoice;
use Filament\Forms;
use Filament\Forms\Components\Builder;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Joaopaulolndev\FilamentPdfViewer\Forms\Components\PdfViewerField;
use Filament\Tables\Actions\Action;
use Illuminate\Support\Facades\Storage;


class InvoiceResource extends Resource
{
    protected static ?string $model = Invoice::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('invoice_number')
                    ->required(),
                Forms\Components\DatePicker::make('issue_date')
                    ->required(),
                Forms\Components\DatePicker::make('due_date')
                    ->required(),
                Forms\Components\TextInput::make('total_amount')
                    ->required(),
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
                Forms\Components\Select::make('status')
                    ->options([
                        'Pending' => 'Pending',
                        'Paid' => 'Paid',
                        'Cancelled' => 'Cancelled',
                        'Partially Paid' => 'Partially Paid',
                    ])
                    ->default('Pending')
                    ->required(),
                Forms\Components\TextInput::make('total_paid')
                    ->required(),
                    Forms\Components\TextInput::make('pending_amount')
                    ->required(),
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
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('invoice_number')->sortable()->searchable(),
                TextColumn::make('client.name')->sortable()->searchable()->label('Client'),
                TextColumn::make('createdBy.name')
                    ->label('Creado por')
                    ->placeholder('-')
                    ->searchable()
                    ->hidden(true),
                TextColumn::make('issue_date')->date()->sortable(),
                TextColumn::make('due_date')->date()->sortable(),
                TextColumn::make('total_amount')->money('COP')->sortable(),
                TextColumn::make('total_paid')->money('COP')->sortable(),
                TextColumn::make('pending_amount')->money('COP')->sortable(),
                BadgeColumn::make('status')
                    ->colors([
                        'Pending' => 'warning',
                        'Paid' => 'success',
                        'Cancelled' => 'danger',
                        'Partially Paid' => 'info',
                    ])
                    ->label('Status'),
            ])
            ->filters([
                //
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
            //
        ];
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