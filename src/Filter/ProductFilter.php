<?php

namespace App\Filter;

class ProductFilter
{
    public function __construct(
        private int $page = 1,
        private ?string $query = null,
        private ?int $min = null,
        private ?int $max = null,
        private ?string $sort = null,
        private string $order = 'ASC',
        private array $tags = [],
    ) {
    }

    /**
     * Get the value of page
     *
     * @return int
     */
    public function getPage(): int
    {
        return $this->page;
    }

    /**
     * Set the value of page
     *
     * @param int $page
     *
     * @return self
     */
    public function setPage(int $page): self
    {
        $this->page = $page;

        return $this;
    }

    /**
     * Get the value of query
     *
     * @return ?string
     */
    public function getQuery(): ?string
    {
        return $this->query;
    }

    /**
     * Set the value of query
     *
     * @param ?string $query
     *
     * @return self
     */
    public function setQuery(?string $query): self
    {
        $this->query = $query;

        return $this;
    }

    /**
     * Get the value of min
     *
     * @return ?int
     */
    public function getMin(): ?int
    {
        return $this->min;
    }

    /**
     * Set the value of min
     *
     * @param ?int $min
     *
     * @return self
     */
    public function setMin(?int $min): self
    {
        $this->min = $min;

        return $this;
    }

    /**
     * Get the value of max
     *
     * @return ?int
     */
    public function getMax(): ?int
    {
        return $this->max;
    }

    /**
     * Set the value of max
     *
     * @param ?int $max
     *
     * @return self
     */
    public function setMax(?int $max): self
    {
        $this->max = $max;

        return $this;
    }

    /**
     * Get the value of sort
     *
     * @return ?string
     */
    public function getSort(): ?string
    {
        return $this->sort;
    }

    /**
     * Set the value of sort
     *
     * @param ?string $sort
     *
     * @return self
     */
    public function setSort(?string $sort): self
    {
        $this->sort = $sort;

        return $this;
    }

    /**
     * Get the value of order
     *
     * @return string
     */
    public function getOrder(): string
    {
        return $this->order;
    }

    /**
     * Set the value of order
     *
     * @param string $order
     *
     * @return self
     */
    public function setOrder(string $order): self
    {
        $this->order = $order;

        return $this;
    }

    /**
     * Get the value of tags
     *
     * @return array
     */
    public function getTags(): array
    {
        return $this->tags;
    }

    /**
     * Set the value of tags
     *
     * @param array $tags
     *
     * @return self
     */
    public function setTags(array $tags): self
    {
        $this->tags = $tags;

        return $this;
    }
}
