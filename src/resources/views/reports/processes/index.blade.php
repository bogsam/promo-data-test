<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Контроль выполнения процессов</title>
    <style>
        :root {
            color-scheme: light;
            --bg: #f5f1e8;
            --card: #fffdf8;
            --text: #1f2937;
            --muted: #6b7280;
            --line: #d6d3ca;
            --accent: #2563eb;
            --danger-bg: #fff1f2;
            --danger-line: #fecdd3;
            --danger-text: #991b1b;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            color: var(--text);
            background:
                radial-gradient(circle at top left, rgba(37, 99, 235, 0.08), transparent 32%),
                linear-gradient(180deg, #faf7f0 0%, var(--bg) 100%);
        }

        main {
            max-width: 1180px;
            margin: 0 auto;
            padding: 48px 24px 72px;
        }

        .page-head {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            align-items: end;
            justify-content: space-between;
            margin-bottom: 24px;
        }

        h1 {
            margin: 0;
            font-size: clamp(2rem, 4vw, 3rem);
            letter-spacing: -0.04em;
        }

        .subtitle {
            margin: 8px 0 0;
            max-width: 720px;
            color: var(--muted);
            line-height: 1.5;
        }

        .panel {
            background: rgba(255, 253, 248, 0.88);
            border: 1px solid rgba(214, 211, 202, 0.8);
            border-radius: 24px;
            overflow: hidden;
            box-shadow: 0 18px 50px rgba(15, 23, 42, 0.06);
            backdrop-filter: blur(10px);
        }

        .panel__empty {
            padding: 32px;
            color: var(--muted);
        }

        .table-wrap {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        thead th {
            padding: 16px 18px;
            background: rgba(37, 99, 235, 0.04);
            text-align: left;
            font-size: 0.9rem;
            color: var(--muted);
            border-bottom: 1px solid var(--line);
            white-space: nowrap;
        }

        tbody td {
            padding: 16px 18px;
            border-bottom: 1px solid rgba(214, 211, 202, 0.7);
            vertical-align: top;
        }

        tbody tr:last-child td {
            border-bottom: 0;
        }

        tbody tr.is-failed {
            background: var(--danger-bg);
            color: var(--danger-text);
        }

        .meta {
            display: block;
            margin-top: 4px;
            font-size: 0.875rem;
            color: var(--muted);
        }

        .status-pill {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            border-radius: 999px;
            padding: 6px 12px;
            font-weight: 600;
            background: rgba(37, 99, 235, 0.08);
            color: var(--accent);
        }

        .status-pill.is-failed {
            background: rgba(244, 63, 94, 0.12);
            color: var(--danger-text);
        }

        .download-link {
            color: var(--accent);
            text-decoration: none;
            font-weight: 600;
        }

        .download-link:hover {
            text-decoration: underline;
        }

        .download-missing {
            color: var(--muted);
        }

        @media (max-width: 768px) {
            main {
                padding: 24px 14px 48px;
            }

            thead th,
            tbody td {
                padding: 14px 12px;
            }
        }
    </style>
</head>
<body>
<main>
    <div class="page-head">
        <div>
            <h1>Контроль выполнения процессов</h1>
            <p class="subtitle">
                Статический список процессов формирования отчётов. Ошибки подсвечиваются, а успешные файлы доступны для скачивания.
            </p>
        </div>
    </div>

    <section class="panel">
        @if ($processes === [])
            <div class="panel__empty">Процессы отчётов пока отсутствуют.</div>
        @else
            <div class="table-wrap">
                <table>
                    <thead>
                    <tr>
                        <th>Дата процесса</th>
                        <th>Время выполнения</th>
                        <th>Идентификатор процесса</th>
                        <th>Статус процесса</th>
                        <th>Файл</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach ($processes as $process)
                        <tr @class(['is-failed' => $process->status === 'Ошибка'])>
                            <td>
                                {{ $process->startedAt->format('Y-m-d H:i:s') }}
                                @if ($process->finishedAt !== null)
                                    <span class="meta">Завершён: {{ $process->finishedAt->format('Y-m-d H:i:s') }}</span>
                                @endif
                            </td>
                            <td>
                                {{ $process->executionTime !== null ? $process->executionTime.' сек.' : 'В работе' }}
                            </td>
                            <td>
                                {{ $process->pid }}
                                <span class="meta">Категория: {{ $process->categoryId }}</span>
                            </td>
                            <td>
                                <span @class(['status-pill', 'is-failed' => $process->status === 'Ошибка'])>
                                    {{ $process->status }}
                                </span>
                            </td>
                            <td>
                                @if ($process->filePath !== null)
                                    <a class="download-link" href="{{ route('report-processes.download', ['reportProcess' => $process->processId]) }}">
                                        {{ $process->fileName }}
                                    </a>
                                @else
                                    <span class="download-missing">-</span>
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
