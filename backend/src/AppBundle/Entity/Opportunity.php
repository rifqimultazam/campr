<?php

namespace AppBundle\Entity;

use Component\Opportunity\Model\OpportunityPriorityTrait;
use Component\Resource\Cloner\CloneableInterface;
use Component\Resource\Model\BlameableInterface;
use Component\Resource\Model\BlameableTrait;
use Component\Resource\Model\PriorityAwareInterface;
use Component\Project\ProjectAwareInterface;
use Component\Project\ProjectInterface;
use Component\Resource\Model\ResourceInterface;
use Component\Resource\Model\ResponsibilityAwareInterface;
use Component\Resource\Model\TimestampableInterface;
use Component\Resource\Model\TimestampableTrait;
use Component\TimeUnit\TimeUnitAwareInterface;
use Component\TimeUnit\TimeUnitsConvertor;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Opportunity.
 *
 * @ORM\Table(name="opportunity")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\OpportunityRepository")
 */
class Opportunity implements TimeUnitAwareInterface, ProjectAwareInterface, PriorityAwareInterface, TimestampableInterface, BlameableInterface, ResponsibilityAwareInterface, ResourceInterface, CloneableInterface
{
    use OpportunityPriorityTrait, TimestampableTrait, BlameableTrait;

    const PRIORITY_VERY_LOW = 0;
    const PRIORITY_LOW = 1;
    const PRIORITY_MEDIUM = 2;
    const PRIORITY_HIGH = 3;
    const PRIORITY_VERY_HIGH = 4;

    const THRESHOLD_VERY_LOW = [0, 25];
    const THRESHOLD_LOW = [25, 50];
    const THRESHOLD_HIGH = [50, 75];
    const THRESHOLD_VERY_HIGH = [75, 101];

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var Project
     *
     * @Serializer\Exclude()
     *
     * @ORM\ManyToOne(targetEntity="Project", inversedBy="opportunities")
     * @ORM\JoinColumn(name="project_id", onDelete="CASCADE")
     */
    private $project;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text")
     */
    private $description;

    /**
     * @var string
     *
     * @ORM\Column(name="cost_savings", type="decimal", precision=9, scale=2, nullable=true)
     */
    private $costSavings;

    /**
     * @var string
     *
     * @ORM\Column(name="time_savings", type="decimal", precision=9, scale=2, nullable=true)
     */
    private $timeSavings;

    /**
     * @var string
     *
     * @ORM\Column(name="time_unit", type="string", length=255)
     */
    private $timeUnit;

    /**
     * @var OpportunityStrategy|null
     *
     * @Serializer\Exclude()
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\OpportunityStrategy")
     * @ORM\JoinColumn(name="opportunity_strategy_id", onDelete="SET NULL")
     */
    private $opportunityStrategy;

    /**
     * @var ArrayCollection|Measure[]
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Measure", mappedBy="opportunity", cascade={"all"})
     */
    private $measures;

    /**
     * @var User|null
     *
     * @Serializer\Exclude()
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User")
     * @ORM\JoinColumn(name="user_id", onDelete="SET NULL")
     */
    private $responsibility;

    /**
     * @var User|null
     *
     * @Serializer\Exclude()
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User")
     * @ORM\JoinColumn(name="created_by", onDelete="SET NULL")
     */
    protected $createdBy;

    /**
     * @var \DateTime|null
     *
     * @Serializer\Type("DateTime<'Y-m-d H:i:s'>")
     *
     * @ORM\Column(name="due_date", type="date", nullable=true)
     */
    private $dueDate;

    /**
     * @var OpportunityStatus|null
     *
     * @Serializer\Exclude()
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\OpportunityStatus")
     * @ORM\JoinColumn(name="opportunity_status_id", onDelete="CASCADE")
     */
    private $opportunityStatus;

    /**
     * @var \DateTime
     *
     * @Serializer\Type("DateTime<'Y-m-d H:i:s'>")
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=false)
     */
    protected $createdAt;

    /**
     * @var \DateTime|null
     *
     * @Serializer\Type("DateTime<'Y-m-d H:i:s'>")
     * @Gedmo\Timestampable(on="update")
     *
     * @ORM\Column(name="updated_at", type="datetime", nullable=true)
     */
    protected $updatedAt;

    /**
     * Risk constructor.
     */
    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->measures = new ArrayCollection();
    }

    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set project.
     *
     * @param ProjectInterface $project
     *
     * @return Opportunity
     */
    public function setProject(ProjectInterface $project = null)
    {
        $this->project = $project;

        return $this;
    }

    /**
     * Get project.
     *
     * @return ProjectInterface
     */
    public function getProject()
    {
        return $this->project;
    }

    /**
     * Returns project name.
     *
     * @Serializer\VirtualProperty()
     * @Serializer\SerializedName("project")
     *
     * @return string
     */
    public function getProjectId()
    {
        return $this->project ? $this->project->getId() : null;
    }

    /**
     * Returns project name.
     *
     * @Serializer\VirtualProperty()
     * @Serializer\SerializedName("projectName")
     *
     * @return string
     */
    public function getProjectName()
    {
        return $this->project ? $this->project->getName() : null;
    }

    /**
     * Set title.
     *
     * @param string $title
     *
     * @return Opportunity
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title.
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set description.
     *
     * @param string $description
     *
     * @return Opportunity
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description.
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set costSavings.
     *
     * @param string $costSavings
     *
     * @return Opportunity
     */
    public function setCostSavings($costSavings)
    {
        $this->costSavings = $costSavings;

        return $this;
    }

    /**
     * Get costSavings.
     *
     * @return string
     */
    public function getCostSavings()
    {
        return $this->costSavings;
    }

    /**
     * Set timeSavings.
     *
     * @param string $timeSavings
     *
     * @return Opportunity
     */
    public function setTimeSavings($timeSavings)
    {
        $this->timeSavings = $timeSavings;

        return $this;
    }

    /**
     * Get timeSavings.
     *
     * @return string
     */
    public function getTimeSavings()
    {
        return $this->timeSavings;
    }

    /**
     * Set dueDate.
     *
     * @param \DateTime $dueDate
     *
     * @return Opportunity
     */
    public function setDueDate(\DateTime $dueDate = null)
    {
        $this->dueDate = $dueDate;

        return $this;
    }

    /**
     * Get dueDate.
     *
     * @return \DateTime
     */
    public function getDueDate()
    {
        return $this->dueDate;
    }

    /**
     * Set opportunityStrategy.
     *
     * @param OpportunityStrategy $opportunityStrategy
     *
     * @return Risk
     */
    public function setOpportunityStrategy(OpportunityStrategy $opportunityStrategy = null)
    {
        $this->opportunityStrategy = $opportunityStrategy;

        return $this;
    }

    /**
     * Get opportunityStrategy.
     *
     * @return OpportunityStrategy
     */
    public function getOpportunityStrategy()
    {
        return $this->opportunityStrategy;
    }

    /**
     * Returns opportunityStrategy id.
     *
     * @Serializer\VirtualProperty()
     * @Serializer\SerializedName("opportunityStrategy")
     *
     * @return string
     */
    public function getOpportunityStrategyId()
    {
        return $this->opportunityStrategy ? $this->opportunityStrategy->getId() : null;
    }

    /**
     * Returns opportunityStrategy name.
     *
     * @Serializer\VirtualProperty()
     * @Serializer\SerializedName("opportunityStrategyName")
     *
     * @return string
     */
    public function getOpportunityStrategyName()
    {
        return $this->opportunityStrategy ? $this->opportunityStrategy->getName() : null;
    }

    /**
     * Set responsibility.
     *
     * @param User $responsibility
     *
     * @return Opportunity
     */
    public function setResponsibility(User $responsibility = null)
    {
        $this->responsibility = $responsibility;

        return $this;
    }

    /**
     * Get responsibility.
     *
     * @return User
     */
    public function getResponsibility()
    {
        return $this->responsibility;
    }

    /**
     * Returns responsibility id.
     *
     * @Serializer\VirtualProperty()
     * @Serializer\SerializedName("responsibility")
     *
     * @return string
     */
    public function getResponsibilityId()
    {
        return $this->responsibility ? $this->responsibility->getId() : null;
    }

    /**
     * Returns responsibility full name.
     *
     * @Serializer\VirtualProperty()
     * @Serializer\SerializedName("responsibilityFullName")
     *
     * @return string
     */
    public function getResponsibilityFullName()
    {
        return $this->responsibility ? $this->responsibility->getFullName() : null;
    }

    /**
     * Set opportunityStatus.
     *
     * @param OpportunityStatus $opportunityStatus
     *
     * @return Opportunity
     */
    public function setOpportunityStatus(OpportunityStatus $opportunityStatus = null)
    {
        $this->opportunityStatus = $opportunityStatus;

        return $this;
    }

    /**
     * Get opportunityStatus.
     *
     * @return OpportunityStatus
     */
    public function getOpportunityStatus()
    {
        return $this->opportunityStatus;
    }

    /**
     * Returns opportunityStatus id.
     *
     * @Serializer\VirtualProperty()
     * @Serializer\SerializedName("opportunityStatus")
     *
     * @return string
     */
    public function getOpportunityStatusId()
    {
        return $this->opportunityStatus ? $this->opportunityStatus->getId() : null;
    }

    /**
     * Returns opportunityStatus name.
     *
     * @Serializer\VirtualProperty()
     * @Serializer\SerializedName("opportunityStatusName")
     *
     * @return string
     */
    public function getOpportunityStatusName()
    {
        return $this->opportunityStatus ? $this->opportunityStatus->getName() : null;
    }

    /**
     * @param Measure $measure
     *
     * @return Opportunity
     */
    public function addMeasure(Measure $measure)
    {
        $measure->setOpportunity($this);
        $this->measures[] = $measure;

        return $this;
    }

    /**
     * @param Measure $measure
     *
     * @return Opportunity
     */
    public function removeMeasure(Measure $measure)
    {
        $this->measures->removeElement($measure);

        return $this;
    }

    /**
     * @return Measure[]|ArrayCollection
     */
    public function getMeasures()
    {
        return $this->measures;
    }

    /**
     * @Serializer\VirtualProperty()
     *
     * @return float
     */
    public function getMeasuresTotalCost()
    {
        $cost = 0;
        foreach ($this->getMeasures() as $measure) {
            $cost += $measure->getCost();
        }

        return round($cost, 2);
    }

    /**
     * @return string
     */
    public function getTimeUnit(): string
    {
        return (string) $this->timeUnit;
    }

    /**
     * @param string $timeUnit
     *
     * @return $this
     */
    public function setTimeUnit(string $timeUnit = null)
    {
        $this->timeUnit = $timeUnit;

        return $this;
    }

    /**
     * Returns createdBy id.
     *
     * @Serializer\VirtualProperty()
     * @Serializer\SerializedName("createdBy")
     *
     * @return string
     */
    public function getCreatedById()
    {
        return $this->createdBy ? $this->createdBy->getId() : null;
    }

    /**
     * Returns createdBy full name.
     *
     * @Serializer\VirtualProperty()
     * @Serializer\SerializedName("createdByFullName")
     *
     * @return string
     */
    public function getCreatedByFullName()
    {
        return $this->createdBy ? $this->createdBy->getFullName() : null;
    }

    /**
     * @Serializer\VirtualProperty()
     *
     * @return float
     */
    public function getPotentialCostSavings(): float
    {
        return round(($this->getProbability() / 100) * $this->getCostSavings(), 4);
    }

    /**
     * @Serializer\VirtualProperty()
     *
     * @return float
     */
    public function getPotentialTimeSavings(): float
    {
        return round(($this->getProbability() / 100) * $this->getTimeSavings(), 4);
    }

    /**
     * @Serializer\VirtualProperty()
     *
     * @return float
     */
    public function getPotentialTimeSavingsHours(): float
    {
        $amount = $this->getPotentialTimeSavings();
        if (empty($amount)) {
            return 0;
        }

        $convertor = new TimeUnitsConvertor($this);

        return round($convertor->toHours($amount), 2);
    }
}
