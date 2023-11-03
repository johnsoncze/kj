<?php

namespace App\PromoArticle;


use App\Extensions\Grido\IRepositorySource;
use App\IRepository;
use App\NotFoundException;
use App\Url\IUrlRepository;
use Ricaefeliz\Mappero\Entities\IEntity;
use Ricaefeliz\Mappero\Repositories\BaseRepository;
use Ricaefeliz\Mappero\ResultObjects\CountDTO;



class PromoArticleRepository extends BaseRepository implements IRepositorySource, IRepository
{


    /** @var string */
    protected $entityName = PromoArticleEntity::class;



    /**
     * @param $id int
     * @return PromoArticleEntity
     * @throws NotFoundException
     */
    public function getOneById(int $id)
    {
        $filters['where'][] = ['id', '=', $id];
        $result = $this->findOneBy($filters);
        if (!$result) {
            throw new NotFoundException("Promo článek nebyl nalezen.");
        }
        return $result;
    }
		
		
    /**
     * @return array
     */
    public function getDefault()
    {
				$filters['sort'] = ['sequence', 'ASC'];
				$filters['where'][] = ['isDefault', '=', 1];				
        return $this->findBy($filters) ?: [];
    }		

		
}
