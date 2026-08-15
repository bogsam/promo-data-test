<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Контроль выполнения процессов</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-stone-100 bg-[radial-gradient(circle_at_top_left,rgba(37,99,235,0.08),transparent_32%),linear-gradient(180deg,#faf7f0_0%,#f5f1e8_100%)] text-center text-slate-800 antialiased">
<main class="mx-auto max-w-[1180px] px-3.5 py-6 sm:px-6 sm:py-12 lg:pb-18">
    <div class="mb-6 flex flex-wrap items-end justify-center gap-3">
        <div>
            <h1 class="text-3xl font-bold text-slate-900 sm:text-5xl">Контроль выполнения процессов</h1>
            <p class="mx-auto mt-2 max-w-3xl leading-6 text-slate-500">
                Статический список процессов формирования отчётов. Ошибки подсвечиваются, а успешные файлы доступны для скачивания.
            </p>
        </div>
    </div>

    <section class="overflow-hidden rounded-3xl border border-stone-300/80 bg-stone-50/90 shadow-[0_18px_50px_rgba(15,23,42,0.06)] backdrop-blur">
        @if ($processes === [])
            <div class="p-8 text-slate-500">Процессы отчётов пока отсутствуют.</div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full border-collapse">
                    <thead>
                    <tr>
                        <th class="whitespace-nowrap border-b border-stone-300 bg-blue-600/[0.04] px-3 py-3.5 text-center text-sm font-semibold text-slate-500 sm:px-[18px] sm:py-4">Дата процесса</th>
                        <th class="whitespace-nowrap border-b border-stone-300 bg-blue-600/[0.04] px-3 py-3.5 text-center text-sm font-semibold text-slate-500 sm:px-[18px] sm:py-4">Время выполнения</th>
                        <th class="whitespace-nowrap border-b border-stone-300 bg-blue-600/[0.04] px-3 py-3.5 text-center text-sm font-semibold text-slate-500 sm:px-[18px] sm:py-4">Идентификатор процесса</th>
                        <th class="whitespace-nowrap border-b border-stone-300 bg-blue-600/[0.04] px-3 py-3.5 text-center text-sm font-semibold text-slate-500 sm:px-[18px] sm:py-4">Статус процесса</th>
                        <th class="whitespace-nowrap border-b border-stone-300 bg-blue-600/[0.04] px-3 py-3.5 text-center text-sm font-semibold text-slate-500 sm:px-[18px] sm:py-4">Файл</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach ($processes as $process)
                        <tr @class(['bg-rose-50 text-red-800' => $process->status === 'Ошибка'])>
                            <td class="border-b border-stone-300/70 px-3 py-3.5 text-center align-middle last:border-b-0 sm:px-[18px] sm:py-4">
                                {{ $process->startedAt->format('Y-m-d H:i:s') }}
                                @if ($process->finishedAt !== null)
                                    <span class="mt-1 block text-sm text-slate-500">Завершён: {{ $process->finishedAt->format('Y-m-d H:i:s') }}</span>
                                @endif
                            </td>
                            <td class="border-b border-stone-300/70 px-3 py-3.5 text-center align-middle last:border-b-0 sm:px-[18px] sm:py-4">
                                {{ $process->executionTime !== null ? $process->executionTime.' сек.' : 'В работе' }}
                            </td>
                            <td class="border-b border-stone-300/70 px-3 py-3.5 text-center align-middle last:border-b-0 sm:px-[18px] sm:py-4">
                                {{ $process->pid }}
                                <span class="mt-1 block text-sm text-slate-500">Категория: {{ $process->categoryId }}</span>
                            </td>
                            <td class="border-b border-stone-300/70 px-3 py-3.5 text-center align-middle last:border-b-0 sm:px-[18px] sm:py-4">
                                <span @class([
                                    'inline-flex items-center gap-2 rounded-full px-3 py-1.5 font-semibold',
                                    'bg-rose-500/[0.12] text-red-800' => $process->status === 'Ошибка',
                                    'bg-emerald-500/[0.14] text-emerald-700' => $process->status === 'Завершён',
                                    'bg-blue-600/[0.08] text-blue-600' => ! in_array($process->status, ['Ошибка', 'Завершён'], true),
                                ])>
                                    {{ $process->status }}
                                </span>
                            </td>
                            <td class="border-b border-stone-300/70 px-3 py-3.5 text-center align-middle last:border-b-0 sm:px-[18px] sm:py-4">
                                @if ($process->filePath !== null)
                                    <a class="font-semibold text-blue-600 no-underline hover:underline" href="{{ route('report-processes.download', ['reportProcess' => $process->processId]) }}">
                                        {{ $process->fileName }}
                                    </a>
                                @else
                                    <span class="text-slate-500">-</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </section>
</main>
</body>
</html>
