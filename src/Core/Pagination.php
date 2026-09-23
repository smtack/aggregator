<?php

namespace Core;

class Pagination
{
    private int $page;
    private int $limit;
    private int $total = 0;

    public function __construct(int $page = 1, int $limit = 25)
    {
        $this->page = max(1, $page);
        $this->limit = $limit;
    }

    public function page(): int
    {
        return $this->page;
    }

    public function limit(): int
    {
        return $this->limit;
    }

    public function offset(): int
    {
        return ($this->page - 1) * $this->limit;
    }

    public function setTotal(int $total): void
    {
        $this->total = max(0, $total);
    }

    public function total(): int
    {
        return $this->total;
    }

    public function pages(): int
    {
        if ($this->total === 0) {
            return 0;
        }

        return (int)ceil($this->total / $this->limit);
    }

    public function hasNext(): bool
    {
        return $this->page < $this->pages();
    }

    public function hasPrevious(): bool
    {
        return $this->page > 1;
    }

    public function previous(): int
    {
        return max(1, $this->page - 1);
    }

    public function next(): int
    {
        return min($this->pages(), $this->page + 1);
    }
}