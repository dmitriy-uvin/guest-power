<?php

declare(strict_types=1);

namespace App\Action\Import;

use App\Exceptions\Import\ImportAPIErrorStatuses;
use App\Exceptions\Import\ImportErrorPropertyStatuses;
use App\Exceptions\Import\WrongImportValueException;
use App\Jobs\GetDataFromApiForPlatforms;
use App\Jobs\UpdateDataFromApiForPlatforms;
use App\Models\Alexa;
use App\Models\Facebook;
use App\Models\Majestic;
use App\Models\Moz;
use App\Models\Platform;
use App\Models\SemRush;
use App\Models\Topic;
use App\Services\SeoRankService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

final class ImportPlatformsTableAction
{
    private SeoRankService $seoRankService;

    public function __construct()
    {
        $this->seoRankService = new SeoRankService();
    }

    public function execute(ImportPlatformsTableRequest $request)
    {
        $filePath = $this->storeFile($request->getFile());
        $platformsData = $this->parseCSVFile($filePath);

        $this->validatePlatformsFromCSV($platformsData);

        $platformsData = $this->convertEmptyFieldsToNull($platformsData);

        $platformsObjects = $this->createPlatforms($platformsData);

        $this->getDataFromApi($platformsObjects);

        return new ImportPlatformsTableResponse([
            'data' => 1
        ]);
    }

    private function getDataFromApi(Collection $platforms)
    {
        GetDataFromApiForPlatforms::dispatch($platforms);

        UpdateDataFromApiForPlatforms::dispatch($platforms)->delay(
            now()->addMinutes(5)
        );
    }

    private function storeFile($file): string
    {
        $extension = $file->getClientOriginalExtension();
        $originalTmpName = $file->getFilename();
        $originalName = $file->getClientOriginalName();
        $fileName = $originalName . '-' . $originalTmpName . '.' . $extension;
        return Storage::putFileAs(
            'public/import',
            $file,
            $fileName
        );
    }

    private function parseCSVFile(string $filePath): array
    {
        $fHandle = fopen('../storage/app/' . $filePath, 'r');
        $platformsData = [];
        $fieldKeys = [];
        $row = 0;
        while (($line = fgets($fHandle)) !== false) {
            if ($row === 0) {
                $fieldKeys = str_getcsv($line);
            } else {
                $platformsData[] = array_combine($fieldKeys, str_getcsv($line));
            }
            $row += 1;
        }
        fclose($fHandle);

        return $platformsData;
    }

    private function validatePlatformsFromCSV($platformsData)
    {
        $row = 1;

        foreach ($platformsData as $index => $platformData) {
            foreach ($platformData as $columnName => $value) {
                switch ($columnName) {
                    case 'website_url':
                    case 'topics':
                    case 'description':
                    case 'article_requirements':
                    case 'where_posted':
                    case 'domain_zone':
                        if (!$value) {
                            throw new WrongImportValueException($row, $columnName);
                        }
                        break;
                    case 'deadline':
                        if (!(int)$value) {
                            throw new WrongImportValueException($row, $columnName);
                        }
                        if ((int)$value < 1 || (int)$value > 60) {
                            throw new WrongImportValueException($row, $columnName, 'Deadline must be between 1 and 60 days');
                        }
                        break;
                    case 'dofollow_active':
                    case 'home_featured_active':
                    case 'niche_edit_link_active':
                        if ((int)$value !== 0 && (int)$value !== 1) {
                            throw new WrongImportValueException($row, $columnName);
                        }
                        break;
                    case 'price':
                    case 'article_writing_price':
                        if (is_numeric($value)) {
                            if ((int)$value < 0) {
                                throw new WrongImportValueException($row, $columnName, "Price can't be less than 0");
                            }
                        } else {
                            throw new WrongImportValueException($row, $columnName, "Price must be numeric");
                        }
                        break;
                    case 'niche_edit_link_price':
                        if ($value) {
                            if (!is_numeric($value)) {
                                throw new WrongImportValueException($row, $columnName, "Niche Edit Price must be numeric");
                            }
                        }
                        break;
                    case 'email':
                        if (!$value) {
                            throw new WrongImportValueException($row, $columnName, "Email value is required");
                        }
                        break;
                }
            }
            $row += 1;
        }

        return true;
    }

    private function createPlatforms($platformsData)
    {
        $platformCollection = collect();

        foreach ($platformsData as $platform) {
            $platformTopics = explode(',', $platform['topics']);

            $topics = Topic::whereIn('name', $platformTopics)->get('id');
            $platform = new Platform($platform);
            $platform->save();
            $platform->topics()->attach($topics);
            $platformCollection->add($platform);
        }

        return $platformCollection;
    }

    private function convertEmptyFieldsToNull(array $platformsData)
    {
        foreach ($platformsData as $index => $platform) {
            foreach ($platform as $field => $value) {
                if ($value === '') {
                    $platformsData[$index][$field] = null;
                }
            }
        }
        return $platformsData;
    }
}