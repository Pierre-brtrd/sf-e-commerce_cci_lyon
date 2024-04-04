<?php

namespace App\Entity\Traits;

use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

trait DateTimeTrait
{
    #[ORM\Column()]
    private ?DateTimeImmutable $createdAt = null;

    #[ORM\Column(nullable: true)]
    private ?DateTimeImmutable $updatedAt = null;

    /**
     * Get the value of createdAt
     *
     * @return ?DateTimeImmutable
     */
    public function getCreatedAt(): ?DateTimeImmutable
    {
        return $this->createdAt;
    }

    /**
     * Set the value of createdAt
     *
     * @param ?DateTimeImmutable $createdAt
     *
     * @return self
     */
    public function setCreatedAt(?DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get the value of updatedAt
     *
     * @return ?DateTimeImmutable
     */
    public function getUpdatedAt(): ?DateTimeImmutable
    {
        return $this->updatedAt;
    }

    /**
     * Set the value of updatedAt
     *
     * @param ?DateTimeImmutable $updatedAt
     *
     * @return self
     */
    public function setUpdatedAt(?DateTimeImmutable $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    #[ORM\PrePersist]
    public function setAutoCreatedAt(): self
    {
        $this->createdAt = new DateTimeImmutable();

        return $this;
    }

    #[ORM\PreUpdate]
    public function setAutoUpdatedAt(): self
    {
        $this->updatedAt = new DateTimeImmutable();

        return $this;
    }
}
