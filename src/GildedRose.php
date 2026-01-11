<?php

declare(strict_types=1);

namespace GildedRose;

final class GildedRose
{
    /**
     * @param Item[] $items
     */
    public function __construct(
        private array $items
    ) {
    }

    public function updateQuality(): void
    {
        foreach ($this->items as $item) {
            $this->updateItem($item);
        }
    }

    private function updateItem(Item $item): void
    {
        // reconocer la clase de ítem y actualizarlo conforme a sus reglas
        if ($this->isSulfuras($item)) {
            $this->updateSulfuras($item);
        } elseif ($this->isAgedBrie($item)) {
            $this->updateAgedBrie($item);
        } elseif ($this->isBackstagePass($item)) {
            $this->updateBackstagePass($item);
        } elseif ($this->isConjured($item)) {
            $this->updateConjured($item);
        } else {
            $this->updateNormalItem($item);
        }
    }

    // identicadores de tipo

    private function isSulfuras(Item $item): bool
    {
        return $item->name === 'Sulfuras, Hand of Ragnaros';
    }

    private function isAgedBrie(Item $item): bool
    {
        return $item->name === 'Aged Brie';
    }

    private function isBackstagePass(Item $item): bool
    {
        return $item->name === 'Backstage passes to a TAFKAL80ETC concert';
    }

    private function isConjured(Item $item): bool
    {
        return str_starts_with($item->name, 'Conjured');
    }

    // actualizaciones por tipo

    private function updateSulfuras(Item $item): void
    {
        
    }

    private function updateAgedBrie(Item $item): void
    {
        $item->sellIn--;

        // mejora calidad
        $this->increaseQuality($item);

        // Se duplica después de la fecha de venta
        if ($item->sellIn < 0) {
            $this->increaseQuality($item);
        }
    }

    private function updateBackstagePass(Item $item): void
    {
        $item->sellIn--;

        // después del concierto, calidad cae a 0
        if ($item->sellIn < 0) {
            $item->quality = 0;
            return;
        }

        // aumenta calidad base
        $this->increaseQuality($item);

        if ($item->sellIn < 10) {
            $this->increaseQuality($item);
        }

        if ($item->sellIn < 5) {
            $this->increaseQuality($item);
        }
    }

    private function updateConjured(Item $item): void
    {
        $item->sellIn--;

        $this->decreaseQuality($item, 2);

        if ($item->sellIn < 0) {
            $this->decreaseQuality($item, 2);
        }
    }

    private function updateNormalItem(Item $item): void
    {
        $item->sellIn--;

        $this->decreaseQuality($item);

        if ($item->sellIn < 0) {
            $this->decreaseQuality($item);
        }
    }

    // helpers

    private function increaseQuality(Item $item, int $amount = 1): void
    {
        $item->quality = min(50, $item->quality + $amount);
    }

    private function decreaseQuality(Item $item, int $amount = 1): void
    {
        $item->quality = max(0, $item->quality - $amount);
    }
}
