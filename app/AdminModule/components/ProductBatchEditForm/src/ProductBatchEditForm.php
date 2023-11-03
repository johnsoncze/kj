<?php

declare(strict_types = 1);

namespace App\AdminModule\Components\ProductBatchEditForm;

use App\Product\AdditionalPhoto\ProductAdditionalPhotoSaveFacade;
use App\Product\AdditionalPhoto\ProductAdditionalPhotoSaveFacadeFactory;
use App\Product\Photo\PhotoManager;
use App\Product\ProductRepository;
use App\ProductParameterGroup\Translation\GroupTranslationTrait;
use Nette\Application\AbortException;
use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Nette\Database\Context as DbContext;

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
final class ProductBatchEditForm extends Control
{
    use GroupTranslationTrait;

    /** @var ProductRepository */
    private ProductRepository $productRepo;

    /** @var PhotoManager */
    protected PhotoManager $productPhotoManager;

    /** @var ProductAdditionalPhotoSaveFacade */
    protected ProductAdditionalPhotoSaveFacade $productAdditionalPhotoSaveFacade;

    /** @var DbContext */
    private DbContext $db;

    /**
     * @param ProductRepository $productRepo
     * @param PhotoManager $productPhotoManager
     * @param DbContext $db
     */
    public function __construct(ProductRepository $productRepo, PhotoManager $productPhotoManager, ProductAdditionalPhotoSaveFacadeFactory $productAdditionalPhotoSaveFacadeFactory, DbContext $db)
    {
        parent::__construct();
        $this->productRepo = $productRepo;
        $this->productPhotoManager = $productPhotoManager;
        $this->productAdditionalPhotoSaveFacade = $productAdditionalPhotoSaveFacadeFactory->create();
        $this->db = $db;
    }

    /**
     * @return Form
     * @throws \Ricaefeliz\Mappero\Exceptions\TranslationMissingException
     */
    public function createComponentForm() : Form
    {
        $form = new Form();
        $productVariantsList = $this->getProductVariantsList();
        $form->addCheckboxList('product_id', 'Varianty produktu *', $productVariantsList)
            ->setAttribute('class', 'space-mr-3')
            ->setRequired('Vyberte nějaké produkty.')
            ->setDefaultValue(array_keys($productVariantsList))
        ;

        $form->addUpload('main_image', 'Vyberte hlavní obrázek (jpg, png)')
            ->setRequired(false)
        ;

        $form->addMultiUpload('additional_images', 'Vyberte doplňkové obrázky (jpg, png)')
            ->setRequired(false)
        ;

        $form->addSubmit('submit', 'Uložit')
            ->setAttribute('class', 'btn btn-success');
        $form->onSuccess[] = [$this, 'formSuccess'];

        return $form;
    }

    /**
     * Handler for success form.
     * @param $form Form
     * @return void
     * @throws AbortException
     */
    public function formSuccess(Form $form)
    {
        $values = $form->getValues();
        $presenter = $this->getPresenter();
        $affectedProducts = $this->productRepo->findByMoreId($values->product_id);

        // save main image
        if($values->main_image->isOk()) {
            foreach ($affectedProducts as $product) {
                $name = $this->productPhotoManager->upload(
                    $product,
                    $values->main_image,
                    true
                );
								
								$product->setPhoto($name);
								$this->productRepo->save($product);
            }
        }

        // save additional images
        if(count($values->additional_images)) {
            foreach ($affectedProducts as $product) {
                $this->productAdditionalPhotoSaveFacade->add(
                    $product,
                    $values->additional_images,
                );
            }
        }

        $this->getPresenter()->flashMessage(
            sprintf(
                'Nahráno: %s%d doplňujících obrázků k %d variantám produktu.',
                $values->main_image ? 'Hlavní obrázek a ' : '',
                count($values->additional_images),
                count($values->product_id),
            )
        );

        $presenter->redirect('this');
    }



    public function render()
    {
        $this->template->setFile(__DIR__ . '/default.latte');
        $this->template->render();
    }

    /**
     * @return array
     * @throws \Ricaefeliz\Mappero\Exceptions\TranslationMissingException
     */
    private function getProductVariantsList(): array
    {
        $currentProductId = $this->presenter->getParameter('id');
        $mainProductId = $this->db->table('product_variant')->where('pv_product_variant_id', $currentProductId)->fetchField('pv_product_id') ?: $currentProductId;
        $productVariants = $this->productRepo->findByMoreId(
            array_merge(
                [$mainProductId => $mainProductId],
                $this->db->table('product_variant')
                    ->where('pv_product_id', $mainProductId)
                    ->fetchPairs('pv_product_variant_id', 'pv_product_variant_id')
            )
        );
        $output = [];
        foreach($productVariants as $productItem) {
            $output[$productItem->getId()] = sprintf(
                '%s - %s (#%d)',
                $productItem->getCode(),
                $productItem->getTranslation()->name,
                $productItem->getId(),
            );
        }
        return $output;
    }

}