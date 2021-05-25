<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

class User implements UserInterface
{
    use AuthenticatableTrait;
    use ResourceTrait;
    use NameableTrait;
    use TimestampableTrait;
    use RoleAwareTrait;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private ?string $email = null;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private ?string $internalId = null;

    /**
     * @ORM\Column(type="boolean")
     */
    private bool $inClub = false;

    /**
     * @ORM\Column(type="boolean")
     */
    private bool $activated = false;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private ?string $activateToken = null;

    /**
     * @ORM\Column(type="boolean")
     */
    private bool $allowEmails = true;

    /**
     * @ORM\Column(type="boolean")
     */
    private bool $notificationsDisabled = false;

    /**
     * @ORM\Column(type="boolean")
     */
    private bool $stickRole = false;

    /**
     * @ORM\ManyToOne(targetEntity=App\Entity\Folder)
     * @ORM\JoinColumn(name=projects_design_group_id, nullable=true)
     */
    private ?FolderInterface $projectFolder = null;

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function getInternalId(): ?string
    {
        return $this->internalId;
    }

    public function setInternalId(?string $internalId): void
    {
        $this->internalId = $internalId;
    }

    public function isInClub(): bool
    {
        return $this->inClub;
    }

    public function setInClub(bool $inClub): void
    {
        $this->inClub = $inClub;
    }

    public function isActivated(): bool
    {
        return $this->activated;
    }

    public function setActivated(bool $activated): void
    {
        $this->activated = $activated;
    }

    public function getActivateToken(): ?string
    {
        return $this->activateToken;
    }

    public function setActivateToken(?string $activateToken): void
    {
        $this->activateToken = $activateToken;
    }

    public function isAllowEmails(): bool
    {
        return $this->allowEmails;
    }

    public function setAllowEmails(bool $allowEmails): void
    {
        $this->allowEmails = $allowEmails;
    }

    public function isNotificationsDisabled(): bool
    {
        return $this->notificationsDisabled;
    }

    public function setNotificationsDisabled(bool $notificationsDisabled): void
    {
        $this->notificationsDisabled = $notificationsDisabled;
    }

    public function isStickRole(): bool
    {
        return $this->stickRole;
    }

    public function setStickRole(bool $stickRole): void
    {
        $this->stickRole = $stickRole;
    }

    public function getProjectFolder(): ?FolderInterface
    {
        return $this->projectFolder;
    }

    public function setProjectFolder(?FolderInterface $projectFolder): void
    {
        $this->projectFolder = $projectFolder;
    }

    public function setEmail(?string $email): void
    {
        $this->email = $email;
    }
}