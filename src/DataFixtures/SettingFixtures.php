<?php

namespace App\DataFixtures;

use App\Entity\Setting;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class SettingFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $setting = $this->createSetting();

        $manager->persist($setting);
        $manager->flush();
    }

    /**
     * Cette méthode permet de créer les paramètres du site.
     *
     * @return Setting
     */
    private function createSetting(): Setting
    {
        $setting = new Setting();

        $setting->setEmail('medecine-du-monde@gmail.com');
        $setting->setPhone('06 05 05 05 05');
        $setting->setCreatedAt(new DateTimeImmutable());
        $setting->setUpdatedAt(new DateTimeImmutable());

        return $setting;
    }
}
