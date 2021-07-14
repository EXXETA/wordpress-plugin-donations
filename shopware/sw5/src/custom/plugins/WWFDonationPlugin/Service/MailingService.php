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