<?php declare(strict_types=1);


namespace WWFDonationPlugin\Models;


use Doctrine\ORM\Mapping as ORM;
use Shopware\Components\Model\ModelEntity;

/**
 * Class DonationReportEntity
 *
 * @ORM\Entity
 * @ORM\Table(name="s_wwf_donation_reports")
 *
 * @package WWFDonationPlugin\Entity
 */
class DonationReportEntity extends ModelEntity
{
    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", nullable=false)
     */
    protected $name;

    /**
     * @var int
     *
     * @ORM\Column(name="order_counter", type="integer", nullable=false)
     */
    protected $orderCounter;

    /**
     * @var string
     *
     * @ORM\Column(name="interval_mode", type="string", nullable=false)
     */
    protected $intervalMode;

    /**
     * @var bool
     *
     * @ORM\Column(name="is_regular", type="boolean")
     */
    protected $isRegular;

    /**
     * @var float
     *
     * @ORM\Column(name="total_amount", type="float", nullable=false)
     */
    protected $totalAmount;

    /**
     * @var string
     *
     * @ORM\Column(name="campaign_details", type="text", nullable=false)
     */
    protected $campaignDetails;

    /**
     * @var string
     *
     * @ORM\Column(name="mail_content", type="text", nullable=false)
     */
    protected $mailContent;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="start_date", type="datetimetz", nullable=false)
     */
    protected $startDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="end_date", type="datetimetz", nullable=false)
     */
    protected $endDate;

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return int
     */
    public function getOrderCounter(): int
    {
        return $this->orderCounter;
    }

    /**
     * @param int $orderCounter
     */
    public function setOrderCounter(int $orderCounter): void
    {
        $this->orderCounter = $orderCounter;
    }

    /**
     * @return string
     */
    public function getIntervalMode(): string
    {
        return $this->intervalMode;
    }

    /**
     * @param string $intervalMode
     */
    public function setIntervalMode(string $intervalMode): void
    {
        $this->intervalMode = $intervalMode;
    }

    /**
     * @return bool
     */
    public function isRegular(): bool
    {
        return $this->isRegular;
    }

    /**
     * @param bool $isRegular
     */
    public function setIsRegular(bool $isRegular): void
    {
        $this->isRegular = $isRegular;
    }

    /**
     * @return float
     */
    public function getTotalAmount(): float
    {
        return $this->totalAmount;
    }

    /**
     * @param float $totalAmount
     */
    public function setTotalAmount(float $totalAmount): void
    {
        $this->totalAmount = $totalAmount;
    }

    /**
     * @return string
     */
    public function getCampaignDetails(): string
    {
        return $this->campaignDetails;
    }

    /**
     * @param string $campaignDetails
     */
    public function setCampaignDetails(string $campaignDetails): void
    {
        $this->campaignDetails = $campaignDetails;
    }

    /**
     * @return string
     */
    public function getMailContent(): string
    {
        return $this->mailContent;
    }

    /**
     * @param string $mailContent
     */
    public function setMailContent(string $mailContent): void
    {
        $this->mailContent = $mailContent;
    }

    /**
     * @return \DateTime
     */
    public function getStartDate(): \DateTime
    {
        return $this->startDate;
    }

    /**
     * @param \DateTime $startDate
     */
    public function setStartDate(\DateTime $startDate): void
    {
        $this->startDate = $startDate;
    }

    /**
     * @return \DateTime
     */
    public function getEndDate(): \DateTime
    {
        return $this->endDate;
    }

    /**
     * @param \DateTime $endDate
     */
    public function setEndDate(\DateTime $endDate): void
    {
        $this->endDate = $endDate;
    }
}