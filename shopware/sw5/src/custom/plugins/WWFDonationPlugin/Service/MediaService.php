<?php


namespace WWFDonationPlugin\Service;


use Doctrine\ORM\EntityRepository;
use exxeta\wwf\banner\AbstractCharityProductManager;
use exxeta\wwf\banner\model\CharityProduct;
use Shopware\Components\Model\ModelManager;
use Shopware\Models\Media\Album;
use Shopware\Models\Media\Media;
use Shopware\Models\Media\Settings;
use Symfony\Component\Filesystem\Exception\FileNotFoundException;
use Symfony\Component\HttpFoundation\File\File;

/**
 * Class MediaService
 *
 * This class handles the integration of media and asset files into a shopware 5 shop
 *
 * @package WWFDonationPlugin\Service
 */
class MediaService
{
    const MEDIA_PREFIX = 'media/image/';
    const ASSET_PATH_PREFIX = __DIR__ . '/../Resources/public/static/';
    const WWF_MEDIA_ALBUM_DIRECTORY = 'WWF-Media';

    /**
     * @var AbstractCharityProductManager
     */
    protected $charityProductManager;

    /**
     * @var \Shopware\Bundle\MediaBundle\MediaService
     */
    protected $mediaService;

    /**
     * @var ModelManager
     */
    protected $entityManager;

    /**
     * @var EntityRepository
     */
    protected $mediaAlbumRepository;

    /**
     * @var EntityRepository
     */
    protected $mediaRepository;

    /**
     * MediaService constructor.
     *
     * @param AbstractCharityProductManager $productManager
     * @param ModelManager $entityManager
     * @param \Shopware\Bundle\MediaBundle\MediaService $mediaService
     */
    public function __construct(AbstractCharityProductManager $productManager,
                                ModelManager $entityManager,
                                \Shopware\Bundle\MediaBundle\MediaService $mediaService)
    {
        $this->charityProductManager = $productManager;
        $this->mediaService = $mediaService;
        $this->entityManager = $entityManager;

        $this->mediaAlbumRepository = $entityManager->getRepository(Album::class);
        $this->mediaRepository = $entityManager->getRepository(Media::class);
    }

    public function getAbsoluteUrlByMediaRecord(Media $media): string
    {
        return $this->mediaService->getUrl($media->getPath());
    }

    /**
     * @param string $internalMediaPath
     * @return bool
     */
    public function doesMediaRecordExist(string $internalMediaPath): bool
    {
        return $this->getMediaRecordByInternalPath($internalMediaPath) instanceof Media;
    }

    /**
     * @param string $internalPath
     * @return Media|null
     */
    public function getMediaRecordByInternalPath(string $internalPath): ?Media
    {
        $mediaRecord = $this->mediaRepository->findOneBy([
            'path' => $internalPath,
        ]);
        if ($mediaRecord instanceof Media) {
            return $mediaRecord;
        }
        return null;
    }

    /**
     * @param CharityProduct $charityProduct
     * @param Album $album
     * @return Media|null
     */
    public function getMediaRecordByCharityProduct(CharityProduct $charityProduct, Album $album): ?Media
    {
        $imagePath = $charityProduct->getImagePath();
        $imagePath = basename($imagePath, '.png');

        $queryBuilder = $this->entityManager->createQueryBuilder();
        $queryBuilder->select('m.id')->from(Media::class, 'm')
            ->where('m.name LIKE :name')
            ->andWhere('m.albumId = :alb')
            ->setMaxResults(1)
            ->setParameter(':name', $imagePath . '%')
            ->setParameter(':alb', $album->getId());

        $arrayResult = $queryBuilder->getQuery()->getArrayResult();
        if (count($arrayResult) == 0 || !isset($arrayResult[0])) {
            // TODO log this case?
            return null;
        }
        $mediaRecord = $this->mediaRepository->findOneBy($arrayResult[0]);
        if ($mediaRecord instanceof Media) {
            return $mediaRecord;
        }
        return null;
    }

    /**
     * import product coin and banner images
     * @param Album $mediaAlbumRecord
     * @throws \Doctrine\ORM\ORMException
     */
    protected function importImageAssets(Album $mediaAlbumRecord): void
    {
        $charityProducts = $this->charityProductManager->getAllProducts();
        // import coin images
        foreach ($charityProducts as $charityProduct) {
            $filePath = $this->getInternalMediaPathByProduct($charityProduct);

            $this->importProductImage($filePath, $charityProduct->getImagePath(), $mediaAlbumRecord);
        }
        // import campaign banner images
//        foreach ($this->charityProductManager->getAllCampaignBannerFileNames() as $bannerImageFileName) {
//            $filePath = $this->getInternalMediaPath($bannerImageFileName);
//
//            $this->importProductImage($filePath, $bannerImageFileName, $mediaAlbumRecord);
//        }
        // import icon assets
//        foreach ($this->charityProductManager->getIconAssetFileNames() as $assetFileName) {
//            $filePath = $this->getInternalMediaPath($assetFileName);
//
//            $this->importProductImage($filePath, $assetFileName, $mediaAlbumRecord);
//        }
    }

    /**
     * @param CharityProduct $charityProduct
     * @return string|null
     */
    public function getInternalMediaPathByProduct(CharityProduct $charityProduct): ?string
    {
        return $this->getInternalMediaPath($charityProduct->getImagePath());
    }

    /**
     * @param string $fileName
     * @return string|null
     */
    public function getInternalMediaPath(string $fileName): ?string
    {
        return sprintf('%s%s', self::MEDIA_PREFIX, $fileName);
    }

    /**
     * @return Album
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function getOrCreateMediaAlbum(): Album
    {
        $possibleMediaAlbumRecord = $this->mediaAlbumRepository->findOneBy([
            'name' => self::WWF_MEDIA_ALBUM_DIRECTORY,
        ]);
        if (!$possibleMediaAlbumRecord instanceof Album) {
            $albumRecord = new Album();
            $albumRecord->setName(self::WWF_MEDIA_ALBUM_DIRECTORY);
            $albumRecord->setPosition(10);
            $albumRecord->setGarbageCollectable(1);

            $albumSettingsRecord = new Settings();
            $albumSettingsRecord->setAlbum($albumRecord);
            $albumSettingsRecord->setCreateThumbnails(1);
            $albumSettingsRecord->setIcon('sprite-globe-green');
            $albumSettingsRecord->setThumbnailHighDpiQuality(70);
            $albumSettingsRecord->setThumbnailHighDpi(1);
            $albumSettingsRecord->setThumbnailQuality(95);
            $albumSettingsRecord->setThumbnailSize('120x120;200x200;400x400');

            $albumRecord->setSettings($albumSettingsRecord);

            $this->entityManager->persist($albumRecord);
            $this->entityManager->persist($albumSettingsRecord);

            $this->entityManager->flush();
            return $albumRecord;
        }
        return $possibleMediaAlbumRecord;
    }


    /**
     * @param string $internalSwMediaPath
     * @param string $fileName
     * @param Album $mediaAlbumRecord
     * @throws \Doctrine\ORM\ORMException
     */
    public function importProductImage(string $internalSwMediaPath, string $fileName, Album $mediaAlbumRecord): ?Media
    {
        $productImagePath = sprintf('%s%s', self::ASSET_PATH_PREFIX, $fileName);
        $tempFile = tempnam(sys_get_temp_dir(), '');
        copy($productImagePath, $tempFile);

        if (!file_exists($productImagePath)) {
            throw new FileNotFoundException(
                sprintf('Path "%s" does not exist', $productImagePath)
            );
        }
        try {
            if (!$this->mediaService->has($internalSwMediaPath)) {
                $this->mediaService->write($internalSwMediaPath, file_get_contents($tempFile));
            }

            if (!str_starts_with($fileName, 'icon_') && !$this->doesMediaRecordExist($internalSwMediaPath)) {
                // ignore svg icons and don't add them to the media library
                // create new media record
                $mediaRecord = new Media();

                $file = new File($productImagePath, true);
                $mediaRecord->setFile($file);

                $mediaRecord->setCreated(new \DateTime());
                $mediaRecord->setDescription('');
                $mediaRecord->setName(basename($fileName));
                $mediaRecord->setAlbum($mediaAlbumRecord);
                $mediaRecord->setUserId(0); // = system user

                $this->entityManager->persist($mediaRecord);
                $this->entityManager->flush();
                return $mediaRecord;
            }
        } finally {
            if (file_exists($tempFile)) {
                unlink($tempFile);
            }
        }
        return null;
    }

}
