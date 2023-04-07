<?php

namespace Nando118\StudiKasus\PHP\LoginManagement\Controller;

class ProductController
{

    function categories(string $productId, string $categoryId): void
    {
        echo "PRODUCT $productId, CATEGORY $categoryId";
    }

}