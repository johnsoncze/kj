<?php

declare(strict_types = 1);

namespace App\AdminModule\Components\Category\SortForm\Resolver;

use App\Language\LanguageEntity;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class ResolverList
{


    /** @var IResolver[] */
    protected $resolvers;



    public function __construct(CollectionSliderResolver $categorySliderResolver,
                                ChildResolver $childResolver,
                                FirstLevelResolver $firstLevelResolver,
                                HomepageListResolver $homepageListResolver)
    {
        $this->resolvers[] = $categorySliderResolver;
        $this->resolvers[] = $childResolver;
        $this->resolvers[] = $firstLevelResolver;
        $this->resolvers[] = $homepageListResolver;
    }



    /**
     * @param $key mixed
     * @param $sorting array
     * @return void
     */
    public function save($key, array $sorting)
    {
        foreach ($this->resolvers as $resolver) {
            if ($resolver->match($key) === TRUE) {
                $resolver->save($sorting);
            }
        }
    }



    /**
     * @param $key mixed
     * @param $language LanguageEntity
     * @return array
     */
    public function findItems($key, LanguageEntity $language) : array
    {
        foreach ($this->resolvers as $resolver) {
            if ($resolver->match($key) === TRUE) {
                return $resolver->findItems($language, $key);
            }
        }
        return [];
    }
}