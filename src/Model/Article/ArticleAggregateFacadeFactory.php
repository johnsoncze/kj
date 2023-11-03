<?php
/**
 * Created by PhpStorm.
 * User: dusanmlynarcik
 * Date: 02.01.17
 * Time: 23:23
 */
declare(strict_types = 1);


namespace App\Article;

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
interface ArticleAggregateFacadeFactory
{


    /**
     * @return ArticleAggregateFacade
     */
    public function create();

}