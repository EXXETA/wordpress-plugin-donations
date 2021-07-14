<?php
/*
 * Copyright 2020-2021 EXXETA AG, Marius Schuppert
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <https://www.gnu.org/licenses/>.
 */


namespace WWFDonationPlugin\Service;


use exxeta\wwf\banner\AbstractCharityProductManager;
use exxeta\wwf\banner\model\CharityProduct;
use Shopware\Core\Content\Media\Aggregate\MediaFolder\MediaFolderEntity;
use Shopware\Core\Content\Media\DataAbstractionLayer\MediaFolderRepositoryDecorator;
use Shopware\Core\Content\Media\DataAbstractionLayer\MediaRepositoryDecorator;
use Shopware\Core\Content\Media\Exception\DuplicatedMediaFileNameException;
use Shopware\Core\Content\Media\File\FileSaver;
use Shopware\Core\Content\Media\File\MediaFile;
use Shopware\Core\Content\Media\MediaEntity;
use Shopware\Core\Content\Product\Aggregate\ProductMedia\ProductMediaEntity;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\Uuid\Uuid;

class MediaService
{
    const WWF_MEDIA_FOLDER_NAME = 'WWF Media';
    const ASSET_PATH_PREFIX = __DIR__ . '/../Resources/public/static/';

    /**
     * @var AbstractCharityProductManager
     */
    protected $charityProductManager;

    /**
     * @var MediaRepositoryDecorator
     */
    protected $mediaRepository;

    /**
     * @var MediaFolderRepositoryDecorator
     */
    protected $mediaFolderRepository;

    /**
     * @var EntityRepository
     */
    protected $productMediaRepository;

    /**
     * @var FileSaver
     */
    protected $fileSaver;

    /**
     * @var MediaFolderEntity
     */
    private $mediaFolder;

    /**
     * MediaService constructor.
     *
     * @param AbstractCharityProductManager $productManager
     * @param MediaRepositoryDecorator $mediaRepository
     * @param MediaFolderRepositoryDecorator $mediaFolderRepository
     * @param EntityRepository $productMediaRepository
     * @param FileSaver $fileSaver
     */
    public function __construct(AbstractCharityProductManager $productManager,
                                MediaRepositoryDecorator $mediaRepository,
                                MediaFolderRepositoryDecorator $mediaFolderRepository,
                                EntityRepository $productMediaRepository,
                                FileSaver $fileSaver)
    {
        $this->charityProductManager = $productManager;
        $this->mediaRepository = $mediaRepository;
        $this->mediaFolderRepository = $mediaFolderRepository;
        $this->productMediaRepository = $productMediaRepository;
        $this->fileSaver = $fileSaver;
    }

    public function install(): void
    {
        $this->createTopLevelDirectory();
        $this->importProductImages();
    }

    public function getProductMediaRecordBySlug(string $slug): ?MediaEntity
    {
        $charityProduct = $this->charityProductManager->getProductBySlug($slug);
        if ($charityProduct instanceof CharityProduct) {
            $baseMediaName = basename($charityProduct->getImagePath(), '.png');

            $criteria = new Criteria();
            $criteria->addFilter(new EqualsFilter('fileName', $baseMediaName));
            $entitySearchResult = $this->mediaRepository->search($criteria, Context::createDefaultContext());
            if ($entitySearchResult->getTotal() > 0) {
                return $entitySearchResult->first();
            }
        }
        return null;
    }

    public function getProductMediaRecord(string $mediaId, string $productId): ?ProductMediaEntity
    {
        if (empty($mediaId) || empty($productId)) {
            return null;
        }
        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('productId', $productId));
        $criteria->addFilter(new EqualsFilter('mediaId', $mediaId));

        $entitySearchResult = $this->productMediaRepository->search($criteria, Context::createDefaultContext());
        if ($entitySearchResult->getTotal() > 0) {
            return $entitySearchResult->first();
        }
        return null;
    }

    /**
     * @param string $filename
     * @return MediaEntity
     */
    public function getPluginMediaRecordByFilename(string $filename): ?MediaEntity
    {
        $mediaFolder = $this->getMediaFolder();
        if (!$mediaFolder) {
            // TODO log this event, it should not happen
            return null;
        }
        $fileCriteria = new Criteria();
        $fileCriteria->addFilter(new EqualsFilter('fileName', $filename));
        $fileCriteria->addFilter(new EqualsFilter('mediaFolderId', $mediaFolder->getId()));
        $entitySearchResult = $this->mediaRepository->search($fileCriteria, Context::createDefaultContext());
        if ($entitySearchResult->getTotal() > 0) {
            return $entitySearchResult->first();
        }
        return null;
    }

    protected function createTopLevelDirectory(): void
    {
        $mediaFolderRecord = $this->getMediaFolder();
        if ($mediaFolderRecord != null) {
            // folder already exists
            return;
        }

        // create new top-level media folder
        $this->mediaFolderRepository->create([[
            'name' => self::WWF_MEDIA_FOLDER_NAME,
            'useParentConfiguration' => false,
            'configuration' => [],
        ]], Context::createDefaultContext());
    }

    protected function importProductImages(): void
    {
        $mediaFolderId = $this->getMediaFolder()->getId();
        if (!$mediaFolderId) {
            // FIXME error log!
            return;
        }
        $charityProducts = $this->charityProductManager->getAllProducts();
        foreach ($charityProducts as $charityProduct) {
            $productMediaRecord = $this->getProductMediaRecordBySlug($charityProduct->getSlug());
            if ($productMediaRecord != null) {
                continue;
            }
            $fileNameWithoutExt = str_replace(sprintf('.%s', 'png'), '', $charityProduct->getImagePath());
            $this->importProductImage($fileNameWithoutExt, $mediaFolderId, 'png');
        }

        foreach ($this->charityProductManager->getAllCampaignBannerFileNames() as $bannerImageFileName) {
            $fileType = 'jpg';
            $fileNameWithoutExt = str_replace(sprintf('.%s', $fileType), '', $bannerImageFileName);
            $this->importProductImage($fileNameWithoutExt, $mediaFolderId, $fileType);
        }
    }

    private function getMediaFolder(): ?MediaFolderEntity
    {
        // "cache" retrieved value as there is only one media folder used by this plugin
        if ($this->mediaFolder instanceof MediaFolderEntity) {
            return $this->mediaFolder;
        }

        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('name', self::WWF_MEDIA_FOLDER_NAME));

        $context = Context::createDefaultContext();
        $entitySearchResult = $this->mediaFolderRepository->search($criteria, $context);
        if ($entitySearchResult->getTotal() > 0) {
            // a directory with this name does already exist
            $first = $entitySearchResult->first();
            if ($first instanceof MediaFolderEntity) {
                $this->mediaFolder = $first;
            }
            return $first;
        }
        return null;
    }

    /**
     * @param string $fileName
     * @param string $mediaFolderId
     * @param string $fileType e.g. 'png' - without leading dot
     */
    protected function importProductImage(string $fileName, string $mediaFolderId, string $fileType): void
    {
        $productImagePath = sprintf('%s%s.%s', self::ASSET_PATH_PREFIX, $fileName, $fileType);
        $tempFile = tempnam(sys_get_temp_dir(), '');
        copy($productImagePath, $tempFile);

        $fileSize = filesize($tempFile);
        $mediaFile = new MediaFile($tempFile, sprintf('image/%s', $fileType), $fileType, $fileSize);

        $mediaId = Uuid::randomHex();
        $context = Context::createDefaultContext();

        $this->mediaRepository->create([[
            'id' => $mediaId,
            'mediaFolderId' => $mediaFolderId
        ]], $context);

        try {
            $fileName = basename($productImagePath, sprintf('.%s', $fileType));
            $this->fileSaver->persistFileToMedia($mediaFile, $fileName, $mediaId, $context);
        } catch (DuplicatedMediaFileNameException $ex) {
            // this is okay.
        } finally {
            if (file_exists($tempFile)) {
                unlink($tempFile);
            }
        }
    }
}
