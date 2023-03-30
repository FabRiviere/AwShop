<?php

namespace App\Controller\Admin;

use App\Entity\Carriers;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class CarriersCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Carriers::class;
    }

 
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            TextField::new('name'),
            TextField::new('description'),
            MoneyField::new('price')->setCurrency('USD'),
            ImageField::new('logo')->setBasePath('/assets/uploads/carriers/')
                                    ->setUploadDir('/public/assets/uploads/carriers/')
                                    ->setUploadedFileNamePattern('[randomhash].[extension]')
                                    ->setRequired(false),
        ];
    }
    
}
