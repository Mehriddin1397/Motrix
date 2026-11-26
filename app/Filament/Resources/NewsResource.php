<?php

namespace App\Filament\Resources;

use App\Filament\Resources\NewsResource\Pages;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Modules\News\Models\News;
use Modules\Video\Models\Video;

class NewsResource extends Resource
{
    protected static ?string $model = News::class;

    protected static ?string $navigationIcon = 'heroicon-o-newspaper';

    protected static ?string $navigationGroup = 'Kontent';

    protected static ?string $navigationLabel = 'Yangiliklar va Maqolalar';

    protected static ?string $modelLabel = 'maqola';

    protected static ?string $pluralModelLabel = 'maqolalar';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make("Maqola asosiy ma'lumotlari")
                            ->schema([
                                Forms\Components\TextInput::make('title')
                                    ->label('Sarlavha')
                                    ->required()
                                    ->maxLength(255)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn (string $operation, $state, Forms\Set $set) => $operation === 'create' ? $set('slug', str($state)->slug()) : null),

                                Forms\Components\TextInput::make('slug')
                                    ->label('Slug (URL)')
                                    ->required()
                                    ->maxLength(255)
                                    ->unique(ignoreRecord: true),

                                Forms\Components\Select::make('category_id')
                                    ->label('Kategoriya')
                                    ->relationship('category', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->createOptionForm([
                                        Forms\Components\TextInput::make('name')
                                            ->label('Kategoriya nomi')
                                            ->required()
                                            ->live(onBlur: true)
                                            ->afterStateUpdated(fn ($state, Forms\Set $set) => $set('slug', str($state)->slug())),
                                        Forms\Components\TextInput::make('slug')
                                            ->label('Slug')
                                            ->required(),
                                    ]),

                                Forms\Components\Select::make('author_id')
                                    ->label('Muallif')
                                    ->relationship('author', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->default(fn () => auth()->id()),
                            ])
                            ->columns(2),

                        Forms\Components\Section::make("Mavzuga oid Video qo'llanma (ixtiyoriy)")
                            ->description("Agar maqola video asosida tayyorlangan bo'lsa, videoni biriktiring. Saytda video pleer va maxsus belgi avtomatik chiqadi.")
                            ->icon('heroicon-o-video-camera')
                            ->collapsible()
                            ->schema([
                                Forms\Components\Select::make('video_id')
                                    ->label("Biriktirilgan video qo'llanma")
                                    ->relationship('video', 'title')
                                    ->searchable()
                                    ->preload()
                                    ->nullable()
                                    ->helperText("Katalogdagi videoni tanlang yoki «+» tugmasi orqali yangi video qo'shing.")
                                    ->createOptionForm([
                                        Forms\Components\Select::make('category_id')
                                            ->label('Video kategoriyasi')
                                            ->relationship('category', 'name')
                                            ->required(),
                                        Forms\Components\TextInput::make('title')
                                            ->label('Video sarlavhasi')
                                            ->required()
                                            ->live(onBlur: true)
                                            ->afterStateUpdated(fn ($state, Forms\Set $set) => $set('slug', str($state)->slug())),
                                        Forms\Components\TextInput::make('slug')
                                            ->label('Slug')
                                            ->required(),
                                        Forms\Components\TextInput::make('url_or_path')
                                            ->label('Video manzili (YouTube, Shorts yoki MP4 havola)')
                                            ->placeholder('https://youtube.com/shorts/... yoki https://youtu.be/...')
                                            ->required(),
                                        Forms\Components\TextInput::make('duration_seconds')
                                            ->label('Davomiyligi (soniya)')
                                            ->numeric(),
                                    ]),
                            ]),

                        Forms\Components\Section::make('Maqola matni va dizayni')
                            ->description("Tepadagi yordamchi tugmalar orqali xuddi avvalgi Batareya maqolasidek bosqichli (1, 2, 3) shablon, eslatma va xulosa bloklarini tezda kiriting.")
                            ->schema([
                                Forms\Components\RichEditor::make('body')
                                    ->label('Maqola matni (Boyitilgan muharrir)')
                                    ->required()
                                    ->fileAttachmentsDisk('public')
                                    ->fileAttachmentsDirectory('articles/content')
                                    ->fileAttachmentsVisibility('public')
                                    ->columnSpanFull()
                                    ->hintActions([
                                        Forms\Components\Actions\Action::make('load_full_template')
                                            ->label("📋 Video-qo'llanma shabloni")
                                            ->icon('heroicon-m-document-duplicate')
                                            ->color('warning')
                                            ->requiresConfirmation()
                                            ->modalHeading("Video-qo'llanma shablonini yuklash")
                                            ->modalDescription("Ushbu amal maqola matniga avvalgi Batareya maqolasidek bosqichli raqamlar (1, 2, 3), rasm joylashuvlari, diqqat qutisi, taqqoslash jadvali va yakuniy xulosa blokini joylaydi. Mavjud matn almashtiriladi.")
                                            ->action(fn (Forms\Set $set) => $set('body', self::getGuideTemplateHtml())),

                                        Forms\Components\Actions\Action::make('add_step')
                                            ->label("➕ Bosqich (Step)")
                                            ->icon('heroicon-m-plus-circle')
                                            ->color('success')
                                            ->form([
                                                Forms\Components\TextInput::make('step_num')
                                                    ->label('Bosqich raqami')
                                                    ->default('1')
                                                    ->required(),
                                                Forms\Components\TextInput::make('step_title')
                                                    ->label('Bosqich sarlavhasi')
                                                    ->placeholder("Masalan: Akkumulyatorni yechib olish kerakmi?")
                                                    ->required(),
                                                Forms\Components\Textarea::make('step_text')
                                                    ->label('Bosqich matni')
                                                    ->placeholder("Ushbu bosqich bo'yicha tushuntirish...")
                                                    ->rows(4),
                                            ])
                                            ->action(function (array $data, Forms\Get $get, Forms\Set $set) {
                                                $current = $get('body') ?? '';
                                                $num = htmlspecialchars($data['step_num']);
                                                $title = htmlspecialchars($data['step_title']);
                                                $text = nl2br(htmlspecialchars($data['step_text'] ?? ''));
                                                $block = <<<HTML

<div class="pt-4">
    <h2 class="text-xl font-extrabold text-zinc-900 dark:text-white flex items-center gap-2">
        <span class="flex h-7 w-7 items-center justify-center rounded-lg bg-amber-500/10 text-sm font-bold text-amber-600 dark:text-amber-400">{$num}</span>
        {$title}
    </h2>
    <p class="mt-3 leading-relaxed">
        {$text}
    </p>
</div>
HTML;
                                                $set('body', $current . $block);
                                            }),

                                        Forms\Components\Actions\Action::make('add_callout')
                                            ->label("⚠️ Eslatma qutisi")
                                            ->icon('heroicon-m-exclamation-triangle')
                                            ->color('info')
                                            ->form([
                                                Forms\Components\Select::make('callout_type')
                                                    ->label('Turi')
                                                    ->options([
                                                        'warning' => '⚠️ Muhim eslatma / Ogohlantirish (Sariq)',
                                                        'info' => '💡 Foydali maslahat / Tavsiya (Moviy)',
                                                        'success' => '✅ Ijobiy qoida / Tasdiq (Yashil)',
                                                    ])
                                                    ->default('warning')
                                                    ->required(),
                                                Forms\Components\TextInput::make('callout_title')
                                                    ->label('Quti sarlavhasi')
                                                    ->default('Muhim qoida:')
                                                    ->required(),
                                                Forms\Components\Textarea::make('callout_text')
                                                    ->label('Eslatma matni')
                                                    ->rows(3)
                                                    ->required(),
                                            ])
                                            ->action(function (array $data, Forms\Get $get, Forms\Set $set) {
                                                $current = $get('body') ?? '';
                                                $type = $data['callout_type'] ?? 'warning';
                                                $title = htmlspecialchars($data['callout_title']);
                                                $text = nl2br(htmlspecialchars($data['callout_text'] ?? ''));

                                                $border = match($type) {
                                                    'info' => 'border-sky-500 bg-sky-500/10 text-sky-900 dark:text-sky-200',
                                                    'success' => 'border-emerald-500 bg-emerald-500/10 text-emerald-900 dark:text-emerald-200',
                                                    default => 'border-amber-500 bg-amber-500/10 text-amber-900 dark:text-amber-200',
                                                };
                                                $block = <<<HTML

<div class="mt-4 rounded-2xl border-l-4 {$border} p-4 text-sm">
    <strong class="font-bold">{$title}</strong>
    <p class="mt-1">
        {$text}
    </p>
</div>
HTML;
                                                $set('body', $current . $block);
                                            }),

                                        Forms\Components\Actions\Action::make('add_summary')
                                            ->label("🎯 Xulosa bloki")
                                            ->icon('heroicon-m-check-badge')
                                            ->color('gray')
                                            ->form([
                                                Forms\Components\TextInput::make('summary_title')
                                                    ->label('Xulosa sarlavhasi')
                                                    ->default('Xulosa: Asosiy tavsiyalar')
                                                    ->required(),
                                                Forms\Components\Textarea::make('summary_points')
                                                    ->label("Xulosa bandlari (Har bir bandni yangi qatordan yozing)")
                                                    ->placeholder("1. Birinchi muhim tavsiya...\n2. Ikkinchi tavsiya...\n3. Uchinchi tavsiya...")
                                                    ->rows(4)
                                                    ->required(),
                                            ])
                                            ->action(function (array $data, Forms\Get $get, Forms\Set $set) {
                                                $current = $get('body') ?? '';
                                                $title = htmlspecialchars($data['summary_title']);
                                                $lines = array_filter(array_map('trim', explode("\n", $data['summary_points'] ?? '')));
                                                $listHtml = '';
                                                foreach ($lines as $line) {
                                                    $listHtml .= '<li>' . htmlspecialchars($line) . '</li>';
                                                }
                                                $block = <<<HTML

<div class="mt-6 rounded-3xl border border-amber-500/30 bg-gradient-to-br from-amber-500/10 via-amber-500/5 to-transparent p-5">
    <h3 class="text-base font-extrabold text-zinc-900 dark:text-white flex items-center gap-2">
        <span>🎯</span> {$title}
    </h3>
    <ol class="mt-3 space-y-2 list-decimal list-inside text-sm text-zinc-700 dark:text-zinc-300 font-medium">
        {$listHtml}
    </ol>
    <div class="mt-3 pt-3 border-t border-amber-500/20 text-xs font-bold text-amber-600 dark:text-amber-400">
        Motrix — Mototsiklni tushuning. Yo‘lni his qiling.
    </div>
</div>
HTML;
                                                $set('body', $current . $block);
                                            }),
                                    ]),
                            ]),
                    ])
                    ->columnSpan(['lg' => 2]),

                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make('Muqova rasmi')
                            ->schema([
                                Forms\Components\SpatieMediaLibraryFileUpload::make('cover')
                                    ->label('Muqova rasmi (Asosiy banner)')
                                    ->collection('cover')
                                    ->image()
                                    ->imageEditor()
                                    ->disk('public')
                                    ->directory('news-covers'),
                            ]),

                        Forms\Components\Section::make('Chop etish holati')
                            ->schema([
                                Forms\Components\Select::make('status')
                                    ->label('Holati')
                                    ->options([
                                        'draft' => 'Qoralama',
                                        'published' => 'Chop etilgan',
                                    ])
                                    ->default('published')
                                    ->required(),
                                Forms\Components\DateTimePicker::make('published_at')
                                    ->label('Chop etilgan sana')
                                    ->default(now()),
                            ]),
                    ])
                    ->columnSpan(['lg' => 1]),
            ])
            ->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\SpatieMediaLibraryImageColumn::make('cover')
                    ->label('Rasm')
                    ->collection('cover')
                    ->circular(false),
                Tables\Columns\TextColumn::make('title')
                    ->label('Sarlavha')
                    ->searchable()
                    ->limit(40),
                Tables\Columns\TextColumn::make('category.name')
                    ->label('Kategoriya')
                    ->badge()
                    ->color('gray'),
                Tables\Columns\TextColumn::make('video.title')
                    ->label('Video')
                    ->icon('heroicon-m-video-camera')
                    ->iconColor('amber')
                    ->placeholder("Yo'q")
                    ->limit(25)
                    ->tooltip(fn ($record) => $record->video?->title),
                Tables\Columns\BadgeColumn::make('status')
                    ->label('Holati')
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'draft' => 'Qoralama',
                        'published' => 'Chop etilgan',
                        default => $state,
                    })
                    ->colors([
                        'gray' => 'draft',
                        'success' => 'published',
                    ]),
                Tables\Columns\TextColumn::make('published_at')
                    ->label('Chop etilgan')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Holati')
                    ->options([
                        'draft' => 'Qoralama',
                        'published' => 'Chop etilgan',
                    ]),
                Tables\Filters\SelectFilter::make('category_id')
                    ->label('Kategoriya')
                    ->relationship('category', 'name'),
                Tables\Filters\TernaryFilter::make('has_video')
                    ->label('Video mavjudligi')
                    ->placeholder('Barchasi')
                    ->trueLabel('Videoli maqolalar')
                    ->falseLabel('Videosiz maqolalar')
                    ->queries(
                        true: fn ($query) => $query->whereNotNull('video_id'),
                        false: fn ($query) => $query->whereNull('video_id'),
                    ),
            ])
            ->actions([
                Tables\Actions\Action::make('view_site')
                    ->label("Saytda ko'rish")
                    ->icon('heroicon-m-arrow-top-right-on-square')
                    ->color('amber')
                    ->url(fn (News $record) => route('news.show', $record->slug))
                    ->openUrlInNewTab(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListNews::route('/'),
            'create' => Pages\CreateNews::route('/create'),
            'edit' => Pages\EditNews::route('/{record}/edit'),
        ];
    }

    /**
     * Avvalgi Batareya maqolasi dizaynidagi boyitilgan HTML shablon.
     */
    public static function getGuideTemplateHtml(): string
    {
        return <<<'HTML'
<div class="space-y-6 text-zinc-800 dark:text-zinc-200">
    <p class="text-lg font-medium leading-relaxed text-zinc-700 dark:text-zinc-300">
        Kirish qismi: Ushbu qo'llanmada mototsiklchilar uchun eng muhim masalalar va amaliy tavsiyalar batafsil tushuntirilgan.
    </p>

    <p class="leading-relaxed">
        Mutaxassislar va rasmiy ishlab chiqaruvchilar tavsiyasiga ko'ra, quyidagi muhim qoidalarga amal qilish lozim.
    </p>

    <!-- 1-BOSQICH -->
    <div class="pt-4">
        <h2 class="text-xl font-extrabold text-zinc-900 dark:text-white flex items-center gap-2">
            <span class="flex h-7 w-7 items-center justify-center rounded-lg bg-amber-500/10 text-sm font-bold text-amber-600 dark:text-amber-400">1</span>
            1-bosqich sarlavhasi
        </h2>

        <p class="mt-3 leading-relaxed">
            Bu yerga birinchi bosqich bo'yicha batafsil tushuntirish va ma'lumotlar yoziladi.
        </p>

        <!-- Eslatma / Diqqat qutisi -->
        <div class="mt-3 rounded-2xl border-l-4 border-amber-500 bg-amber-500/10 p-4 text-sm text-zinc-800 dark:text-zinc-200">
            <strong class="font-bold text-amber-600 dark:text-amber-400">⚠️ Muhim qoida:</strong>
            <p class="mt-1">
                E'tibor qaratilishi kerak bo'lgan xavfsizlik yoki texnik qoidani shu yerga yozing.
            </p>
        </div>
    </div>

    <!-- 2-BOSQICH -->
    <div class="pt-4">
        <h2 class="text-xl font-extrabold text-zinc-900 dark:text-white flex items-center gap-2">
            <span class="flex h-7 w-7 items-center justify-center rounded-lg bg-amber-500/10 text-sm font-bold text-amber-600 dark:text-amber-400">2</span>
            2-bosqich sarlavhasi
        </h2>

        <p class="mt-3 leading-relaxed">
            Bu yerga ikkinchi bosqich bo'yicha ma'lumotlar yoziladi.
        </p>

        <ul class="mt-3 space-y-2 list-disc list-inside text-sm">
            <li><strong>Asosiy qoida 1:</strong> tavsif matni</li>
            <li><strong>Asosiy qoida 2:</strong> tavsif matni</li>
        </ul>
    </div>

    <!-- 3-BOSQICH: TAQQOSLASH JADVALI -->
    <div class="pt-4">
        <h2 class="text-xl font-extrabold text-zinc-900 dark:text-white flex items-center gap-2">
            <span class="flex h-7 w-7 items-center justify-center rounded-lg bg-amber-500/10 text-sm font-bold text-amber-600 dark:text-amber-400">3</span>
            Texnik taqqoslash jadvali
        </h2>

        <div class="mt-4 overflow-x-auto rounded-2xl border border-zinc-200 dark:border-zinc-800">
            <table class="w-full text-left text-sm border-collapse">
                <thead>
                    <tr class="bg-amber-500/10 text-xs font-bold uppercase tracking-wider text-amber-700 dark:text-amber-400 border-b border-zinc-200 dark:border-zinc-800">
                        <th class="p-3">Tur / Model</th>
                        <th class="p-3">Muhim talab va qoidalar</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800">
                    <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/50">
                        <td class="p-3 font-bold text-zinc-900 dark:text-white">1-kategoriya</td>
                        <td class="p-3 text-zinc-600 dark:text-zinc-300">Tushuntirish matni</td>
                    </tr>
                    <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/50">
                        <td class="p-3 font-bold text-zinc-900 dark:text-white">2-kategoriya</td>
                        <td class="p-3 text-zinc-600 dark:text-zinc-300">Tushuntirish matni</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- XULOSA QUTISI -->
    <div class="mt-6 rounded-3xl border border-amber-500/30 bg-gradient-to-br from-amber-500/10 via-amber-500/5 to-transparent p-5">
        <h3 class="text-base font-extrabold text-zinc-900 dark:text-white flex items-center gap-2">
            <span>🎯</span> Xulosa: 4 ta oltin qoida
        </h3>
        <ol class="mt-3 space-y-2 list-decimal list-inside text-sm text-zinc-700 dark:text-zinc-300 font-medium">
            <li><strong>1-tavsiya:</strong> qisqacha mazmuni</li>
            <li><strong>2-tavsiya:</strong> qisqacha mazmuni</li>
            <li><strong>3-tavsiya:</strong> qisqacha mazmuni</li>
            <li><strong>4-tavsiya:</strong> qisqacha mazmuni</li>
        </ol>
        <div class="mt-4 pt-3 border-t border-amber-500/20 text-xs font-bold text-amber-600 dark:text-amber-400">
            Motrix — Mototsiklni tushuning. Yo‘lni his qiling.
        </div>
    </div>
</div>
HTML;
    }
}
