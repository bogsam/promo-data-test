<?php

declare(strict_types=1);

namespace App\Modules\Reports\Infrastructure\Services;

use App\Modules\Reports\Application\Contracts\ReportFileWriter;
use App\Modules\Reports\Application\Data\GenerateReportFileData;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

final class LaravelReportFileWriter implements ReportFileWriter
{
    public function write(GenerateReportFileData $data): string
    {
        $fileName = sprintf(
            'report_%d_%d_%s.csv',
            $data->manufacturerId,
            $data->categoryId,
            $data->startedAt->format(format: 'Y-m-d_H-i-s'),
        );

        $relativePath = 'reports/' . $fileName;
        $fullPath = Storage::disk('local')->path($relativePath);
        $directory = dirname(path: $fullPath);

        if (file_exists(filename: $directory) && ! is_dir(filename: $directory)) {
            throw new RuntimeException(message: sprintf('Unable to create CSV directory: %s', $directory));
        }

        if (! is_dir(filename: $directory)) {
            if (! mkdir(directory: $directory, permissions: 0775, recursive: true) && ! is_dir(filename: $directory)) {
                throw new RuntimeException(message: sprintf('Unable to create CSV directory: %s', $directory));
            }
        }

        $handle = fopen(filename: $fullPath, mode: 'wb');

        if ($handle === false) {
            throw new RuntimeException(message: sprintf('Unable to open CSV file for writing: %s', $fullPath));
        }

        try {
            fputcsv(stream: $handle, fields: ['manufacturer_name', 'product_name', 'price', 'price_date']);

            foreach ($data->productPriceData as $productPriceRow) {
                fputcsv(stream: $handle, fields: [
                    $productPriceRow->manufacturerName,
                    $productPriceRow->productName,
                    $productPriceRow->price,
                    $productPriceRow->priceDate->format(format: 'Y-m-d H:i:s'),
                ]);
            }
        } finally {
            fclose(stream: $handle);
        }

        return $relativePath;
    }
}
