<?php


namespace WWFDonationPlugin\Service;


use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Shopware\Models\Mail\Mail;

class MailingService
{
    public const WWF_REPORT_MAIL_KEY = 'wwf_report_mail';
    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @var EntityRepository
     */
    protected $mailTemplateRepository;

    /**
     * MailingService constructor.
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->mailTemplateRepository = $entityManager->getRepository(Mail::class);
    }


    public function install()
    {
        $mailTemplate = $this->mailTemplateRepository->findOneBy(['name' => self::WWF_REPORT_MAIL_KEY]);
        if (!$mailTemplate instanceof Mail) {
            $mailTemplate = new Mail();
        }
        $mailTemplate->setIsHtml(true);
        $mailTemplate->setName(self::WWF_REPORT_MAIL_KEY);
        $mailTemplate->setFromMail('{config name=mail}');
        $mailTemplate->setFromName('{config name=shopName}');
        $mailTemplate->setSubject('Spendenbericht Nr. X');
        $mailTemplate->setContentHtml('<body></body>');
        $mailTemplate->setMailtype(Mail::MAILTYPE_SYSTEM);

        $this->entityManager->persist($mailTemplate);
        $this->entityManager->flush();
    }

    public function uninstall()
    {
        $mailTemplate = $this->mailTemplateRepository->findOneBy(['name' => self::WWF_REPORT_MAIL_KEY]);
        $this->entityManager->remove($mailTemplate);
        $this->entityManager->flush();
    }
}