<?php

use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Config\Loader\LoaderInterface;

class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = array(
            new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new Symfony\Bundle\SecurityBundle\SecurityBundle(),
            new Symfony\Bundle\TwigBundle\TwigBundle(),
            new Symfony\Bundle\MonologBundle\MonologBundle(),
            new Symfony\Bundle\SwiftmailerBundle\SwiftmailerBundle(),
            new Symfony\Bundle\AsseticBundle\AsseticBundle(),
            new Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
            new Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle(),
			new FOS\UserBundle\FOSUserBundle(),
            new Main\UserBundle\MainUserBundle(),
            new Main\HomeBundle\MainHomeBundle(),
			new Sonata\CoreBundle\SonataCoreBundle(),
			new Sonata\BlockBundle\SonataBlockBundle(),
			new Knp\Bundle\MenuBundle\KnpMenuBundle(),
			new Sonata\DoctrineORMAdminBundle\SonataDoctrineORMAdminBundle(),
			new Sonata\AdminBundle\SonataAdminBundle(),
            new Main\ExerciceBundle\MainExerciceBundle(),
            new Main\EpreuveBundle\MainEpreuveBundle(),
            new Main\SavoirBundle\MainSavoirBundle(),
            new Main\ThemeBundle\MainThemeBundle(),
            new Main\MatiereBundle\MainMatiereBundle(),
			new Liuggio\ExcelBundle\LiuggioExcelBundle(),
            new Main\EvaluationBundle\MainEvaluationBundle(),
            new Main\CoursBundle\MainCoursBundle(),
            new Main\BadgeBundle\MainBadgeBundle(),
			new Knp\Bundle\TimeBundle\KnpTimeBundle(),
			new HWI\Bundle\OAuthBundle\HWIOAuthBundle(),
            new Main\ClasseBundle\MainClasseBundle(),
            new Main\BlogBundle\MainBlogBundle(),
        );

        if (in_array($this->getEnvironment(), array('dev', 'test'))) {
            $bundles[] = new Symfony\Bundle\WebProfilerBundle\WebProfilerBundle();
            $bundles[] = new Sensio\Bundle\DistributionBundle\SensioDistributionBundle();
            $bundles[] = new Sensio\Bundle\GeneratorBundle\SensioGeneratorBundle();
        }

        return $bundles;
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
		// if ($_SERVER['SERVER_NAME'] == "papilo.ch" || $_SERVER['SERVER_NAME'] == "www.papilo.ch") // due to redirection, we don't seem to have those set for papilo.ch
			// $loader->load(__DIR__.'/config/config_suisse.yml');
		// else
			$loader->load(__DIR__.'/config/config_'.$this->getEnvironment().'.yml');
    }
}
