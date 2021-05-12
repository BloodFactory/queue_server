<?php

namespace App\Service\Dictionary;

use Doctrine\ORM\EntityManagerInterface;
use Psr\Cache\InvalidArgumentException;
use Symfony\Component\Cache\Adapter\AdapterInterface;

class Service
{
    private const CACHE_KEY = 'dictionary.services';

    private AdapterInterface $cache;
    private EntityManagerInterface $em;

    public function __construct(AdapterInterface $cache, EntityManagerInterface $em)
    {
        $this->cache = $cache;
        $this->em = $em;
    }

    /**
     * @return array
     * @throws InvalidArgumentException
     */
    public function fetch(): array
    {
        $item = $this->cache->getItem(self::CACHE_KEY);

        $result = [];

        if (!$item->isHit()) {
            $services = $this->em->getRepository(\App\Entity\Service::class)->findAll();

            foreach ($services as $service) {
                $result[] = [
                    'value' => $service->getId(),
                    'label' => $service->getName()
                ];
            }

            $item->set($result);
            $this->cache->save($item);
        } else {
            $result = $item->get();
        }

        return $result;
    }

    /**
     * @throws InvalidArgumentException
     */
    public function clear(): void
    {
        $this->cache->deleteItem(self::CACHE_KEY);
    }
}
